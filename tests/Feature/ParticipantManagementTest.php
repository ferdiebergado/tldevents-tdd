<?php

namespace Tests\Feature;

use App\User;
use App\Participant;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\Feature\TestCase;

class ParticipantManagementTest extends TestCase
{
    use WithFaker;

    /** 
     * @group create 
     * @group crud
     */
    public function testAParticipantCanBeCreatedByAnEncoder()
    {
        $this->setUser();

        $response = $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $this->assertCount(1, Participant::all());

        $response->assertRedirect('/participants/' . Participant::first()->id);

        foreach ($this->participantData() as $key => $value) {
            $this->assertEquals($value, $participant->$key);
        }

        $response->assertSessionHas('success');

        $this->assertEquals('Participant saved.', session('success'));
    }

    /** 
     * @group create 
     * @group crud
     */
    public function testAParticipantCanBeCreatedByAnAdmin()
    {
        $this->setUser('admin');

        $response = $this->post('/participants', $this->participantData());

        $this->assertCount(1, Participant::all());

        $response->assertRedirect('/participants/' . Participant::first()->id);

        $response->assertSessionHas('success');

        $this->assertEquals('Participant saved.', session('success'));
    }

    /** 
     * @group create 
     * @group crud
     */
    public function testAParticipantCannotBeCreatedByAUser()
    {
        $this->setUser('user');

        $response = $this->post('/participants', $this->participantData());

        $response->assertStatus(403);

        $this->assertCount(0, Participant::all());
    }

    /** 
     * @group create 
     * @group crud
     */
    public function testAParticipantCannotBeCreatedByAGuest()
    {
        $response = $this->post('/participants', $this->participantData());

        $response->assertRedirect('/login');

        $this->assertCount(0, Participant::all());
    }

    /** 
     * @group create 
     * @group crud
     */
    public function testAParticipantCannotBeCreatedWhenIncompleteDataIsProvided()
    {
        $this->setUser();

        foreach (Arr::except($this->participantData(), ['station', 'email']) as $key => $value) {
            $response = $this->post('/participants', Arr::except($this->participantData(), [$key]));
            $response->assertSessionHasErrors($key);
            $this->assertCount(0, Participant::all());
        }
    }

    /** 
     * @group create 
     * @group crud
     * @group validation
     */
    public function testAParticipantCannotBeCreatedWhenLastNameExceedsMaxCharacters()
    {
        $this->setUser();

        $response = $this->post('/participants', array_merge($this->participantData(), ['last_name' => $this->faker->realText(300)]));
        $response->assertSessionHasErrors('last_name');
        $this->assertCount(0, Participant::all());
    }

    /** 
     * @group create 
     * @group crud
     * @group validation
     */
    public function testAParticipantCannotBeCreatedWhenFirstNameExceedsMaxCharacters()
    {
        $this->setUser();

        $response = $this->post('/participants', array_merge($this->participantData(), ['first_name' => $this->faker->realText(300)]));
        $response->assertSessionHasErrors('first_name');
        $this->assertCount(0, Participant::all());
    }

    /** 
     * @group create 
     * @group crud
     * @group validation
     */
    public function testAParticipantCannotBeCreatedWhenMiExceedsMaxCharacters()
    {
        $this->setUser();

        $response = $this->post('/participants', array_merge($this->participantData(), ['mi' => 'dela']));
        $response->assertSessionHasErrors('mi');
        $this->assertCount(0, Participant::all());
    }

    /** 
     * @group create 
     * @group crud
     * @group validation
     */
    public function testAParticipantCannotBeCreatedWhenSexIsInvalid()
    {
        $this->setUser();

        $response = $this->post('/participants', array_merge($this->participantData(), ['sex' => 'X']));
        $response->assertSessionHasErrors('sex');
        $this->assertCount(0, Participant::all());
    }

    /** 
     * @group create 
     * @group crud
     * @group validation
     */
    public function testAParticipantCannotBeCreatedWhenMobileExceedsMaxCharacters()
    {
        $this->setUser();

        $response = $this->post('/participants', array_merge($this->participantData(), ['mobile' => $this->faker->realText(300)]));
        $response->assertSessionHasErrors('mobile');
        $this->assertCount(0, Participant::all());
    }

