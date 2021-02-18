<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LocaleRepository implements LocaleRepositoryInterface
{
    /**
     * @return Collection
     */
    public static function getStates(): Collection
    {
        return DB::table('states')->orderBy('name', 'asc')->get();
    }

    /**
     * @param mixed $uf
     * @return Collection
     */
    public function getCitiesByUF($uf): Collection
    {
        return DB::table('cities')->where('uf', $uf)->orderBy('name', 'asc')->get();
    }
}
