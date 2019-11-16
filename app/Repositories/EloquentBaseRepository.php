<?php

namespace App\Repositories;

use App\Repositories\BaseRepositoryInterface;
use App\BaseModel as Model;
use App\Event;

class EloquentBaseRepository implements BaseRepositoryInterface
{
    /** @var Model */
    protected $model;

    /**
     * EloquentBaseRepository Constructor
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /** @inheritDoc */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findBy($field, $value)
    {
        return $this->model->where($field, $value)->get();
    }

    public function latest($date = 'created_at')
    {
        return $this->model->latest($date)->get();
    }

    /** @inheritDoc */
    public function firstOrCreate(array $attributes, array $values): Model
    {
        return $this->model->firstOrCreate($attributes, $values);
    }

    /** @inheritDoc */
    public function update(Model $model, $attributes): Model
    {
        $model->update($attributes);
        return $model->fresh();
    }

    /** @inheritDoc */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /** @inheritDoc */
    public function forceDelete(Model $model): bool
    {
        return $model->forceDelete();
    }

    /** @inheritDoc */
    public function restore(Model $model): bool
    {
        return $model->restore();
    }
}
