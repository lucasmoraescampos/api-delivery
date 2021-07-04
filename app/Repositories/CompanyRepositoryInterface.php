<?php

namespace App\Repositories;

use App\Models\Favorite;
use Illuminate\Support\Collection;

interface CompanyRepositoryInterface
{
    /**
     * @param array $attributes
     * @return Collection
     */
    public function getFavorites(array $attributes): Collection;

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
