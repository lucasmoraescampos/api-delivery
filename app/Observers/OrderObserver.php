<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the order "created" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        $base_url = env('SOCKET_URL');

        $order->load('company');

        $room = [
            'mp-room-user-' . $order->user_id,
            'mp-room-user-' . $order->company->user_id,
        ];

        Http::post("{$base_url}/user/publish", [
            'room' => $room,
            'event' => 'created-order', 
            'data' => $order
        ]);
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        $base_url = env('SOCKET_URL');

        $order->load('company');

        $room = [
            'mp-room-user-' . $order->user_id,
            'mp-room-user-' . $order->company->user_id,
        ];

        Http::post("{$base_url}/user/publish", [
            'room' => $room,
            'event' => 'updated-order', 
            'data' => $order
        ]);
    }
}
