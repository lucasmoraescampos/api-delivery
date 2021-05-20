<?php

namespace App\Repositories;

use App\Models\Favorite;

interface CompanyRepositoryInterface
{
    /**
     * @param array $attributes
     * @return Favorite
     */
    public function favorite(array $attributes): Favorite;
}
