<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface CardRepositoryInterface
{
    /**
     * @return Collection
     */
    public function getByAuth(): Collection;
}
