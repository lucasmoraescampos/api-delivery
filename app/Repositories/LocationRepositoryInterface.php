<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface LocationRepositoryInterface
{
    /**
     * @return Collection
     */
    public function getByAuth(): Collection;
}
