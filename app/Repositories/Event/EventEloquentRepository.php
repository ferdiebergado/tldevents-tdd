<?php

namespace App\Repositories\Event;

use App\Event;
use App\Repositories\EloquentCachedRepository;
use App\Repositories\Event\EventRepositoryInterface;

class EventEloquentRepository extends EloquentCachedRepository implements EventRepositoryInterface
{
    public function __construct(Event $event)
    {
        parent::__construct($event);
    }

    public function activeByAuthUser()
    {
        $user = auth()->id();
        return cache()->remember($this->cachePrefix . 'active_by_user_' . $user, $this->cacheTimeout, function () use ($user) {
            return $this->model->whereIsActive(true)->whereUpdatedBy($user)->latest()->first();
        });
    }
}
