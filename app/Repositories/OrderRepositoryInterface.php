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

    /**
     * @param array $attributes
     * @return Order
     */
    public function createByCompany(array $attributes): Order;

    /**
     * @param array $attributes
     * @return Order
     */
    public function createByUser(array $attributes): Order;
}
