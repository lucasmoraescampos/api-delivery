<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use MercadoPago;

class Order extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'company_id',
        'payment_type',
        'payment_method_id',
        'card_number',
        'card_holder_name',
        'address',
        'latitude',
        'longitude',
        'cashback',
        'price',
        'delivery_price',
        'amount',
        'fee_meu_pedido',
        'fee_mercado_pago',
        'status'
    ];

    public static function get()
    {
        $orders = Order::from('orders as o')
            ->select(
                'o.id',
                'o.created_at',
                'o.delivered_at',
                'o.feedback',
                'o.status',
                'c.name as company_name',
                'c.waiting_time',
                'c.phone as company_phone',
                'c.photo as company_photo'
            )
            ->leftJoin('companies as c', 'c.id', 'o.company_id')
            ->where('o.user_id', Auth::id())
            ->orderBy('o.created_at', 'desc')
            ->get();

        foreach ($orders as &$order) {

            if ($order->delivered_at == null) {

                $order->waiting_time = Order::prepareWaitingTime($order->created_at, $order->waiting_time);
            } else {

                $order->waiting_time = null;
            }

            $order->products = OrderProduct::from('orders_products as o')
                ->select('p.name', 'o.qty')
                ->leftJoin('products as p', 'p.id', 'o.product_id')
                ->where('o.order_id', $order->id)
                ->get();
        }

        return $orders;
    }

    public static function getById($id)
    {
        $order = Order::from('orders as o')
            ->select(
                'o.id',
                'o.created_at',
                'o.delivered_at',
                'o.feedback',
                'o.address',
                'o.payment_type',
                'o.payment_method_id',
                'o.card_number',
                'o.card_holder_name',
                'o.price',
                'o.delivery_price',
                'o.amount',
                'o.status',
                'c.name as company_name',
                'c.waiting_time',
                'c.phone as company_phone',
                'c.photo as company_photo',
                'p.name as payment_method_name',
                'p.icon as payment_method_icon'
            )
            ->leftJoin('companies as c', 'c.id', 'o.company_id')
            ->leftJoin('payment_methods as p', 'p.id', 'o.payment_method_id')
            ->where('o.user_id', Auth::id())
            ->where('o.id', $id)
            ->first();

        if ($order->delivered_at == null) {

            $order->waiting_time = Order::prepareWaitingTime($order->created_at, $order->waiting_time);
        } else {

            $order->waiting_time = null;
        }

        $order->products = OrderProduct::from('orders_products as o')
            ->select('p.id', 'p.name', 'o.unit_price', 'o.qty', 'o.note')
            ->leftJoin('products as p', 'p.id', 'o.product_id')
            ->where('o.order_id', $order->id)
            ->get();

        foreach ($order->products as &$product) {

            $product->subcomplements = OrderSubcomplement::from('orders_subcomplements as o')
                ->select('s.description', 'o.unit_price', 'o.qty')
                ->leftJoin('subcomplements as s', 's.id', 'o.subcomplement_id')
                ->leftJoin('complements as c', 'c.id', 's.complement_id')
                ->where('o.order_id', $order->id)
                ->where('c.product_id', $product->id)
                ->get();
        }

        return $order;
    }

    public static function validateProducts($data)
    {
        if (count($data['products']) == 0) {

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'products' => [
                        'O array products é obrigatório.'
                    ]
                ]
            ], 422);
        }

        $total_price = 0;

        foreach ($data['products'] as $key => $product) {

            if (!isset($product['id'])) {

                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        "products[$key]" => [
                            'O campo id é obrigatório.'
                        ]
                    ]
                ], 422);
            }

            if (!isset($product['qty'])) {

                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        "products[$key]" => [
                            'O campo qty é obrigatório.'
                        ]
                    ]
                ], 422);
            }

            $product2 = Product::find($product['id']);

            if ($product2 == null) {

                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        "products[$key]" => [
                            'Id não encontrado.'
                        ]
                    ]
                ], 422);
            }

            if ($product2->company_id != $data['company_id']) {

                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        "products[$key]" => [
                            "Este produto não pertence à company_id {$data['company_id']}."
                        ]
                    ]
                ], 422);
            }

            $total_price += $product2->price * $product['qty'];

            if (isset($product['complements'])) {

                $complements_requireds = [];

                foreach ($product['complements'] as $key2 => $complement) {

                    if (!isset($complement['id'])) {

                        return response()->json([
                            'message' => 'The given data was invalid.',
                            'errors' => [
                                "products[$key]['complements'][$key2]" => [
                                    'O campo id é obrigatório.'
                                ]
                            ]
                        ], 422);
                    }

                    if (!isset($complement['subcomplements']) || count($complement['subcomplements']) == 0) {

                        return response()->json([
                            'message' => 'The given data was invalid.',
                            'errors' => [
                                "products[$key]['complements'][$key2]" => [
                                    'O array subcomplements é obrigatório.'
                                ]
                            ]
                        ], 422);
                    }

                    $complement2 = Complement::find($complement['id']);

                    if ($complement2 == null) {

                        return response()->json([
                            'message' => 'The given data was invalid.',
                            'errors' => [
                                "products[$key]['complements'][$key2]" => [
                                    'Id não encontrado.'
                                ]
                            ]
                        ], 422);
                    }

                    if ($complement2->is_required) {

                        $complements_requireds[] = $complement2->id;
                    }

                    $qty = 0;

                    foreach ($complement['subcomplements'] as $key3 => $subcomplement) {

                        if (!isset($subcomplement['id'])) {

                            return response()->json([
                                'message' => 'The given data was invalid.',
                                'errors' => [
                                    "products[$key]['complements'][$key2]['subcomplements][$key3]" => [
                                        'O campo id é obrigatório.'
                                    ]
                                ]
                            ], 422);
                        }

                        if (!isset($subcomplement['qty'])) {

                            return response()->json([
                                'message' => 'The given data was invalid.',
                                'errors' => [
                                    "products[$key]['complements'][$key2]['subcomplements][$key3]" => [
                                        'O campo qty é obrigatório.'
                                    ]
                                ]
                            ], 422);
                        }

                        $subcomplement2 = Subcomplement::find($subcomplement['id']);

                        if ($subcomplement2 == null) {

                            return response()->json([
                                'message' => 'The given data was invalid.',
                                'errors' => [
                                    "products[$key]['complements'][$key2]['subcomplements][$key3]" => [
                                        'Id não encontrado.'
                                    ]
                                ]
                            ], 422);
                        }

                        if ($subcomplement2->complement_id != $complement2->id) {

                            return response()->json([
                                'message' => 'The given data was invalid.',
                                'errors' => [
                                    "products[$key]['complements'][$key2]['subcomplements][$key3]" => [
                                        'Este subcomplemento não pertence ao complemento.'
                                    ]
                                ]
                            ], 422);
                        }

                        $qty += $subcomplement['qty'];

                        $total_price += $subcomplement2->price * $subcomplement['qty'];
                    }

                    if ($qty > $complement2->qty_max) {

                        return response()->json([
                            'message' => 'The given data was invalid.',
                            'errors' => [
                                "products[$key]['complements'][$key2]" => [
                                    'Este complemento excede a quantidade máxima permitida.'
                                ]
                            ]
                        ], 422);
                    }

                    if ($complement2->qty_min !== null && $qty < $complement2->qty_min) {

                        return response()->json([
                            'message' => 'The given data was invalid.',
                            'errors' => [
                                "products[$key]['complements'][$key2]" => [
                                    'Este complemento não atinge a quantidade mínima estabelecida.'
                                ]
                            ]
                        ], 422);
                    }
                }
            }

            if (isset($complements_requireds) && count($complements_requireds) > 0) {

                $complement = Complement::where('product_id', $product['id'])
                    ->where('is_required', 1)
                    ->whereNotIn('id', $complements_requireds)
                    ->first();

                if ($complement != null) {

                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            "products[$key]" => [
                                "O Complemento $complement->id é obrigatório."
                            ]
                        ]
                    ], 422);
                }
            } else {

                $complement = Complement::where('product_id', $product['id'])
                    ->where('is_required', 1)
                    ->first();

                if ($complement != null) {

                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            "products[$key]" => [
                                "O Complemento $complement->id é obrigatório."
                            ]
                        ]
                    ], 422);
                }
            }
        }

        $company = Company::find($data['company_id']);

        if ($total_price < $company->min_value) {

            $value = number_format($company->min_value, 2, ',', '.');

            return response()->json([
                'status' => false,
                'message' => "O pedido mínimo para esta loja é de R$ $value, não inclusa a taxa de entrega!"
            ]);
        }

        return true;
    }

    public static function create($data)
    {
        $orders_products = [];

        $orders_subcomplements = [];

        $order_id = null;

        $total_price = 0;

        $company = Company::find($data['company_id']);

        foreach ($data['products'] as $product) {

            $product_price = Product::find($product['id'])->price;

            $total_price += $product_price * $product['qty'];

            $note = isset($product['note']) ? $product['note'] : null;

            $orders_products[] = [
                'order_id' => &$order_id,
                'product_id' => $product['id'],
                'qty' => $product['qty'],
                'unit_price' => $product_price,
                'note' => $note
            ];

            if (isset($product['complements'])) {

                foreach ($product['complements'] as $complement) {

                    foreach ($complement['subcomplements'] as $subcomplement) {

                        $subcomplement_price = Subcomplement::find($subcomplement['id'])->price;

                        $total_price += $subcomplement_price * $subcomplement['qty'];

                        $orders_subcomplements[] = [
                            'order_id' => &$order_id,
                            'subcomplement_id' => $subcomplement['id'],
                            'qty' => $subcomplement['qty'],
                            'unit_price' => $subcomplement_price
                        ];
                    }
                }
            }
        }

        $amount = $total_price + $company->delivery_price;

        if ($data['payment_type'] == PAYMENT_APP) {

            $user = Auth::user();

            MercadoPago\SDK::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));

            $payment = new MercadoPago\Payment();

            $payment->transaction_amount = $amount;

            $payment->token = $data['card_token'];

            $payment->description = PAYMENT_DESCRIPTION;

            $payment->statement_descriptor = PAYMENT_DESCRIPTION;

            $payment->installments = 1;

            $payment->payment_method_id = $data['payment_method_id'];

            $payment->payer = [
                'first_name' => $user->name,
                'last_name' => $user->surname,
                'email' => $user->email
            ];

            $payment->save();

            if ($payment->status == 'approved') {

                $fee_mercado_pago = $amount - $payment->transaction_details->net_received_amount;

                $order_id = Order::insertGetId([
                    'user_id' => Auth::id(),
                    'company_id' => $data['company_id'],
                    'payment_type' => $data['payment_type'],
                    'payment_method_id' => $data['payment_method_id'],
                    'card_number' => $data['card_number'],
                    'card_holder_name' => $data['card_holder_name'],
                    'address' => $data['address'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'price' => $total_price,
                    'delivery_price' => $company->delivery_price,
                    'amount' => $amount,
                    'fee_meu_pedido' => 0,
                    'fee_mercado_pago' => $fee_mercado_pago,
                    'status' => WAITING_CONFIRMATION,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } else {

                return false;
            }
        } else {

            $order_id = Order::insertGetId([
                'user_id' => Auth::id(),
                'company_id' => $data['company_id'],
                'payment_type' => $data['payment_type'],
                'payment_method_id' => $data['payment_method_id'],
                'address' => $data['address'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'cashback' => $data['cashback'],
                'price' => $total_price,
                'delivery_price' => $company->delivery_price,
                'amount' => $amount,
                'fee_meu_pedido' => 0,
                'status' => WAITING_CONFIRMATION,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        OrderProduct::insert($orders_products);

        OrderSubcomplement::insert($orders_subcomplements);

        return Order::find($order_id);
    }

    public function sendFeedback($feedback)
    {
        $this->feedback = $feedback;

        $this->save();

        $qty = Order::where('company_id', $this->company_id)
            ->whereNotNull('feedback')
            ->count();

        $sum = Order::where('company_id', $this->company_id)
            ->whereNotNull('feedback')
            ->sum('feedback');

        Company::where('id', $this->company_id)->update([
            'feedback' => ($sum + $feedback) / ($qty + 2)
        ]);
    }

    public function confirmDelivery()
    {
        $this->status = DELIVERED;

        $this->delivered_at = date('Y-m-d H:i:s');

        $this->save();
    }

    private static function prepareWaitingTime($created_at, $waiting_time)
    {
        $time = date('H:i', strtotime("+$waiting_time minutes", strtotime($created_at)));

        $time .= ' - ';

        $waiting_time += 10;

        $time .= date('H:i', strtotime("+$waiting_time minutes", strtotime($created_at)));

        return $time;
    }
}
