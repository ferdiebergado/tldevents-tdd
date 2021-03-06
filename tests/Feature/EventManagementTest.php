<?php

namespace Tests\Feature;

use App\User;
use App\Event;
use Tests\Feature\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class EventManagementTest extends TestCase
{
    use WithFaker;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->states(['active', 'encoder'])->create();
        $this->actingAs($this->user);
    }

    public function testAnEventCannotBeCreatedByAGuestUser()
    {
        auth()->logout();

        $response = $this->post('/events', $this->data());

        $response->assertRedirect('/login');
    }

    public function testAnEventCannotBeCreatedByAnUnverifiedUser()
    {
        auth()->logout();

        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->post('/events', $this->data());

        $response->assertRedirect('/email/verify');
    }

    public function testAnEventCannotBeCreatedByAnInactiveUser()
    {
        auth()->logout();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/events', $this->data());

        $this->assertCount(0, Event::all());
        $response->assertRedirect('/login');
    }

    public function testAnEventCanBeCreatedByAVerifiedAndActiveEncoder()
    {
        $response = $this->post('/events', $this->data());

        $event = Event::first();

        $this->assertCount(1, Event::all());
        $response->assertRedirect('/events/' . $event->id);
        $response->assertSessionHas('success');
        $this->assertEquals('Event saved.', session('success'));
    }

    public function testAnEventCanBeCreatedByAnAdminUser()
    {
        auth()->logout();

        $admin = factory(User::class)->states(['active', 'admin'])->create();

        $response = $this->actingAs($admin)->post('/events', $this->data());

        $event = Event::first();

        $this->assertCount(1, Event::all());
        $response->assertRedirect('/events/' . $event->id);
        $response->assertSessionHas('success');
        $this->assertEquals('Event saved.', session('success'));
    }

    public function testAnEventTitleIsRequired()
    {
        $response = $this->post('/events', array_merge($this->data(), ['title' => '']));

        $response->assertSessionHasErrors('title');
    }

    public function testAnEventTitleIsRequiredOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['title' => '']));

        $response->assertSessionHasErrors('title');
    }

    public function testAnEventTitleShouldBeAtLeastTwoCharacters()
    {
        $response = $this->post('/events', array_merge($this->data(), ['title' => 'T']));

        $response->assertSessionHasErrors('title');
    }

    public function testAnEventTitleShouldBeAtLeastTwoCharactersOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['title' => 'T']));

        $response->assertSessionHasErrors('title');
    }

    public function testAnEventTitleShouldNotExceedMaxCharacters()
    {
        $response = $this->post('/events', array_merge($this->data(), ['title' => $this->faker->realText(300)]));

        $response->assertSessionHasErrors('title');
    }

    public function testAnEventTitleShouldNotExceedMaxCharactersOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['title' => $this->faker->realText(300)]));

        $response->assertSessionHasErrors('title');
    }

    public function testAnEventStartDateIsRequired()
    {
        $response = $this->post('/events', array_merge($this->data(), ['start_date' => '']));

        $response->assertSessionHasErrors('start_date');
    }

    public function testAnEventStartDateIsRequiredOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['start_date' => '']));

        $response->assertSessionHasErrors('start_date');
    }

    public function testAnEventEndDateIsRequired()
    {
        $response = $this->post('/events', array_merge($this->data(), ['end_date' => '']));

        $response->assertSessionHasErrors('end_date');
    }

    public function testAnEventEndDateIsRequiredOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['end_date' => '']));

        $response->assertSessionHasErrors('end_date');
    }

    public function testAnEventTypeIsRequired()
    {
        $response = $this->post('/events', array_merge($this->data(), ['type' => '']));

        $response->assertSessionHasErrors('type');
    }

    public function testAnEventTypeIsRequiredOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['type' => '']));

        $response->assertSessionHasErrors('type');
    }

    public function testAnEventTypeIsWithinTheSpecifiedValue()
    {
        $response = $this->post('/events', array_merge($this->data(), ['type' => 'X']));

        $response->assertSessionHasErrors('type');
    }

    public function testAnEventTypeIsWithinTheSpecifiedValueOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['type' => 'X']));

        $response->assertSessionHasErrors('type');
    }

    public function testAnEventGroupingIsRequired()
    {
        $response = $this->post('/events', array_merge($this->data(), ['grouping' => '']));

        $response->assertSessionHasErrors('grouping');
    }

    public function testAnEventGroupingIsRequiredOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['grouping' => '']));

        $response->assertSessionHasErrors('grouping');
    }

    public function testAnEventGroupingIsWithinTheSpecifiedValue()
    {
        $response = $this->post('/events', array_merge($this->data(), ['grouping' => 'X']));

        $response->assertSessionHasErrors('grouping');
    }

    public function testAnEventGroupingIsWithinTheSpecifiedValueOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['grouping' => 'X']));

        $response->assertSessionHasErrors('grouping');
    }

    public function testAnEventStartDateIsADate()
    {
        $response = $this->post('/events', array_merge($this->data(), ['start_date' => 'notadate2019']));

        $response->assertSessionHasErrors('start_date');
    }

    public function testAnEventStartDateIsADateOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['start_date' => 'notadate2019']));

        $response->assertSessionHasErrors('start_date');
    }

    public function testAnEventEndDateIsADate()
    {
        $response = $this->post('/events', array_merge($this->data(), ['end_date' => 'notadate2019']));

        $response->assertSessionHasErrors('end_date');
    }

    public function testAnEventEndDateIsADateOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['end_date' => 'notadate2019']));

        $response->assertSessionHasErrors('end_date');
    }

    public function testAnEventEndDateMustBeGreaterThanOrEqualToStartDate()
    {
        $response = $this->post('/events', array_merge($this->data(), ['end_date' => now()->subDays(4)->toDateString()]));

        $response->assertSessionHasErrors('end_date');
    }

    public function testAnEventEndDateMustBeGreaterThanOrEqualToStartDateOnUpdate()
    {
        $event = factory(Event::class)->create();

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), ['end_date' => now()->subDays(4)->toDateString()]));

        $response->assertSessionHasErrors('end_date');
    }

    public function testNoDuplicateEventIsCreated()
    {
        for ($i = 0; $i < 2; $i++) {
            $this->post('/events', $this->data());
        }

        $this->assertEquals(1, Event::all()->count());
    }

    public function testAnEventCannotBeUpdatedByAnUnverifiedUser()
    {
        $this->post('/events', $this->data());

        auth()->logout();

        $user = factory(User::class)->create(['email_verified_at' => null]);

        $event = Event::first();

        $updates = [
            'title' => 'Updated title'
        ];

        $response = $this->actingAs($user)->put('/events/' . $event->id, array_merge($this->data(), $updates));

        $this->assertEquals($event->title, $event->fresh()->title);

        $response->assertRedirect('/email/verify');
    }

    /** @group update */
    public function testAnEventCanBeUpdatedByAnActiveEncoder()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $updates = [
            'title' => 'Updated title'
        ];

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), $updates));

        $this->assertEquals($updates['title'], $event->fresh()->title);

        $response->assertRedirect('/events/' . $event->id);
        $response->assertSessionHas('info');
        $this->assertEquals('Event updated.', session('info'));
    }

    /** @group update */
    public function testAnEventCanBeUpdatedByAnActiveAdmin()
    {
        auth()->logout();

        $user = factory(User::class)->states(['active', 'admin'])->create();

        $this->actingAs($user)->post('/events', $this->data());

        $event = Event::first();

        $updates = [
            'title' => 'Updated title'
        ];

        $response = $this->put('/events/' . $event->id, array_merge($this->data(), $updates));

        $this->assertEquals($updates['title'], $event->fresh()->title);

        $response->assertRedirect('/events/' . $event->id);
        $response->assertSessionHas('info');
        $this->assertEquals('Event updated.', session('info'));
    }

    public function testReturnsNotFoundStatusWhenUpdatingANonExistingEvent()
    {
        $updates = [
            'title' => 'Updated title'
        ];

        $response = $this->put('/events/9999999', array_merge($this->data(), $updates));

        $response->assertNotFound();
    }

    public function testAnEventCanBeViewedByAnActiveEncoder()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $response = $this->get('/events/' . $event->id);

        foreach ($this->data() as $key => $value) {
            $response->assertSee($event->$key);
            $this->assertEquals($value, $event->$key);
        }
    }

    public function testAnEventCanBeViewedByAnActiveAdmin()
    {
        auth()->logout();

        $user = factory(User::class)->states(['active', 'admin'])->create();

        $this->actingAs($user)->post('/events', $this->data());

        $event = Event::first();

        $response = $this->get('/events/' . $event->id);

        foreach ($this->data() as $key => $value) {
            $response->assertSee($event->$key);
            $this->assertEquals($value, $event->$key);
        }
    }

    public function testAnEventCanBeViewedByAnActiveUser()
    {
        $this->post('/events', $this->data());

        auth()->logout();

        $user = factory(User::class)->state('active')->create();

        $event = Event::first();

        $response = $this->actingAs($user)->get('/events/' . $event->id);

        foreach ($this->data() as $key => $value) {
            $response->assertSee($event->$key);
            $this->assertEquals($value, $event->$key);
        }
    }

    public function testReturnsNotFoundStatusWhenNonExistingEventIsViewed()
    {
        $response = $this->get('/events/999');

        $response->assertNotFound();
    }

    public function testEventsCanBeFetchedAsJsonByAnActiveEncoder()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/events', $this->fakeData());
        }

        $response = $this->json('GET', '/events');

        $response->assertJsonCount(5, 'data');
    }

    public function testEventsCanBeFetchedAsJsonByAnActiveAdmin()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/events', $this->fakeData());
        }

        auth()->logout();

        $user = factory(User::class)->states(['active', 'admin'])->create();

        $response = $this->actingAs($user)->json('GET', '/events');

        $response->assertJsonCount(5, 'data');
    }

    public function testEventsCanBeFetchedAsJsonByAnActiveUser()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/events', $this->fakeData());
        }

        auth()->logout();

        $user = factory(User::class)->state('active')->create();

        $response = $this->actingAs($user)->json('GET', '/events');

        $response->assertJsonCount(5, 'data');
    }

    /** @group delete */
    public function testAnActiveEncoderCanDeleteHisOwnEvent()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $response = $this->delete('/events/' . $event->id);

        $this->assertEquals(0, Event::all()->count());

        $response->assertRedirect('/events');

        $response->assertSessionHas('success');
        $this->assertEquals('Event deleted.', session('success'));
    }

    public function testAnActiveEncoderCannotDeleteAnotherUsersEvent()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        auth()->logout();

        $encoder2 = factory(User::class)->states(['active', 'encoder'])->create();

        $response = $this->actingAs($encoder2)->delete('/events/' . $event->id);

        $response->assertStatus(403);

        $this->assertEquals(1, Event::all()->count());
    }

    /** @group delete */
    public function testAnEventCanBeDeletedByAnActiveAdmin()
    {
        auth()->logout();

        $user = factory(User::class)->states(['active', 'admin'])->create();

        $this->actingAs($user)->post('/events', $this->data());

        $event = Event::first();

        $response = $this->delete('/events/' . $event->id);

        $this->assertEquals(0, Event::all()->count());

        $response->assertRedirect('/events');

        $response->assertSessionHas('success');
        $this->assertEquals('Event deleted.', session('success'));
    }

    public function testReturnsANotFoundStatusWhenDeletingANonExistingEvent()
    {
        $this->post('/events', $this->data());

        Event::first();

        $response = $this->delete('/events/999999999');

        $response->assertNotFound();
    }

    public function testAnEventCanBeForcedDeletedByAnActiveAdmin()
    {
        auth()->logout();

        $user = factory(User::class)->states(['active', 'admin'])->create();

        $this->actingAs($user)->post('/events', $this->data());

        $event = Event::first();

        $response = $this->delete('/events/' . $event->id . '/force');

        $this->assertEquals(0, Event::all()->count());

        $response->assertRedirect('/events');

        $response->assertSessionHas('success');
        $this->assertEquals('Event permanently deleted.', session('success'));
    }

    public function testAnEventCanNotBeForcedDeletedByAnActiveEncoder()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $response = $this->delete('/events/' . $event->id . '/force');

        $response->assertStatus(403);

        $this->assertEquals(1, Event::all()->count());
    }

    public function testReturnsANotFoundStatusWhenForceDeletingANonExistingEvent()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $response = $this->delete('/events/999999999/force');

        $response->assertNotFound();
    }

    public function testAnEventCanBeRestoredByAnActiveAdmin()
    {
        auth()->logout();

        $user = factory(User::class)->states(['active', 'admin'])->create();

        $this->actingAs($user);

        $this->post('/events', $this->data());

        $event = Event::first();

        $this->delete('/events/' . $event->id);

        $response = $this->post('/events/' . $event->id . '/restore');

        $this->assertEquals(1, Event::all()->count());

        $response->assertRedirect('/events');

        $response->assertSessionHas('success');

        $this->assertEquals('Event restored.', session('success'));
    }

    public function testAnActiveEncoderCanRestoreHisOwnEvent()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $this->delete('/events/' . $event->id);

        $response = $this->post('/events/' . $event->id . '/restore');

        $response->assertRedirect('/events');

        $this->assertEquals(1, Event::all()->count());

        $response->assertSessionHas('success');

        $this->assertEquals('Event restored.', session('success'));
    }

    public function testReturnsANotFoundStatusWhenRestoringANonExistingEvent()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $this->delete('/events/' . $event->id);

        $response = $this->post('/events/999999999/restore');

        $response->assertNotFound();
    }

    public function testAnActiveEncoderCannotRestoreAnotherEncodersEvent()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $this->delete('/events/' . $event->id);

        auth()->logout();

        $encoder1 = factory(User::class)->states(['active', 'encoder'])->create();

        $response = $this->actingAs($encoder1)->post('/events/' . $event->id . '/restore');

        $response->assertStatus(403);

        $this->assertEquals(0, Event::all()->count());
    }

    public function testAnEventCannotBeDeletedByAnActiveUser()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $user = factory(User::class)->state('active')->create();

        $response = $this->actingAs($user)->delete('/events/' . $event->id);

        $response->assertStatus(403);

        $this->assertEquals(1, Event::all()->count());
    }

    public function testOnlyOneEventIsActivatedByAUser()
    {
        factory(Event::class, 5)->create();
        factory(Event::class)->create(['is_active' => true]);
        $event = factory(Event::class)->make(['is_active' => true]);

        $this->post('/events', $event->toArray());

        $this->assertCount(1, Event::whereIsActive(true)->whereCreatedBy($this->user->id)->get());
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

    public function fakeData()
    {
        return [
            'title' => $this->faker->text,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays($this->faker->numberBetween(1, 30))->toDateString(),
            'type' => $this->faker->randomElement(['W', 'T', 'C']),
            'grouping' => $this->faker->randomElement(['R', 'L', 'M', 'N'])
        ];
    }
}
