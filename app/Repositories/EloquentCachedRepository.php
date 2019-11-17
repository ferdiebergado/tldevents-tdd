<?php

namespace App\Repositories;

use App\BaseModel as Model;
use App\Repositories\BaseRepositoryInterface;

class EloquentCachedRepository implements BaseRepositoryInterface
{
    /** @var Model */
    protected $model;

    /** @var int */
    protected $cacheTimeout = 60;

    /** @var string */
    protected $cachePrefix;

    /** @var string */
    protected $cacheKeyLatest;

    /**
     * EloquentBaseRepository Constructor
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->cachePrefix = $this->model->getTable() . '_';
        $this->cacheKeyLatest = $this->cachePrefix . 'latest';
    }

    /** @inheritDoc */
    public function find(int $id): ?Model
    {
        return cache()->remember($this->cachePrefix . $id, $this->cacheTimeout, function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function findBy($field, $value)
    {
        return $this->model->where($field, $value)->get();
    }

    public function latest($date = 'created_at')
    {
        return cache()->remember($this->cacheKeyLatest, $this->cacheTimeout, function () use ($date) {
            return $this->model->latest($date)->get();
        });
    }

    /** @inheritDoc */
    public function firstOrCreate(array $attributes, array $values): Model
    {
        $created = $this->model->firstOrCreate($attributes, $values);
        cache()->forget($this->cacheKeyLatest);
        return cache()->remember($this->cachePrefix . $created->id, $this->cacheTimeout, function () use ($created) {
            return $created;
        });
    }

    /** @inheritDoc */
    public function update(Model $model, $attributes): Model
    {
        $model->update($attributes);
        cache()->forget($this->cacheKeyLatest);
        return cache()->remember($this->cachePrefix . $model->id, $this->cacheTimeout, function () use ($model) {
            return $model;
        });
    }

    /** @inheritDoc */
    public function delete(Model $model): bool
    {
        cache()->forget($this->cachePrefix . $model->id);
        cache()->forget($this->cacheKeyLatest);
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
        cache()->forget($this->cacheKeyLatest);
        return $model->restore();
    }
}
