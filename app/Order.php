<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['user_id', 'company_id', 'total_price'];

    public static function validate($data)
    {
        if (!isset($data['company_id'])) {

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'company_id' => [
                        'O campo company_id é obrigatório.'
                    ]
                ]
            ], 422);

        }

        if (!isset($data['products']) || count($data['products']) == 0) {

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'products' => [
                        'O array products é obrigatório.'
                    ]
                ]
            ], 422);

        }

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

            }

            else {

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

        return true;
    }

    public static function create($products, $company_id)
    {
        $orders_products = [];

        $orders_subcomplements = [];

        $order_id = null;

        $total_price = 0;

        foreach ($products as $product) {

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

        $order_id = Order::insertGetId([
            'user_id' => Auth::id(),
            'company_id' => $company_id,
            'total_price' => $total_price
        ]);

        OrderProduct::insert($orders_products);

        OrderSubcomplement::insert($orders_subcomplements);

        return Order::find($order_id);

    }
}
