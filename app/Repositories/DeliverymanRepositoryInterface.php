<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface DeliverymanRepositoryInterface
{
    /**
     * @param mixed $company_id
     * @return Collection
     */
    public function getByCompany($company_id): Collection;
}