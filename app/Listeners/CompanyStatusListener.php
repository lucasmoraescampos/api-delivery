<?php

namespace App\Listeners;

use App\Events\CompanyEvent;
use Illuminate\Support\Facades\Http;

class CompanyStatusListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CompanyEvent $event)
    {
        $base_url = env('SOCKET_URL');

        Http::post("{$base_url}/user/publish", [
            'room' => 'mp-user-' . $event->company->user_id,
            'event' => 'company', 
            'data' => $event->company
        ]);
    }
}
