<?php

namespace App\Repositories;

use App\Event;
use App\Repositories\EloquentBaseRepository;
use App\Repositories\EventRepositoryInterface;

class EventEloquentRepository extends EloquentBaseRepository implements EventRepositoryInterface
{
    public function __construct(Event $event)
    {
        parent::__construct($event);
    }
}
