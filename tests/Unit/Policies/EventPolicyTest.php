<?php

namespace Tests\Unit\Policies;

use App\Event;
use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\User */
    private $encoder;

    private $user;

    private $admin;

    /** @var \App\Event */
    private $event;

    /** @inheritDoc */
    protected function setUp(): void
    {
        parent::setUp();
        $this->encoder = factory(User::class)->states(['active', 'encoder'])->create();
        $this->user = factory(User::class)->state('active')->create();
        $this->admin = factory(User::class)->state('admin')->create();
        $this->event = factory(Event::class)->create();
    }

    /**
     * Test if an encoder can create an event.
     *
     * @return void
     */
    public function testEncoderCanCreateAnEvent()
    {
        $this->assertTrue($this->encoder->can('create', new Event()));
    }

    /**
     * Test if an encoder can view an event.
     *
     * @return void
     */
    public function testEncoderCanViewAnEvent()
    {
        $this->assertTrue($this->encoder->can('view', $this->event));
    }

    /**
     * Test if an encoder can view any event.
     *
     * @return void
     */
    public function testEncoderCanViewAnyEvent()
    {
        $this->assertTrue($this->encoder->can('viewAny', $this->event));
    }

    /**
     * Test if an encoder can update an event.
     *
     * @return void
     */
    public function testEncoderCanUpdateAnEvent()
    {
        $this->assertTrue($this->encoder->can('update', $this->event));
    }

    /**
     * Test if an encoder can delete his own event.
     *
     * @return void
     */
    public function testEncoderCanDeleteHisOwnEvent()
    {
        $this->actingAs($this->encoder);
        $event = factory(Event::class)->create();

        $this->assertTrue($this->encoder->can('delete', $event));
    }

    /**
     * Test if an encoder can delete his own event.
     *
     * @return void
     */
    public function testEncoderCanRestoreHisOwnEvent()
    {
        $this->actingAs($this->encoder);
        $event = factory(Event::class)->create();

        $event->delete();

        $this->assertTrue($this->encoder->can('restore', $event));
    }

    /**
     * Test if an encoder cannot restore other user's event.
     *
     * @return void
     */
    public function testEncoderCannotRestoreOtherEncodersEvent()
    {
        $user = factory(User::class)->states(['active', 'encoder'])->create();

        $this->actingAs($user);
        $event = factory(Event::class)->create();

        $event->delete();

        $this->assertFalse($this->encoder->can('restore', $event));
    }

    /**
     * Test if an encoder can delete another user's event.
     *
     * @return void
     */
    public function testEncoderCannotDeleteAnotherUsersEvent()
    {
        $this->actingAs($this->user);
        $event = factory(Event::class)->create();

        $this->assertFalse($this->encoder->can('delete', $event));
    }

    /**
     * Test if a user cannot create an event.
     *
     * @return void
     */
    public function testUserCannotCreateAnEvent()
    {
        $this->assertFalse($this->user->can('create', new Event()));
    }

    /**
     * Test if a user can view an event.
     *
     * @return void
     */
    public function testUserCanViewAnEvent()
    {
        $this->assertTrue($this->user->can('view', $this->event));
    }

    /**
     * Test if a user can view any event.
     *
     * @return void
     */
    public function testUserCanViewAnyEvent()
    {
        $this->assertTrue($this->user->can('viewAny', $this->event));
    }

    /**
     * Test if a user cannot update an event.
     *
     * @return void
     */
    public function testUserCannotUpdateAnEvent()
    {
        $this->assertFalse($this->user->can('update', $this->event));
    }

    /**
     * Test if a user cannot delete an event.
     *
     * @return void
     */
    public function testUserCannotDeleteAnEvent()
    {
        $this->assertFalse($this->user->can('delete', $this->event));
    }

    /**
     * Test if a user cannot permanently delete an event.
     *
     * @return void
     */
    public function testUserCannotForceDeleteAnEvent()
    {
        $this->assertFalse($this->user->can('forceDelete', $this->event));
    }

    /**
     * Test if an admin can create an event.
     *
     * @return void
     */
    public function testAdminCanCreateAnEvent()
    {
        $this->assertTrue($this->admin->can('create', new Event()));
    }

    /**
     * Test if an admin can view an event.
     *
     * @return void
     */
    public function testAdminCanViewAnEvent()
    {
        $this->assertTrue($this->admin->can('view', $this->event));
    }

    /**
     * Test if an admin can view any event.
     *
     * @return void
     */
    public function testAdminCanViewAnyParticipant()
    {
        $this->assertTrue($this->admin->can('viewAny', $this->event));
    }

    /**
     * Test if an admin can update an event.
     *
     * @return void
     */
    public function testAdminCanUpdateAnEvent()
    {
        $this->assertTrue($this->admin->can('update', $this->event));
    }

    /**
     * Test if an admin can delete an event.
     *
     * @return void
     */
    public function testAdminCanDeleteAnEvent()
    {
        $this->assertTrue($this->admin->can('delete', $this->event));
    }

    /**
     * Test if an admin can permanently delete an event.
     *
     * @return void
     */
    public function testAdminCanForceDeleteAnEvent()
    {
        $this->assertTrue($this->admin->can('forceDelete', $this->event));
    }
}
