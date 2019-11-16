<?php

namespace Tests\Unit\Services;

use App\Event;
use Tests\TestCase;
use App\Services\EventService;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Services\EventService */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app()->make(EventService::class);
    }

    public function testCreate()
    {
        $this->service->create($this->data());

        $this->assertCount(1, Event::all());

        $event = Event::first();

        foreach ($this->data() as $key => $value) {
            $this->assertEquals($value, $event->$key);
        }
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

    public function testUpdate()
    {
        $event = factory(Event::class)->create();

        $title = 'Updated title';

        $this->assertNotEquals($title, $event->title);

        $updated = $this->service->update($event, compact('title'));

        $this->assertEquals($title, $updated->title);
    }

    public function testDelete()
    {
        $event = factory(Event::class)->create($this->data());

        $this->assertCount(1, Event::all());

        $deleted = $this->service->delete($event);

        $this->assertTrue($deleted);
        $this->assertCount(0, Event::all());
    }

    public function testRestore()
    {
        $event = factory(Event::class)->create();

        $user = factory(User::class)->states(['active', 'encoder'])->create();

        $this->actingAs($user);

        $this->service->delete($event);

        $this->assertCount(0, Event::all());

        $restored = $this->service->restore($event);

        $this->assertTrue($restored);
        $this->assertCount(1, Event::all());
    }

    public function testRestoreThrowsModelNotFoundExceptionOnNonExistingEvent()
    {
        $event = factory(Event::class)->create();

        $user = factory(User::class)->states(['active', 'encoder'])->create();

        $this->actingAs($user);

        $this->service->delete($event);

        $this->assertCount(0, Event::all());

        $restored = $this->service->restore($event);

        $this->assertTrue($restored);
        $this->assertCount(1, Event::all());
    }

    public function testForceDestroy()
    {
        $event = factory(Event::class)->create($this->data());

        $destroyed = $this->service->forceDestroy($event);

        $this->assertTrue($destroyed);
        $this->assertDatabaseMissing('events', array_merge(['id' => $event->id], $this->data()));
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
