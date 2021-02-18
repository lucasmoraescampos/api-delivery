<?php

namespace App\Repositories;

use App\Models\PlanSubscription;

interface PlanRepositoryInterface
{
    /**
     * @param mixed $user_id
     * @return PlanSubscription
     */
    public static function getCurrentPlanByUser($user_id): ?PlanSubscription;

    /**
     * @param array $attributes
     * @return PlanSubscription
     */
    public function subscription(array $attributes): PlanSubscription;
}
