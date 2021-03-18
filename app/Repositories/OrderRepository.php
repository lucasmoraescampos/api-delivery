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
use App\Models\Card;
use App\Models\CompanyPaymentMethod;
use App\Models\Complement;
use App\Models\Location;
use App\Models\Subcomplement;
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
    public function create(array $attributes): Order
    {
        $this->validateCreate($attributes);

        $user = Auth::user();

        $company = Company::find($attributes['company_id']);

        $order = new Order();

        $order->price = $this->calculatePrice($attributes['products']);

        if ($company->min_order_value > $order->price) {

            throw new CustomException('Pedido mínimo não atingido', 422);

        }

        $order->user_id = $user->id;

        $order->company_id = $company->id;

        $order->delivery_price = $company->delivery_price;

        $order->total_price = $order->price + $order->delivery_price;

        if ($attributes['payment_type'] == Order::PAYMENT_ONLINE) {


            // $order->payment_method = collect([
            //     'name' => $attributes['payment_method_id'],
            //     'icon' => ''
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
     * @param array $products
     * @return float
     */
    private function calculatePrice(array $products): float
    {
        $products_prices = Product::selectRaw('id, (price - rebate) as price')
            ->whereIn('id', Arr::pluck($products, 'id'))
            ->get()
            ->pluck('price', 'id');

        $subcomplements_prices = Subcomplement::select('id', 'price')
            ->whereIn('id', Arr::collapse(Arr::pluck($products, 'complements.*.subcomplements.*.id')))
            ->get()
            ->pluck('price', 'id');

        $total = 0;

        foreach ($products as $product) {

            $subtotal = 0;

            if (isset($product['complements'])) {

                foreach ($product['complements'] as $complement) {

                    foreach ($complement['subcomplements'] as $subcomplement) {

                        $subtotal += $subcomplements_prices[$subcomplement['id']] * $subcomplement['qty'];

                    }

                }

            }

            $total += ($products_prices[$product['id']] + $subtotal) * $product['qty'];

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
    private function validateCreate(array $attributes): void
    {
        $validator = Validator::make($attributes, [

            'type' => [

                'required', 'numeric',

                function ($attribute, $value, $fail) {

                    if ($value != Order::TYPE_DELIVERY && $value != Order::TYPE_WITHDRAWAL) {

                        $fail('Tipo inválido! Escolha 1 para ENTREGA ou 2 para RETIRADA.');

                    }

                }

            ],

            'payment_type' => [

                'required', 'numeric',

                function ($attribute, $value, $fail) {

                    if ($value != Order::PAYMENT_ONLINE && $value != Order::PAYMENT_DELIVERY) {

                        $fail('Tipo de pagamento invalido. Escolha 1 para PAGAMENTO ONLINE ou 2 para PAGAMENTO NA ENTREGA.');

                    }

                }

            ],

            'location_id' => [

                'required_if:type,' . Order::TYPE_DELIVERY, 'numeric',

                function ($attribute, $value, $fail) {

                    $notFound = Location::where('id', $value)
                        ->where('user_id', Auth::id())
                        ->count() == 0;

                    if ($notFound) {

                        $fail('Localização não encontrada.');

                    }

                }

            ],

            'company_id' => [

                'required', 'numeric',

                function ($attribute, $value, $fail) {

                    $notFound = Company::where('id', $value)->count() == 0;

                    if ($notFound) {

                        $fail('Empresa não encontrada.');

                    }

                }

            ],

            'payment_method_id' => [

                'required',

                function ($attribute, $value, $fail) use ($attributes) {

                    $mercadopago = ['visa', 'master', 'hipercard', 'amex', 'elo'];

                    if ($attributes['payment_type'] == Order::PAYMENT_ONLINE && !in_array($value, $mercadopago)) {

                        $fail('Método de pagamento desconhecido.');

                    }

                    elseif ($attributes['payment_type'] == Order::PAYMENT_DELIVERY) {

                        $notAvailable = CompanyPaymentMethod::where('id', $value)
                            ->where('company_id', $attributes['company_id'])
                            ->count() == 0;

                        if ($notAvailable) {

                            $fail('Método de pagamento indisponível para esta empresa.');

                        }

                    }

                }

            ],

            'card_token' => 'required_if:payment_type,' . Order::PAYMENT_ONLINE . '|string',

            'change_money' => 'nullable|numeric',

            'products.*.id' => 'required|numeric',

            'products.*.qty' => 'required|numeric|min:1',

            'products.*.complements' => 'nullable|array',

            'products.*.complements.*.id' => 'required|numeric',

            'products.*.complements.*.subcomplements' => 'required|array',

            'products.*.complements.*.subcomplements.*.id' => 'required|numeric',

            'products.*.complements.*.subcomplements.*.qty' => 'required|numeric|min:1',

            'products' => [

                'required', 'array',

                function ($attribute, $value, $fail) use ($attributes) {

                    $products = Arr::pluck($value, 'id');

                    $notBelong = Product::select('id')
                        ->whereIn('id', $products)
                        ->where('company_id', '<>', $attributes['company_id'])
                        ->get();

                    if ($notBelong->count() > 0) {

                        $ids = $notBelong->pluck('id')->implode(', ');

                        return $fail("Os produtos id: {$ids} não pertencem a empresa id {$attributes['company_id']}.");

                    }

                    foreach ($value as $product) {

                        $complements = [];

                        if (isset($product['complements'])) {

                            $complements = Arr::pluck($product['complements'], 'id');

                            $notBelong = Complement::select('id')
                                ->whereIn('id', $complements)
                                ->where('product_id', '<>', $product['id'])
                                ->get();

                            if ($notBelong->count() > 0) {

                                $ids = $notBelong->pluck('id')->implode(', ');
        
                                return $fail("Os complementos id: {$ids} não pertencem ao produto id {$product['id']}.");
        
                            }

                            foreach ($product['complements'] as $complement) {

                                $subcomplements = Arr::pluck($complement['subcomplements'], 'id');

                                $notBelong = Subcomplement::select('id')
                                    ->whereIn('id', $subcomplements)
                                    ->where('complement_id', $complement['id'])
                                    ->get();

                                if ($notBelong->count() > 0) {

                                    $ids = $notBelong->pluck('id')->implode(', ');
            
                                    return $fail("Os subcomplementos id: {$ids} não pertencem ao complemento id {$complement['id']}.");
            
                                }

                                $qty = array_sum(Arr::pluck($complement['subcomplements'], 'qty'));

                                $limit = Complement::find($complement['id'], ['qty_min', 'qty_max']);

                                if ($qty > $limit->qty_max) {

                                    return $fail("Qty total de subcomplementos é maior que o limite máximo permitido para o complemento id {$complement['id']}.");

                                }

                                if ($limit->qty_min !== null && $qty < $limit->qty_min) {

                                    return $fail("Qty total de subcomplementos é menor que o limite mínimo permitido para o complemento id {$complement['id']}.");

                                }

                            }

                        }

                        $requiredComplements = Complement::select('id')
                            ->where('product_id', $product['id'])
                            ->where('required', true)
                            ->get()
                            ->pluck('id')
                            ->diff($complements);

                        if ($requiredComplements->count() > 0) {

                            return $fail("Os complementos id: {$requiredComplements->implode(', ')} são obrigatórios para o produto id {$product['id']}.");

                        }

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
