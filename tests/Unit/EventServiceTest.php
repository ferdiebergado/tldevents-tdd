<?php

namespace Tests\Unit;

use App\Event;
use Tests\TestCase;
use App\Services\EventService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventServiceTest extends TestCase
{
    use RefreshDatabase;

    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app()->make(EventService::class);
    }

    public function testShowAll()
    {
        factory(Event::class, 5)->create();

        $events = $this->service->showAll();

        $this->assertCount(5, $events);
        $this->assertInstanceOf(Collection::class, $events);
    }

    public function testShow()
    {
        $event = factory(Event::class)->create($this->data());

        $show = $this->service->show($event->id);

        $this->assertInstanceOf(Event::class, $event);

        foreach ($this->data() as $key => $value) {
            $this->assertEquals($value, $show->$key);
        }
    }

    public function data()
    {
        return [
            'title' => 'Example title',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'type' => 'W',
            'grouping' => 'R'
        ];
    }
}
