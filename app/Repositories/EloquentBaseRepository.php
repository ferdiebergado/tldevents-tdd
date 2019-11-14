<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepositoryInterface;

class EloquentBaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function find($id)
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

    public function firstOrCreate($attrToCheck, $attributes)
    {
        return $this->model->firstOrCreate($attrToCheck, $attributes);
    }

    public function update($id, $attributes)
    {
        $model = $this->find($id);
        $model->update($attributes);
        return $model->fresh();
    }

    public function delete($id)
    {
        $model = $this->find($id);
        return $model->delete();
    }
}
