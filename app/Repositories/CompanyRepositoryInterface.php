<?php

namespace App\Repositories;

use App\Models\Favorite;

interface CompanyRepositoryInterface
{
    /**
     * @param array $attributes
     * @return Favorite
     */
    public function createFavorite(array $attributes): Favorite;

    /**
     * @param mixed $company_id
     * @return void
     */
    public function deleteFavorite($company_id): void;
}
