<?php

namespace Tests\Unit\Repositories;

use App\Event;
use Tests\TestCase;
use App\Repositories\EloquentCachedRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

class EloquentCachedRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @var \App\Repositories\EloquentCachedRepository */
    private $repo;

    private $cachePrefix = 'events_';

    /** @var string */
    private $cacheKeyLatest = 'events_latest';

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new EloquentCachedRepository(new Event());
        cache()->flush();
    }

    public function testFind()
    {
        $event = factory(Event::class)->create();

        $key = $this->cachePrefix . $event->id;

        $this->assertTrue(cache()->missing($key));

        $this->repo->find($event->id);

        $this->assertTrue(cache()->has($key));

        $cached = $this->repo->find($event->id);

        $this->assertEquals($event->title, $cached->title);
        $this->assertEquals($event->start_date, $cached->start_date);
        $this->assertEquals($event->end_date, $cached->end_date);
        $this->assertEquals($event->type, $cached->type);
        $this->assertEquals($event->grouping, $cached->grouping);
    }

    public function testLatest()
    {
        factory(Event::class, 5)->create();

        $this->assertTrue(cache()->missing($this->cacheKeyLatest));

        $this->repo->latest();

        $this->assertTrue(cache()->has($this->cacheKeyLatest));

        $latest = $this->repo->latest();

        $this->assertCount(5, $latest);
    }

    public function testFirstOrCreate()
    {
        factory(Event::class, 5)->create();

        $this->assertTrue(cache()->missing($this->cacheKeyLatest));

        $this->repo->latest();

        $this->assertTrue(cache()->has($this->cacheKeyLatest));

        $latest = $this->repo->latest();

        $this->assertCount(5, $latest);

        $new = factory(Event::class)->make();

        $arrayNew = $new->toArray();

        $created = $this->repo->firstOrCreate(Arr::only($arrayNew, ['title', 'start_date', 'end_date']), Arr::only($arrayNew, ['type', 'grouping']));

        $keyCreated = $this->cachePrefix . $created->id;

        $this->assertFalse(cache()->has($this->cacheKeyLatest));

        $this->assertTrue(cache()->has($keyCreated));

        $this->assertEquals($new->title, $created->title);
        $this->assertEquals($new->start_date, $created->start_date);
        $this->assertEquals($new->end_date, $created->end_date);
        $this->assertEquals($new->type, $created->type);
        $this->assertEquals($new->grouping, $created->grouping);
    }

    /** @group update */
    public function testUpdate()
    {
        $new = factory(Event::class)->make();

        $arrayNew = $new->toArray();

        $created = $this->repo->firstOrCreate(Arr::only($arrayNew, ['title', 'start_date', 'end_date']), Arr::only($arrayNew, ['type', 'grouping']));

        $key = $this->cachePrefix . $created->id;

        $title = 'Revised title';

        $current = cache($key);

        $this->assertNotEquals($current->title, $title);

        $updated = $this->repo->update($created, compact('title'));

        $this->assertEquals($title, $updated->title);
    }

    public function testDelete()
    {
        $new = factory(Event::class)->make();

        $arrayNew = $new->toArray();

        $created = $this->repo->firstOrCreate(Arr::only($arrayNew, ['title', 'start_date', 'end_date']), Arr::only($arrayNew, ['type', 'grouping']));

        $keyCreated = $this->cachePrefix . $created->id;

        $this->assertTrue(cache()->has($keyCreated));

        factory(Event::class, 5)->create();

        $this->assertTrue(cache()->missing($this->cacheKeyLatest));

        $this->repo->latest();

        $this->assertTrue(cache()->has($this->cacheKeyLatest));

        $this->repo->delete($created);

        $this->assertFalse(cache()->has($keyCreated));
        $this->assertFalse(cache()->has($this->cacheKeyLatest));

        $this->assertSoftDeleted('events', ['id' => $created->id]);
    }
}
