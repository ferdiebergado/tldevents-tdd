<?php

namespace Tests\Unit;

use App\Event;
use App\Repositories\EloquentBaseRepository;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class EloquentBaseRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @var \App\Repositories\EloquentBaseRepository */
    private $repo;

    /** @var \App\Event */
    private $event;

    /** @var \App\User */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new EloquentBaseRepository(new Event());

        $this->user = factory(User::class)->states(['active', 'encoder'])->create();
        $this->actingAs($this->user);
        $this->event = factory(Event::class)->create();
    }

    public function testFind()
    {
        $event = $this->repo->find($this->event->id);

        foreach ($this->data()['columns'] as $column) {
            $this->assertEquals($this->event->$column, $event->$column);
        }
    }

    public function testUpdate()
    {
        $update = [
            'title' => $this->faker->text
        ];

        $event = $this->repo->update($this->event, $update);

        $this->assertEquals($update['title'], $event->title);
    }

    public function testDelete()
    {
        $deleted = $this->repo->delete($this->event);

        $this->assertTrue($deleted);

        $this->assertSoftDeleted('events', Arr::except($this->event->toArray(), $this->data()['excluded']));

        $this->assertEquals($this->user->id, Event::withTrashed()->first()->deleted_by);
    }

    public function data()
    {
        return [
            'columns' => [
                'title',
                'start_date',
                'end_date',
                'type',
                'grouping'
            ],
            'excluded' => [
                'deleted_at',
                'deleted_by',
                'type_name',
                'type_keys',
                'type_names',
                'grouping_name',
                'grouping_keys',
                'grouping_names'
            ]
        ];
    }
}
