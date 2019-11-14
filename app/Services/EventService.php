<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Repositories\EventRepositoryInterface;

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
        DB::beginTransaction();
        try {
            $isActive = '';
            if (array_key_exists('is_active', $validated)) {
                $isActive = 'is_active';
                $active = $this->repository->activeByAuthUser();
                $active->update(['is_active' => false]);
            }
            $event = $this->repository->firstOrCreate(Arr::only($validated, ['title', 'start_date', 'end_date']), Arr::only($validated, ['type', 'grouping', $isActive]));
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return $event;
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