    /** 
     * @group update
     * @group crud
     */
    public function testAParticipantCanBeUpdatedByAnEncoder()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $mobile = '639876543210';

        $response = $this->put('/participants/' . $participant->id, array_merge($this->participantData(), compact('mobile')));

        $this->assertEquals($mobile, $participant->fresh()->mobile);

        $response->assertRedirect('/participants/' . $participant->id);

        $response->assertSessionHas('success');

        $this->assertEquals('Participant updated.', session('success'));
    }

    /** 
     * @group update
     * @group crud
     */
    public function testAParticipantCanBeUpdatedByAnAdmin()
    {
        $this->setUser('admin');

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $mobile = '639876543210';

        $response = $this->put('/participants/' . $participant->id, array_merge($this->participantData(), compact('mobile')));

        $this->assertEquals($mobile, $participant->fresh()->mobile);

        $response->assertRedirect('/participants/' . $participant->id);

        $response->assertSessionHas('success');

        $this->assertEquals('Participant updated.', session('success'));
    }

    /** 
     * @group update
     * @group crud
     */
    public function testAParticipantCannotBeUpdatedByAUser()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $mobile = '639876543210';

        $this->setUser('user');

        $response = $this->put('/participants/' . $participant->id, array_merge($this->participantData(), compact('mobile')));

        $response->assertStatus(403);
    }

    /** 
     * @group update
     * @group crud
     */
    public function testAParticipantCannotBeUpdatedByAGuest()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $mobile = '639876543210';

        auth()->logout();

        $response = $this->put('/participants/' . $participant->id, array_merge($this->participantData(), compact('mobile')));

        $response->assertRedirect('/login');
    }

    /** 
     * @group update 
     * @group crud
     */
    public function testAParticipantCannotBeUpdatedWhenIncompleteDataIsProvided()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        foreach (Arr::except($this->participantData(), ['station', 'email']) as $key => $value) {
            $response = $this->put('/participants/' . $participant->id, Arr::except($this->participantData(), [$key]));
            $response->assertSessionHasErrors($key);
        }
    }

    /** 
     * @group create 
     * @group crud
     * @group validation
     */
    public function testAParticipantCannotBeUpdatedWhenLastNameExceedsMaxCharacters()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();
        $last_name = $this->faker->realText(300);
        $response = $this->post('/participants', array_merge($this->participantData(), compact('last_name')));
        $response->assertSessionHasErrors('last_name');
    }

    /** 
     * @group create 
     * @group crud
     * @group validation
     */
    public function testAParticipantCannotBeUpdatedWhenFirstNameExceedsMaxCharacters()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();
        $first_name = $this->faker->realText(300);
        $response = $this->post('/participants', array_merge($this->participantData(), compact('first_name')));
        $response->assertSessionHasErrors('first_name');
    }

    /** 
     * @group create 
     * @group crud
     * @group validation
     */
    public function testAParticipantCannotBeUpdatedWhenMiExceedsMaxCharacters()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $mi = 'dela';

        $response = $this->post('/participants', array_merge($this->participantData(), compact('mi')));
        $response->assertSessionHasErrors('mi');
    }

    /** 
     * @group create 
     * @group crud
     * @group validation
     */
    public function testAParticipantCannotBeUpdatedWhenSexIsInvalid()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $sex = 'X';

        $response = $this->post('/participants', array_merge($this->participantData(), compact('sex')));
        $response->assertSessionHasErrors('sex');
    }

    /** 
     * @group update 
     * @group crud
     * @group validation
     */
    public function testAParticipantCannotBeUpdatedWhenMobileExceedsMaxCharacters()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $mobile = $this->faker->realText(300);

        $response = $this->put('/participants/' . $participant->id, array_merge($this->participantData(), compact('mobile')));
        $response->assertSessionHasErrors('mobile');
    }

    /** 
     * @group delete
     * @group crud
     */
    public function testAParticipantCanBeDeletedByAnEncoder()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $response = $this->delete('/participants/' . $participant->id);

        $this->assertCount(0, Participant::all());

        $response->assertRedirect('/participants');

        $response->assertSessionHas('success');

        $this->assertEquals('Participant deleted.', session('success'));
    }

    /** 
     * @group delete
     * @group crud
     */
    public function testAParticipantCanBeDeletedByAnAdmin()
    {
        $this->setUser('admin');

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $response = $this->delete('/participants/' . $participant->id);

        $this->assertCount(0, Participant::all());

        $response->assertRedirect('/participants');

        $response->assertSessionHas('success');

        $this->assertEquals('Participant deleted.', session('success'));
    }

    /** 
     * @group delete
     * @group crud
     */
    public function testAParticipantCannotBeDeletedByAUser()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $this->setUser('user');

        $response = $this->delete('/participants/' . $participant->id);

        $response->assertStatus(403);
    }

    /** 
     * @group delete
     * @group crud
     */
    public function testAParticipantCannotBeDeletedByAGuest()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        auth()->logout();

        $response = $this->delete('/participants/' . $participant->id);

        $response->assertRedirect('/login');
    }

    /** @group forceDelete */
    public function testAnAdminCanPermanentlyDeleteAParticipant()
    {
        $this->setUser('admin');

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $response = $this->delete('/participants/' . $participant->id . '/force');

        $this->assertCount(0, Participant::withTrashed()->get());

        $response->assertSessionHas('success');

        $this->assertEquals('Participant permanently deleted.', session('success'));

        $response->assertRedirect('/participants');
    }

    /** @group forceDelete */
    public function testAnEncoderCannotPermanentlyDeleteAParticipant()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $response = $this->delete('/participants/' . $participant->id . '/force');

        $response->assertStatus(403);
    }

    /** @group forceDelete */
    public function testAUserCannotPermanentlyDeleteAParticipant()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $this->setUser('user');

        $response = $this->delete('/participants/' . $participant->id . '/force');

        $response->assertStatus(403);
    }

    /** @group forceDelete */
    public function testAGuestCannotPermanentlyDeleteAParticipant()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $response = $this->delete('/participants/' . $participant->id);

        auth()->logout();

        $response = $this->delete('/participants/' . $participant->id . '/force');

        $response->assertRedirect('/login');
    }

    /** @group restore */
    public function testParticipantCanBeRestoredByAnEncoder()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $this->delete('/participants/' . $participant->id);

        $response = $this->post('/participants/' . $participant->id . '/restore');

        $this->assertCount(1, Participant::all());

        $response->assertRedirect('/participants');

        $response->assertSessionHas('success');

        $this->assertEquals('Participant restored.', session('success'));
    }

    /** @group restore */
    public function testParticipantCannotBeRestoredByAUser()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $this->delete('/participants/' . $participant->id);

        $this->setUser('user');

        $response = $this->post('/participants/' . $participant->id . '/restore');

        $response->assertStatus(403);

        $this->assertCount(0, Participant::all());
    }

    /** @group restore */
    public function testParticipantCannotBeRestoredByAGuest()
    {
        $this->setUser();

        $this->post('/participants', $this->participantData());

        $participant = Participant::first();

        $this->delete('/participants/' . $participant->id);

        auth()->logout();

        $response = $this->post('/participants/' . $participant->id . '/restore');

        $response->assertRedirect('/login');

        $this->assertCount(0, Participant::all());
    }

    private function setUser(string $role = 'encoder')
    {
        if (auth()->check()) {
            auth()->logout();
        }
        $user = factory(User::class)->states(['active', $role])->create();
        $this->actingAs($user);
    }

    private function participantData()
    {
        return [
            'last_name' => 'bautista',
            'first_name' => 'ramon',
            'mi' => 'X',
            'sex' => 'M',
            'station' => 'South Station',
            'mobile' => '639998887766',
            'email' => 'abc@123.com'
        ];
    }
}
