<?php

namespace App\Repositories;

use App\Models\Plan;
use MercadoPago;
use App\Models\PlanSubscription;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PlanRepository extends BaseRepository implements PlanRepositoryInterface
{
    /**
     * PlanRepository constructor.
     *
     * @param Plan $model
     */
    public function __construct(Plan $model)
    {
        parent::__construct($model);
    }

    /**
     * @param mixed $user_id
     * @return PlanSubscription
     */
    public static function getCurrentPlanByUser($user_id): ?PlanSubscription
    {
        return PlanSubscription::with('plan')
            ->where('user_id', $user_id)
            ->where('status', true)
            ->orderBy('created_at', 'desc')
            ->first();   
    }

    /**
     * @param array $attributes
     * @return PlanSubscription
     */
    public function subscription(array $attributes): PlanSubscription
    {
        $this->validateSubscription($attributes);

        $plan = Plan::find($attributes['plan_id']);

        $payment = $this->pay([
            'transaction_amount' => $plan->price,
            'token' => $attributes['card_token'],
            'payment_method_id' => $attributes['payment_method_id'],
            'description' => env('APP_NAME') . ' ' . $plan->name
        ]);

        if ($payment->status === 'approved') {

            $transaction_details = $payment->transaction_details;

            $fee = $transaction_details->total_paid_amount - $transaction_details->net_received_amount;

            $planSubscription = PlanSubscription::create([
                'user_id' => Auth::id(),
                'plan_id' => $plan->id,
                'payment_id' => $payment->id,
                'transaction_amount' => $payment->transaction_amount,
                'transaction_fee' => $fee,
                'expiration' => date('Y-m-d H:i:s', strtotime('+ 1 month'))
            ]);

            $planSubscription->load('plan');

            return $planSubscription;

        }

        else {

            throw new CustomException('Pagamento rejeitado! Tente novamente usando outro cartão.', 200);

        }
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateSubscription(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'payment_method_id' => 'required|string',
            'card_token' => 'required|string',
            'plan_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (Plan::where('id', $value)->count() == 0) {
                        return $fail('Plano não encontrado.');
                    }
                }
            ]
        ]);

        $validator->validate();
    }

    /**
     * @param array $attributes
     * @return MercadoPago\Payment
     */
    private function pay(array $attributes): MercadoPago\Payment
    {
        MercadoPago\SDK::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));

        $user = Auth::user();

        $payment = new MercadoPago\Payment();

        $payment->transaction_amount = (float) $attributes['transaction_amount'];
        $payment->token = $attributes['token'];
        $payment->description = $attributes['description'];
        $payment->installments = 1;
        $payment->payment_method_id = $attributes['payment_method_id'];

        $payer = new MercadoPago\Payer();

        $payer->type = 'customer';
        $payer->id = $user->customer_id;
        $payer->email = $user->email;

        $payment->payer = $payer;

        $payment->save();

        return $payment;
    }
}