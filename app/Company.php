<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
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

    public static function getPerformance()
    {
        $year = date('Y', strtotime('-5 month'));

        $month = date('m', strtotime('-5 month'));

        $date = "$year-$month-01 00:00:00";

        $months = [
            [
                'name' => date('Y-m-d', strtotime('-5 month')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-d', strtotime('-4 month')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-d', strtotime('-3 month')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-d', strtotime('-2 month')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-d', strtotime('-1 month')),
                'value' => 0
            ],
            [
                'name' => date('Y-m-d'),
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
            ->where('status', DELIVERED)
            ->where('company_id', Auth::id())
            ->get();

        foreach ($orders as $order) {

            if (date('Y-m-d', strtotime($order->created_at)) == $months[0]['name']) {
                $months[0]['value'] += $order->amount;
            }
            elseif (date('Y-m-d', strtotime($order->created_at)) == $months[1]['name']) {
                $months[1]['value'] += $order->amount;
            }
            elseif (date('Y-m-d', strtotime($order->created_at)) == $months[2]['name']) {
                $months[2]['value'] += $order->amount;
            }
            elseif (date('Y-m-d', strtotime($order->created_at)) == $months[3]['name']) {
                $months[3]['value'] += $order->amount;
            }
            elseif (date('Y-m-d', strtotime($order->created_at)) == $months[4]['name']) {
                $months[4]['value'] += $order->amount;
            }
            elseif (date('Y-m-d', strtotime($order->created_at)) == $months[5]['name']) {
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
