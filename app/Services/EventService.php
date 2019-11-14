<?php

namespace App\Services;

use App\Repositories\EventRepositoryInterface;
use Illuminate\Support\Arr;

class EventService
{
    protected $repository;

    public function __construct(EventRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function showAll()
    {
        return $this->repository->latest();
    }

    public function show($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $validated)
    {
        return $this->repository->firstOrCreate(Arr::only($validated, ['title', 'start_date', 'end_date']), Arr::only($validated, ['type', 'grouping']));
    }

    public function update(int $id, array $validated)
    {
        return $this->repository->update($id, $validated);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
