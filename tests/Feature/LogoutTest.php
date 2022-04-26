<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testUserIsLoggedOutProperly()
    {
        $user = User::factory()->create([
            'email'=> $email = time().'@example.com',
        ]);

        $token = $user->createToken('AppName')->plainTextToken;
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('get', '/api/notes', [], $headers)->assertStatus(200);
        $this->json('post', '/api/logout', [], $headers)->assertStatus(200);

        $user = User::find($user->id);

        $this->assertEquals(null, $user->token);
    }

    public function testUserWithNullToken()
    {
        $user = User::factory()->create([
            'email'=> $email = time().'@example.com',
        ]);

        $this->json('get', '/api/notes', [])->assertStatus(401);
    }
}
