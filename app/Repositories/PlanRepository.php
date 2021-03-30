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
}