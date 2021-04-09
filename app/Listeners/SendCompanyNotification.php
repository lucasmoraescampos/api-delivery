<?php

namespace App\Listeners;

use App\Events\CompanyStatus;
use App\Models\FcmToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\WebPushConfig;

class SendCompanyNotification
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
        $company_url = env('SOCKET_URL');

        $messaging = app('firebase.messaging');

        $tokens = $this->getPlatformTokens($event->company->user_id);

        if (isset($tokens[FcmToken::PLATFORM_WEB])) {

            $config = WebPushConfig::fromArray([
                'notification' => [
                    'title' => 'Pedidos',
                    'body' => 'Você recebeu um novo pedido',
                    'icon' => $event->company->image,
                ],
                'fcm_options' => [
                    'link' => "{$company_url}/orders",
                ],
            ]);

            $message = CloudMessage::new()->withWebPushConfig($config);

            $messaging->sendMulticast($message, $tokens[FcmToken::PLATFORM_WEB]);
        }

        if (isset($tokens[FcmToken::PLATFORM_ANDROID])) {

            $config = AndroidConfig::fromArray([
                'ttl' => '3600s',
                'priority' => 'normal',
                'notification' => [
                    'title' => '$GOOG up 1.43% on the day',
                    'body' => '$GOOG gained 11.80 points to close at 835.67, up 1.43% on the day.',
                    'icon' => $event->company->image,
                    'color' => '#18a4e0',
                    'sound' => 'default',
                ],
            ]);
            
            $message = CloudMessage::new()->withAndroidConfig($config);

            $messaging->sendMulticast($message, $tokens[FcmToken::PLATFORM_ANDROID]);
        }

        if (isset($tokens[FcmToken::PLATFORM_IOS])) {

            $config = ApnsConfig::fromArray([
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => '$GOOG up 1.43% on the day',
                            'body' => '$GOOG gained 11.80 points to close at 835.67, up 1.43% on the day.',
                        ],
                        'badge' => 42,
                        'sound' => 'default',
                    ],
                ],
            ]);
            
            $message = CloudMessage::new()->withApnsConfig($config);

            $messaging->sendMulticast($message, $tokens[FcmToken::PLATFORM_IOS]);
        }
    }

    /**
     * @param mixed $user_id
     * @return array
     */
    private function getPlatformTokens($user_id): array
    {
        $fcmtokens = FcmToken::select('token', 'platform')
            ->where('user_id', $user_id)
            ->orderBy('updated_at', 'desc')
            ->limit(500)
            ->get();

        $tokens = [];

        foreach ($fcmtokens as $fcmtoken) {
            $tokens[$fcmtoken->platform][] = $fcmtoken->token;
        }

        return $tokens;
    }
}
