<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface LocaleRepositoryInterface
{
    /**
     * @return Collection
     */
    public static function getStates(): Collection;

    /**
     * @param mixed $uf
     * @return Collection
     */
    public function getCitiesByUF($uf): Collection;
}
