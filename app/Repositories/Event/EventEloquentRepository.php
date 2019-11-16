<?php

namespace App\Repositories\Event;

use App\Event;
use App\Repositories\EloquentBaseRepository;
use App\Repositories\Event\EventRepositoryInterface;

class EventEloquentRepository extends EloquentBaseRepository implements EventRepositoryInterface
{
    public function __construct(Event $event)
    {
        parent::__construct($event);
    }

    public function activeByAuthUser()
    {
        return $this->model->whereIsActive(true)->whereCreatedBy(auth()->id())->latest()->first();
    }
}
