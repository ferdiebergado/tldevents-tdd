<?php

namespace App\Http\Controllers;

use App\Participant;
use App\Http\Requests\ParticipantRequest;

class ParticipantController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Participant::class, 'participant');
    }

    public function store(ParticipantRequest $request)
    {
        $participant = Participant::firstOrCreate($request->only(['last_name', 'first_name', 'mi', 'sex']), $request->only(['station', 'mobile', 'email']));

        session()->flash('success', 'Participant saved.');

        return redirect('/participants/' . $participant->id);
    }

    public function update(Participant $participant, ParticipantRequest $request)
    {
        $participant->update($request->all());

        session()->flash('success', 'Participant updated.');

        return redirect('/participants/' . $participant->id);
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();

        session()->flash('success', 'Participant deleted.');

        return redirect('/participants');
    }

    public function forceDestroy(Participant $participant)
    {
        $this->authorize('forceDelete', $participant);

        if ($participant->forceDelete()) {

            session()->flash('success', 'Participant permanently deleted.');

            return redirect('/participants');
        }
    }

    public function restore(int $id)
    {
        $participant = Participant::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $participant);

        if ($participant->restore()) {

            session()->flash('success', 'Participant restored.');

            return redirect('/participants');
        }
    }
}
