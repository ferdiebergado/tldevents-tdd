<?php

namespace Tests\Feature;

use App\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class EventManagementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testAnEventCanBeCreated()
    {
        $response = $this->post('/events', $this->data());

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

    public function testAnEventTitleShouldBeAtLeastTwoCharacters()
    {
        $response = $this->post('/events', array_merge($this->data(), ['title' => 'T']));

        $response->assertSessionHasErrors('title');
    }

    public function testAnEventTitleShouldNotExceedMaxCharacters()
    {
        $response = $this->post('/events', array_merge($this->data(), ['title' => $this->faker->realText(300)]));

        $response->assertSessionHasErrors('title');
    }

    public function testAnEventStartDateIsRequired()
    {
        $response = $this->post('/events', array_merge($this->data(), ['start_date' => '']));

        $response->assertSessionHasErrors('start_date');
    }

    public function testAnEventEndDateIsRequired()
    {
        $response = $this->post('/events', array_merge($this->data(), ['end_date' => '']));

        $response->assertSessionHasErrors('end_date');
    }

    public function testAnEventTypeIsRequired()
    {
        $response = $this->post('/events', array_merge($this->data(), ['type' => '']));

        $response->assertSessionHasErrors('type');
    }

    public function testAnEventTypeIsWithinTheSpecifiedValue()
    {
        $response = $this->post('/events', array_merge($this->data(), ['type' => 'X']));

        $response->assertSessionHasErrors('type');
    }

    public function testAnEventGroupingIsRequired()
    {
        $response = $this->post('/events', array_merge($this->data(), ['grouping' => '']));

        $response->assertSessionHasErrors('grouping');
    }

    public function testAnEventGroupingIsWithinTheSpecifiedValue()
    {
        $response = $this->post('/events', array_merge($this->data(), ['grouping' => 'X']));

        $response->assertSessionHasErrors('grouping');
    }

    public function testAnEventStartDateIsADate()
    {
        $response = $this->post('/events', array_merge($this->data(), ['start_date' => 'notadate2019']));

        $response->assertSessionHasErrors('start_date');
    }

    public function testAnEventEndDateIsADate()
    {
        $response = $this->post('/events', array_merge($this->data(), ['end_date' => 'notadate2019']));

        $response->assertSessionHasErrors('end_date');
    }

    public function testAnEventEndDateMustBeGreaterThanOrEqualToStartDate()
    {
        $response = $this->post('/events', array_merge($this->data(), ['end_date' => now()->subDays(4)->toDateString()]));

        $response->assertSessionHasErrors('end_date');
    }

    public function testAnEventCanBeUpdated()
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

    public function testAnEventCanBeViewed()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $response = $this->get('/events/' . $event->id);

        foreach ($this->data() as $key => $value) {
            $response->assertSee($event->$key);
            $this->assertEquals($value, $event->$key);
        }
    }

    public function testAnEventCanBeDeleted()
    {
        $this->post('/events', $this->data());

        $event = Event::first();

        $response = $this->delete('/events/' . $event->id);

        $this->assertEquals(0, Event::all()->count());

        $response->assertRedirect('/events');

        $response->assertSessionHas('success');
        $this->assertEquals('Event deleted.', session('success'));
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
