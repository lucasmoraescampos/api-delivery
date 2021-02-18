<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Interface EloquentRepositoryInterface
 * @package App\Repositories
 */
interface EloquentRepositoryInterface
{
    /**
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * @param mixed $id
     * @return Model
     */
    public function getById($id): Model;

    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * @param mixed $id
     * @param array $attributes
     * @return Model
     */
    public function update($id, array $attributes): Model;

    /**
     * @param mixed $id
     * @return void
     */
    public function delete($id): void;
}
