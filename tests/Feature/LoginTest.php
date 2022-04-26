<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    public function testRequiresEmailAndLogin()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(422)
            ->assertJson(
                [
                    "message" =>  "the provided data is not valid",
                    "errors" =>  [
                        "email" =>  ["The email field is required."],
                        "password" => ["The password field is required."]
                    ]
                ]
            );
    }


    public function testUserLoginsSuccessfully()
    {
        $user = User::factory()->create([
            'name' => 'Test',
            'email'=> $email = time().'@example.com',
            'password' => bcrypt('toptal123')
        ]);

        $payload = ['email' => $email, 'password' => 'toptal123'];

        $this->json('POST', 'api/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'token',
                'token_type',
                'expires_at',
            ]);
    }
}
