<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    /**
     * @param $adminLat
     * @param $adminLng
     * @param $userLat
     * @param $userLng
     * @return float
     */
    public static function calculateDistance($adminLat, $adminLng, $userLat, $userLng): float;

    /**
     * @param mixed $company_id
     * @return Collection
     */
    public function getByCompany($company_id): Collection;
}
