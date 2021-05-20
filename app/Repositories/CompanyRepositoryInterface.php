<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface CompanyRepositoryInterface
{
    /**
     * @param array $attributes
     * @return void
     */
    public function favorite(array $attributes): void;
}
