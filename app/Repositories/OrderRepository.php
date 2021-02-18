<?php

namespace App\Repositories;

use App\Attendant;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Company;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Table;
use MercadoPago;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Validator;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    /**
     * OrderRepository constructor.
     *
     * @param Order $model
     */
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * @param $companyLat
     * @param $companyLng
     * @param $userLat
     * @param $userLng
     * @return float
     */
    public static function calculateDistance($companyLat, $companyLng, $userLat, $userLng): float
    {
        return 111.045
            * rad2deg(acos(min(1.0, cos(deg2rad($userLat))
            * cos(deg2rad($companyLat))
            * cos(deg2rad($userLng) - deg2rad($companyLng))
            + sin(deg2rad($userLat))
            * sin(deg2rad($companyLat)))));
    }

    /**
     * @param mixed $company_id
     * @return Collection
     */
    public function getByCompany($company_id): Collection
    {
        $company = Company::slug($company_id);

        return Order::where('company_id', $company->id)->get();
    }

    /**
     * @param array $attributes
     * @return Order
     */
    public function createByCompany(array $attributes): Order
    {
        $this->validateCreateByCompany($attributes);

        $company = Company::find($attributes['company_id']);

        $order = new Order(Arr::only($attributes, [
            'company_id',
            'table_id',
            'attendant_id',
            'products',
            'type'
        ]));

        $order->total_price = 0;

        if ($order->type == Order::TYPE_DELIVERY) {

            $order->payment_type = $attributes['payment_type'];

            $order->delivery_location = json_encode(['address' => $attributes['address']]);

            $order->additional_information = $attributes['additional_information'];

            $order->delivery_price = $company->delivery_price;

            $order->total_price += $order->delivery_price;

        }

        else {

            $order->payment_type =  Order::PAYMENT_LOCAL;
            
        }

        $order->products = $this->serializeProduct($attributes['products']);

        $order->price = $this->calculatePrice($attributes['products']);

        $order->total_price += $order->price;

        $order->save();

        return $order;
    }

    /**
     * @param array $attributes
     * @return Order
     */
    public function createByUser(array $attributes): Order
    {
        $this->validateCreateByUser($attributes);

        $user = Auth::user();

        $company = Company::find($attributes['company_id']);

        $order = new Order();

        $order->total_price = $order->price = $this->calculatePrice($attributes['products']);

        if ($company->min_order_value > $order->price) {

            throw new CustomException('Pedido mínimo não atingido', 200);

        }

        $order->customer_id = $user->customer->id;

        $order->company_id = $company->id;

        $order->total_price += $order->delivery_price = $company->delivery_price;

        if ($attributes['payment_type'] == Order::PAYMENT_ONLINE) {

            // $card = CustomerCard::select('number', 'holder_name')
            //     ->where('id', $attributes['customer_card_id'])
            //     ->first();

            // $order->payment_method = collect([
            //     'name' => $attributes['payment_method_id'],
            //     'icon' => '',
            //     'card_latest_numbers' => substr($card->number, -4),
            //     'card_holder' => $card->holder_name
            // ])->toJson();

            $this->payWithMercadoPago([
                'total_price' => $order->total_price,
                'card_token' => $attributes['card_token'],
                'company' => $company->name,
                'payment_method_id' => $attributes['payment_method_id'],
                'customer' => $user->name,
                'email' => $user->email,
            ]);

        }

        elseif ($attributes['payment_type'] == Order::PAYMENT_DELIVERY) {

            $order->payment_method = PaymentMethod::select('name', 'icon')
                ->where('id', $attributes['payment_method_id'])
                ->first()
                ->toJson();

            if (isset($attributes['change_money'])) {

                $order->change_money = $attributes['change_money'];

            }

        }

        $order->type = $attributes['type'];

        $order->payment_type = $attributes['payment_type'];

        $order->forecast = date('Y-m-d H:i:s', strtotime("+ {$company->waiting_time} minute"));

        $order->products = $this->serializeProduct($attributes['products']);

        $order->save();

        return $order;
    }

    /**
     * @param mixed $attributes
     * @return void
     */
    private function validateCreateByCompany(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'company_id' => [
                'required', 'numeric',
                function ($attribute, $value, $fail) {
                    if (!CompanyRepository::checkAuth($value)) {
                        $fail('Empresa não autorizada.');
                    }
                }
            ],
            'table_id' => [
                'nullable', 'numeric',
                function ($attribute, $value, $fail) use ($attributes) {
                    if (Table::where('id', $value)->where('company_id', $attributes['company_id'])->count() == 0) {
                        $fail('Mesa não encontrada.');
                    }
                }
            ],
            'attendant_id' => [
                'nullable', 'numeric',
                function ($attribute, $value, $fail) use ($attributes) {
                    if (Attendant::where('id', $value)->where('company_id', $attributes['company_id'])->count() == 0) {
                        $fail('Atendente não encontrada.');
                    }
                }
            ],
            'products' => 'required|array',
            'products.*.id' => [
                'required', 'numeric',
                function ($attribute, $value, $fail) use ($attributes) {
                    if (Product::where('id', $value)->where('company_id', $attributes['company_id'])->count() == 0) {
                        $fail('Produto não encontrado.');
                    }
                }
            ],
            'products.*.qty' => 'required|numeric|min:1',
            'type' => [
                'required', 'numeric',
                function ($attribute, $value, $fail) {
                    if ($value !== Order::TYPE_LOCAL && $value !== Order::TYPE_DELIVERY) {
                        $fail('Tipo inválido, informe 0 para local ou 1 para delivery.');
                    }
                }
            ],
            'payment_type' => [
                'required_if:type,1', 'numeric',
                function ($attribute, $value, $fail) {
                    if ($value !== Order::PAYMENT_LOCAL && $value !== Order::PAYMENT_DELIVERY) {
                        $fail('Tipo inválido, informe 0 para local ou 2 para delivery.');
                    }
                }
            ],
            'address' => 'required_if:type,1|string|max:500',
            'additional_information' => 'nullable|string|max:500'
        ]);

        $validator->validate();
    }

    /**
     * @param array $products
     * @return float
     */
    private function calculatePrice(array $products): float
    {
        $items = Product::selectRaw('id, (price - IFNULL(rebate, 0)) as price')
            ->whereIn('id', Arr::pluck($products, 'id'))
            ->get()
            ->pluck('price', 'id');

        $total = 0;

        foreach ($products as $product) {

            $index = $product['id'];

            $total += ($items[$index] * $product['qty']);

        }

        return $total;
    }

    /**
     * @param array $products
     * @return string
     */
    private function serializeProduct(array $products): string
    {
        $items = Product::selectRaw('id, name, (price - IFNULL(rebate, 0)) as price')
            ->whereIn('id', Arr::pluck($products, 'id'))
            ->get()
            ->keyBy('id');

        $data = new Collection();

        foreach ($products as $product) {

            $index = $product['id'];

            $item = $items[$index];

            $data->add([
                'name' => $item->name,
                'qty' => $product['qty'],
                'price' => $item->price
            ]);

        }

        return $data->toJson();
    }

    /**
     * @param mixed $attributes
     * @return void
     */
    private function validateCreateByUser(array $attributes): void
    {
        $validator = Validator::make($attributes, [

            'card_token' => 'required_if:payment_type,1|string',

            'change_money' => 'nullable|numeric',

            'type' => [

                'required', 'numeric',

                function ($attribute, $value, $fail) {

                    if ($value < 1 || $value > 2) {

                        return $fail('Tipo inválido! Escolha 1 para DELIVERY ou 2 para RETIRADA.');

                    }

                }

            ],

            'payment_type' => [

                'required', 'numeric',

                function ($attribute, $value, $fail) {

                    if ($value < 0 || $value > 2) {

                        return $fail('Tipo de pagamento invalido. Escolha 0 para PAGAMENTO LOCAL, 1 para PAGAMENTO ONLINE ou 2 para PAGAMENTO NA ENTREGA.');

                    }

                }

            ],

            'company_id' => [

                'required', 'numeric',

                function ($attribute, $value, $fail) {

                    if (Company::where('id', $value)->count() == 0) {

                        return $fail('Empresa não encontrada.');

                    }

                }

            ],

            'customer_card_id' => [

                'required_if:payment_type,1', 'numeric',

                function ($attribute, $value, $fail) {

                    if (!$this->checkCustomerCard($value)) {

                        return $fail('Esse cartão não foi cadastrado.');

                    }

                }

            ],

            'payment_method_id' => [

                'required_if:payment_type,1,2',

                function ($attribute, $value, $fail) {

                    $payment_type = $this->request->get('payment_type');

                    $company_id = $this->request->get('company_id');

                    $mercadopago = ['visa', 'master', 'hipercard', 'amex', 'elo'];

                    if ($payment_type == Order::PAYMENT_ONLINE && !in_array($value, $mercadopago)) {

                        return $fail('Método de pagamento inválido.');

                    }

                    elseif ($payment_type == Order::PAYMENT_DELIVERY && !$this->checkPaymentMethod($value, $company_id)) {

                        return $fail('Método de pagamento indisponível para esta empresa.');

                    }

                }

            ],

            'customer_location_id' => [

                'required_if:type,1', 'numeric',

                function ($attribute, $value, $fail) {

                    if (!$this->checkCustomerLocation($value)) {

                        return $fail('Localização do usuário não encontrada.');

                    }

                }

            ]

        ]);

        $validator->validate();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function payWithMercadoPago(array $attributes): void
    {
        MercadoPago\SDK::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));

        $payment = new MercadoPago\Payment();

        $payment->transaction_amount = $attributes['total_price'];

        $payment->token = $attributes['card_token'];

        $payment->description = $attributes['company'];

        $payment->statement_descriptor = env('APP_NAME');

        $payment->installments = 1;

        $payment->payment_method_id = $attributes['payment_method_id'];

        $payment->payer = [
            'first_name' => Str::beforeLast($attributes['customer'], ' '),
            'last_name' => Str::afterLast($attributes['customer'], ' '),
            'email' => $attributes['email']
        ];

        $payment->save();

        if ($payment->status !== 'approved') {

            throw new CustomException('Pagamento recusado. Tente outro método de pagamento!', 200);

        }
    }
}
