<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\EventRequest;

class EventController extends Controller
{
    public function store(EventRequest $request)
    {
        $event = Event::create($request->all());

        session()->flash('success', 'Event saved.');

        return redirect('/events/' . $event->id);
    }

    public function update(Event $event, EventRequest $request)
    {
        $event->update($request->all());

        session()->flash('info', 'Event updated.');

        return redirect('/events/' . $event->id);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        session()->flash('success', 'Event deleted.');

        return redirect('/events');
    }

    public function show(Event $event)
    {
        return $event;
    }
}
