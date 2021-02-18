<?php

namespace App\Repositories;

use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BaseRepository implements EloquentRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * @param mixed $id
     * @return Model
     */
    public function getById($id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * @param mixed $id
     * @param array $attributes
     * @return Model
     */
    public function update($id, array $attributes): Model
    {
        $model = $this->model->where('id', $id)->firstOrFail();

        $model->fill($attributes);

        $model->save();

        return $model;
    }

    /**
     * @param mixed $id
     * @return void
     */
    public function delete($id): void
    {
        $model = $this->model->where('id', $id)->firstOrFail();

        $model->delete();
    }
}
