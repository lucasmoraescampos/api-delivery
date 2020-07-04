<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Storage;

class Company extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'companies';

    protected $fillable = [
        'category_id',
        'name',
        'email',
        'phone',
        'password',
        'zipcode',
        'street_name',
        'street_number',
        'complement',
        'district',
        'city',
        'uf',
        'latitude',
        'longitude',
        'min_value',
        'delivery_price',
        'waiting_time',
        'range',
        'is_open',
        'accept_payment_app',
        'accept_outsourced_delivery',
        'accept_withdrawal_local'
    ];

    protected $attributes = [
        'balance_available' => 0,
        'balance_receivable' => 0,
        'status' => WAITING,
        'is_open' => 0
    ];

    protected $hidden = [
        'password'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getQtyMenuSessions()
    {
        return MenuSession::where('company_id', $this->id)->count();
    }

    public static function getByCategory($category_id)
    {
        return Company::select('id', 'photo', 'name', 'waiting_time', 'latitude', 'longitude', 'delivery_price', 'is_open', 'feedback')
            ->where('category_id', $category_id)
            ->whereIn('id', function ($query) {

                $query->select('company_id')
                    ->from(with(new Product)->getTable())
                    ->distinct();
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('is_open', 'desc')
            ->get();
    }

    public static function getBySubcategory($subcategory_id)
    {
        return Company::select('id', 'photo', 'name', 'waiting_time', 'latitude', 'longitude', 'delivery_price', 'is_open', 'feedback')
            ->whereIn('id', function ($query) use ($subcategory_id) {

                $query->select('company_id')
                    ->from(with(new Product)->getTable())
                    ->where('subcategory_id', $subcategory_id)
                    ->distinct();
            })
            ->orderBy('created_at', 'asc')
            ->orderBy('is_open', 'desc')
            ->get();
    }

    public static function getBySearch($search)
    {
        return Product::from('products as p')
            ->select('c.id', 'c.photo', 'c.name', 'c.waiting_time', 'c.latitude', 'c.longitude', 'c.delivery_price', 'c.created_at', 'c.is_open', 'c.feedback')
            ->leftJoin('companies as c', 'c.id', 'p.company_id')
            ->where('p.name', 'like', "%$search%")
            ->orWhere('c.name', 'like', "%$search%")
            ->orderBy('c.created_at', 'asc')
            ->orderBy('c.is_open', 'desc')
            ->distinct()
            ->get();
    }

    public function getPerformance()
    {
        $year = date('Y', strtotime('-5 month'));

        $month = date('m', strtotime('-5 month'));

        $date = "$year-$month-01 00:00:00";

        $months = [
            [
                'name' => date('Y-m-01', strtotime('-5 month')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-01', strtotime('-4 month')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-01', strtotime('-3 month')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-01', strtotime('-2 month')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-01', strtotime('-1 month')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-01'),
                'value' => 0
            ]
        ];

        $days = [
            [
                'name' => date('Y-m-d', strtotime('-5 day')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-d', strtotime('-4 day')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-d', strtotime('-3 day')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-d', strtotime('-2 day')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-d', strtotime('-1 day')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-d'),
                'value' => 0
            ]
        ];

        $orders = Order::select('created_at', 'amount')
            ->where('created_at', '>=', $date)
            ->where('status', COMPLETED)
            ->where('company_id', $this->id)
            ->get();

        foreach ($orders as $order) {

            if (date('m', strtotime($order->created_at)) == date('m', strtotime($months[0]['name']))) {
                $months[0]['value'] += $order->amount;
            }
            elseif (date('m', strtotime($order->created_at)) == date('m', strtotime($months[1]['name']))) {
                $months[1]['value'] += $order->amount;
            }
            elseif (date('m', strtotime($order->created_at)) == date('m', strtotime($months[2]['name']))) {
                $months[2]['value'] += $order->amount;
            }
            elseif (date('m', strtotime($order->created_at)) == date('m', strtotime($months[3]['name']))) {
                $months[3]['value'] += $order->amount;
            }
            elseif (date('m', strtotime($order->created_at)) == date('m', strtotime($months[4]['name']))) {
                $months[4]['value'] += $order->amount;
            }
            elseif (date('m', strtotime($order->created_at)) == date('m', strtotime($months[5]['name']))) {
                $months[5]['value'] += $order->amount;
            }

            if (date('Y-m-d', strtotime($order->created_at)) == $days[0]['name']) {
                $days[0]['value'] += $order->amount;
            }
            elseif (date('Y-m-d', strtotime($order->created_at)) == $days[1]['name']) {
                $days[1]['value'] += $order->amount;
            }
            elseif (date('Y-m-d', strtotime($order->created_at)) == $days[2]['name']) {
                $days[2]['value'] += $order->amount;
            }
            elseif (date('Y-m-d', strtotime($order->created_at)) == $days[3]['name']) {
                $days[3]['value'] += $order->amount;
            }
            elseif (date('Y-m-d', strtotime($order->created_at)) == $days[4]['name']) {
                $days[4]['value'] += $order->amount;
            }
            elseif (date('Y-m-d', strtotime($order->created_at)) == $days[5]['name']) {
                $days[5]['value'] += $order->amount;
            }
        }

        return [
            'days' => $days,
            'months' => $months
        ];
    }

    public function getOrders()
    {
        return Order::from('orders as o')
            ->select(
                'o.id',
                'o.latitude',
                'o.longitude',
                'o.delivery_forecast',
                'o.amount',
                'o.status',
                'o.is_withdrawal_local',
                'o.created_at',
                'o.delivered_at',
                'u.name',
                'u.surname'
            )
            ->leftJoin('users as u', 'u.id', 'o.user_id')
            ->where('o.company_id', $this->id)
            ->where('o.status', '<>', REFUSED)
            ->orderBy('o.created_at', 'desc')
            ->get()
            ->groupBy('status');
    }

    public function getOrderById($id)
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
                'o.price',
                'o.delivery_price',
                'o.delivery_forecast',
                'o.cashback',
                'o.amount',
                'o.status',
                'o.is_withdrawal_local',
                'u.id as user_id',
                'u.name as user_name',
                'u.surname as user_surname',
                'u.phone as user_phone',
                'p.name as payment_method_name',
                'p.icon as payment_method_icon'
            )
            ->leftJoin('users as u', 'u.id', 'o.user_id')
            ->leftJoin('payment_methods as p', 'p.id', 'o.payment_method_id')
            ->where('o.company_id', $this->id)
            ->where('o.status', '<>', REFUSED)
            ->where('o.id', $id)
            ->first();

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

    public function upload($file)
    {
        $this->deleteLastPhoto();

        $name = uniqid(date('HisYmd'));

        $ext = $file->extension();

        $full_name = "{$name}.{$ext}";

        $file->storeAs('companies', $full_name);

        $this->photo = 'https://api.meupedido.org/storage/companies/' . $full_name;

        $this->save();
    }

    private function deleteLastPhoto()
    {

        if ($this->photo) {

            $array = explode('/', $this->photo);

            $photo = 'companies/' . end($array);

            Storage::delete($photo);
        }
    }
}
