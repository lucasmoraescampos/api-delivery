<?php

namespace App\Listeners;

use App\Events\CompanyStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class SendCompanySocket
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
     * @param  CompanyStatus  $event
     * @return void
     */
    public function handle(CompanyStatus $event)
    {
        $base_url = env('SOCKET_URL');

        Http::withHeaders(['authorization' => $_SERVER['HTTP_AUTHORIZATION']])
            ->post("{$base_url}/user/publish", [
                'room' => "user-{$event->company->user_id}",
                'event' => 'companyStatus',
                'data' => $event->company
            ]);
    }
}
