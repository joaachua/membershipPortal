<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRegister()
    {
        $request = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone_number' => '123456789',
            'membership' => 'basic',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/register', $request);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message', 
            'data',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    public function testLogin()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $request = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $request);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'data',
        ]);
    }
}