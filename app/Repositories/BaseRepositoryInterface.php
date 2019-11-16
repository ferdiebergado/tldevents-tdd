<?php

declare(strict_types=1);

namespace App\Repositories;

use App\BaseModel as Model;

interface BaseRepositoryInterface
{
    /**
     * Find a model by its primary key
     *
     * @param  int  $id
     * @return Model
     */
    public function find(int $id): ?Model;

    public function findBy(string $field, $value);

    public function latest(string $date);

    /**
     * Get the first record matching the attributes or create it.
     *
     * @param array $attributes
     * @param array $values
     * @return Model
     */
    public function firstOrCreate(array $attributes, array $values): Model;

    /**
     * Update a model
     *
     * @param Model $model
     * @param array $attributes
     * @return Model
     */
    public function update(Model $model, array $attributes): Model;

    /**
     * Delete a model
     *
     * @param Model $model
     * @return boolean
     */
    public function delete(Model $model): bool;

    /**
     * Permanently delete an event
     *
     * @param Model $model
     * @return boolean
     */
    public function forceDelete(Model $model): bool;

    /**
     * Restore a soft-deleted model instance.
     * 
     * @param int $id
     * @return bool|null
     */
    public function restore(Model $model): bool;
}
