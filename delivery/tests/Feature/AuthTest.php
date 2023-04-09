<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => 'Test Personal Access Client'
        ]);
    }

    public function testDatabaseConnection()
    {
        $result = DB::select('SELECT 1+1 AS sum');
        $this->assertEquals(2, $result[0]->sum);
    }

    public function test_register()
    {
        $response = $this->post(route('register'), [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'Aa123456',
            'password_confirmation' => 'Aa123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'access_token',
            ])
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'test',
            'email' => 'test@test.com',
        ]);
    }

    public function test_login()
    {
        $user = User::factory()->create(['password' => bcrypt('Aa123456')]);
         $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Aa123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'access_token',
            ])
            ->assertJson([
                'success' => true
            ]);
    }

    public function test_logout()
    {
        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Aa123456',
        ]);
        $token = $user->createToken('TestToken')->accessToken;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(route('logout'));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);
    }
}
