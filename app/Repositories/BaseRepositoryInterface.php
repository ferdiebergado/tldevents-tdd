<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function find(int $id);

    public function findBy(string $field, $value);

    public function latest(string $date);

    public function firstOrCreate(array $attributesToCheck, array $attributes);

    public function update(int $id, array $attributes);

    public function delete(int $id);
}
