<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface SegmentRepositoryInterface
{
    /**
     * @param mixed $company_id
     * @return Collection
     */
    public function getByCompany($company_id): Collection;

    /**
     * @param array $attributes
     * @return Collection
     */
    public function reorder(array $attributes): Collection;
}