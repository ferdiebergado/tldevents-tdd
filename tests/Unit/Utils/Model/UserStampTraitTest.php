<?php

namespace Tests\Unit\Utils\Models;

use App\User;
use App\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserStampTraitTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }

    /**
     * Test userstamps are created on new model creation.
     *
     * @return void
     */
    public function testCreating()
    {
        $event = factory(Event::class)->create();

        $this->assertEquals($this->user->id, $event->created_by);
        $this->assertInstanceOf(User::class, $event->creator);
        $this->assertEquals($this->user->name, $event->creator->name);
        $this->assertEquals($this->user->id, $event->updated_by);
        $this->assertInstanceOf(User::class, $event->editor);
        $this->assertEquals($this->user->name, $event->editor->name);
    }

    /**
     * Test userstamps are updated on model update.
     *
     * @return void
     */
    public function testUpdating()
    {
        $event = factory(Event::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $event->update(['title' => $this->faker->word]);

        $this->assertEquals($user->id, $event->fresh()->updated_by);
        $this->assertEquals($user->name, $event->editor->name);
    }

    /**
     * Test userstamps are updated on model delete.
     *
     * @return void
     */
    public function testDeleting()
    {
        $event = factory(event::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $event->delete();

        $this->assertEquals($user->id, $event->deleted_by);
        $this->assertEquals($user->name, $event->destroyer->name);
    }
}
