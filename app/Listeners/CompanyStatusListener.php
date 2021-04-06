<?php

namespace App\Listeners;

use App\Events\CompanyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;

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
     * @param  CompanyEvent  $event
     * @return void
     */
    public function handle(CompanyEvent $event)
    {
        $base_url = env('SOCKET_URL');

        Http::withHeaders(['authorization' => $_SERVER['HTTP_AUTHORIZATION']])
            ->post("{$base_url}/user/publish", [
                'room' => "user-{$event->company->user_id}",
                'event' => 'companyStatus',
                'data' => $event->company
            ]);

        $messaging = app('firebase.messaging');

        $config = WebPushConfig::fromArray([
            'notification' => [
                'title' => '$GOOG up 1.43% on the day',
                'body' => '$GOOG gained 11.80 points to close at 835.67, up 1.43% on the day.',
                'icon' => $event->company->image,
            ],
            'fcm_options' => [
                'link' => 'https://localhost:8100/companies',
            ],
        ]);

        $message = CloudMessage::fromArray([
            'token' => 'fXU8lwgz0OJCMnAf8mwj_a:APA91bF4B0_2jBs1OspmzlRKX64NsrmhxZBIK8FQAybtAXV9kEo9Albgm2hXlY52mDBOS3Z2EHyuRWXCd8CivLerDDvqKWJApY3N7pNfa-2Qz4m_ehaTK5oTBzevtdolhjGBIH-9jKCx'
        ]);
        
        $message = $message->withWebPushConfig($config);
        
        $messaging->send($message);
    }
}
