<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Note;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(){
        $user = User::factory()->create();
        $token = $user->createToken('AppName')->plainTextToken;
        return ['Authorization' => "Bearer $token"];
    }

    public function testsNotesAreCreatedCorrectly()
    {
        $headers = $this->authenticate();
        $payload = [
            'title' => 'Lorem',
            'body' => 'Ipsum',
        ];

        $this->json('POST', '/api/notes', $payload, $headers)
            ->assertStatus(201)
            ->assertJson(['id' => 1, 'title' => 'Lorem', 'body' => 'Ipsum']);
    }

    public function testsRequireTitleWhenUserIsCreated()
    {
        $headers = $this->authenticate();
        $payload = ['body' => 'Ipsum'];
        $this->json('POST', '/api/notes', $payload, $headers)
            ->assertStatus(422)
            ->assertJson(
                [
                    "message" =>  "the provided data is not valid",
                    "errors" =>  [
                        "title" =>  ["The title field is required."],
                    ]
                ]
            );
    }

    public function testsRequireBodyWhenUserIsCreated()
    {
        $headers = $this->authenticate();
        $payload = ['title' => 'Ipsum'];
        $this->json('POST', '/api/notes', $payload, $headers)
            ->assertStatus(422)
            ->assertJson(
                [
                    "message" =>  "the provided data is not valid",
                    "errors" =>  [
                        "body" =>  ["The body field is required."],
                    ]
                ]
            );
    }

    public function testsNotesAreCreatedOk()
    {
        $headers = $this->authenticate();
        $payload = [
            'title' => 'Lorem',
            'body' => 'Ipsum',
        ];

        $response = $this->json('POST', '/api/notes', $payload, $headers);

        $this->assertCount(1, Note::all());
        $note = Note::first();
        $this->assertEquals($note->title, 'Lorem');
        $this->assertEquals($note->body, 'Ipsum');
    }

    public function testsNotesAreUpdatedCorrectly()
    {
        $headers = $this->authenticate();
        $note = Note::factory()->create([
            'title' => 'First Article',
            'body' => 'First Body',
        ]);

        $payload = [
            'title' => 'Lorem',
            'body' => 'Ipsum',
        ];

        $response = $this->json('PUT', '/api/notes/' . $note->id, $payload, $headers)
            ->assertStatus(200)
            ->assertJson([ 
                'id' => 1, 
                'title' => 'Lorem', 
                'body' => 'Ipsum' 
            ]);
    }

    public function testsNotesAreDeletedCorrectly()
    {
        $headers = $this->authenticate();
        $note = Note::factory()->create([
            'title' => 'First Article',
            'body' => 'First Body',
        ]);

        $this->json('DELETE', '/api/notes/' . $note->id, [], $headers)
            ->assertStatus(204);
    }

    public function testsFindNoteCorrectly()
    {
        $headers = $this->authenticate();
        Note::factory()->create([
            'title' => 'Second Article',
            'body' => 'Second Body'
        ]);

        $response = $this->json('GET', '/api/notes/1', [], $headers)
            ->assertStatus(200)
            ->assertJson(
                [ 'title' => 'Second Article', 'body' => 'Second Body' ]
            );
    }

    public function testsCouldNotFindNote()
    {
        //$this->withoutExceptionHandling();
        $headers = $this->authenticate();
        Note::factory()->create([
            'title' => 'Second Article',
            'body' => 'Second Body'
        ]);

        $response = $this->json('GET', '/api/notes/2', [], $headers)
            ->assertStatus(404)
            ->assertJson(
                [ "message"=> "Record not found." ]
            );
    }

    public function testsNotesAreListedCorrectly()
    {
        Note::factory()->create([
            'title' => 'First Article',
            'body' => 'First Body'
        ]);
        Note::factory()->create([
            'title' => 'Second Article',
            'body' => 'Second Body'
        ]);

        $headers = $this->authenticate();

        $response = $this->json('GET', '/api/notes', [], $headers)
            ->assertStatus(200)
            ->assertJson([
                [ 'title' => 'First Article', 'body' => 'First Body' ],
                [ 'title' => 'Second Article', 'body' => 'Second Body' ]
            ])
            ->assertJsonStructure([
                '*' => ['id', 'body', 'title', 'created_at', 'updated_at'],
            ]);
    }

}