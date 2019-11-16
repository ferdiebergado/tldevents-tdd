<?php

declare(strict_types=1);

namespace App\Services;

use App\Event;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Repositories\Event\EventRepositoryInterface;
use App\BaseModel as Model;
use Illuminate\Database\Eloquent\Collection;

class EventService
{
    /** @var \App\Repositories\EventRepositoryInterface */
    protected $repository;

    /**
     * EventService Constructor
     *
     * @param EventRepositoryInterface $repository
     */
    public function __construct(EventRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Show all events.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function showAll(): Collection
    {
        return $this->repository->latest();
    }

    /**
     * Show an event
     *
     * @param integer $id
     * @return Model|null
     */
    public function show(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * Create an event if not exists
     *
     * @param array $validated
     * @return Model
     */
    public function create(array $validated): Model
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

    /**
     * Update an event
     *
     * @param Event $event
     * @param array $validated
     * @return Model
     */
    public function update(Event $event, array $validated): Model
    {
        return $this->repository->update($event, $validated);
    }

    /**
     * Delete an event.
     *
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return bool
     */
    public function delete(Event $event): bool
    {
        return $this->repository->delete($event);
    }

    /**
     * Permanently delete an event
     *
     * @param Event $event
     * @return boolean
     */
    public function forceDestroy(Event $event): bool
    {
        return $this->repository->forceDelete($event);
    }

    /**
     * Restore a soft-deleted event
     *
     * @param Event $event
     * @return boolean
     */
    public function restore(Event $event): bool
    {
        return $this->repository->restore($event);
    }
}
