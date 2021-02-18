<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface DeliveryPersonRepositoryInterface
{
    /**
     * @param mixed $company_id
     * @return Collection
     */
    public function getByCompany($company_id): Collection;
}