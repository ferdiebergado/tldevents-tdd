<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\EventRequest;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $service;

    public function __construct(EventService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['data' => $this->service->showAll()]);
        }
    }

    public function store(EventRequest $request)
    {
        $event = $this->service->create($request->validated());

        session()->flash('success', 'Event saved.');

        return redirect('/events/' . $event->id);
    }

    public function update(Event $event, EventRequest $request)
    {
        $this->service->update($event->id, $request->validated());

        session()->flash('info', 'Event updated.');

        return redirect('/events/' . $event->id);
    }

    public function destroy(Event $event)
    {
        $this->service->delete($event->id);

        session()->flash('success', 'Event deleted.');

        return redirect('/events');
    }

    public function show(Event $event)
    {
        return $this->service->show($event->id);
    }
}
