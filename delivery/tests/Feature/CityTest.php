<?php

namespace Tests\Feature;


use Faker\Factory as Faker;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CityTest extends TestCase
{
    use RefreshDatabase;

    private $user_admin;
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => 'Test Personal Access Client'
        ]);
        $this->user = User::factory()->create(['is_admin' => false]);
        $this->user_admin = User::factory()->create(['is_admin' => true]);
    }

    public function testIndex()
    {
        City::factory(5)->create();

        $this->actingAs($this->user, 'api')->getJson(route('cities.index'))
            ->assertStatus(200)
            ->assertJson([
                'cities' => City::all()->toArray(),
            ]);
    }

    public function testStore()
    {
        $this->actingAs($this->user, 'api')
            ->postJson(route('cities.story'), ['name' => 3])
            ->assertStatus(403);
    }

    public function testStoreAdmin()
    {
        $faker = Faker::create();
        $city = $faker->unique()->city;
        $this->actingAs($this->user_admin, 'api')
            ->postJson(route('cities.story'), ['name' => $city])
            ->assertStatus(200)->assertJson([
                'message' => 'City added successfully',
            ]);
        $this->assertDatabaseHas('cities', ['name' => $city]);
    }

    public function testDestroy()
    {
        $city = City::factory()->create();
        $this->actingAs($this->user, 'api')
             ->delete(route('cities.destroy', $city->id))
             ->assertStatus(403);
    }


    public function testDestroyAdmin()
    {
        $city = City::factory()->create();
        $this->actingAs($this->user_admin, 'api')
            ->delete(route('cities.destroy', $city->id))
            ->assertStatus(200)->assertJson([
                'message' => 'City deleted successfully',
            ]);

        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }
}
