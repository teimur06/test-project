<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\DeliveryRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DeliveryRequestControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;


    private $user;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => 'Test Personal Access Client'
        ]);
        $this->user = User::factory()->create();
    }

    public function testIndex()
    {

        $deliveryRequests = DeliveryRequest::factory()->count(5)->create(['user_id' => $this->user->id]);
        $admin = User::factory()->create(['is_admin' => true]);

        // Тестирование для обычного пользователя
        $response = $this->actingAs($this->user, 'api')->getJson(route('delivery.index'));
        $response->assertOk();
        $response->assertJsonCount(5);


        // Тестирование для администратора
        DeliveryRequest::factory()->count(2)->create();
        $response = $this->actingAs($admin, 'api')->getJson(route('delivery.index'));
        $response->assertOk();
        $response->assertJsonCount(7);
    }


    public function testStore()
    {
        $fromCity = City::factory()->create();
        $toCity = City::factory()->create();

        $data = [
            'user_id' => $this->user->id,
            'from_city_id' => $fromCity->id,
            'to_city_id' => $toCity->id,
            'delivery_date' => $this->faker->dateTime()->format('Y-m-d'),
            'status' => 'pending',
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('delivery.store'), $data)
            ->assertStatus(200)
            ->assertJson(['success' => true]);


        $this->assertDatabaseHas('delivery_requests', $data);
    }


    public function testStoreInvalidData()
    {
        $data = [
            'from_city_id' => 9999,
            'to_city_id' => 8888,
            'delivery_date' => 'invalid date',
            'status' => 'invalid status',
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('delivery.store'), $data)
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => 'Invalid date format'
            ]);

    }

    public function testStoreDuplicateRequest()
    {
        $fromCity = City::factory()->create();
        $toCity = City::factory()->create();

        $data = [
            'user_id' => $this->user->id,
            'from_city_id' => $fromCity->id,
            'to_city_id' => $toCity->id,
            'delivery_date' => $this->faker->dateTime()->format('Y-m-d'),
            'status' => 'pending',
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('delivery.store'), $data)
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('delivery_requests', $data);

        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('delivery.store'), $data)
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => 'Delivery request with this parameters already exists'
            ]);
    }

    public function testUpdate()
    {
        $deliveryRequest = DeliveryRequest::factory()->create(['status' => 'pending']);
        $data = ['status' => 'approved', 'id' => $deliveryRequest->id];

        $response = $this->actingAs($this->user, 'api')
            ->putJson(route('delivery.update', $deliveryRequest->id), $data)
            ->assertStatus(200)
            ->assertJson($data);

        $this->assertDatabaseHas('delivery_requests', ['id' => $deliveryRequest->id, 'status' => 'approved']);
    }


    public function testUpdateInvalidData()
    {
        $deliveryRequest = DeliveryRequest::factory()->create(['status' => 'pending']);
        $data = ['status' => 'invalid status', 'id' => $deliveryRequest->id];

        $response = $this->actingAs($this->user, 'api')
            ->putJson(route('delivery.update', $deliveryRequest->id), $data)
            ->assertStatus(422);

    }

    public function testDestroy()
    {
        $deliveryRequest = DeliveryRequest::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->delete(route('delivery.destroy', $deliveryRequest->id))
            ->assertStatus(200)
            ->assertJson(['message' => 'deliveryRequest deleted successfully']);

        $this->assertDatabaseMissing('delivery_requests', ['id' => $deliveryRequest->id] );
    }


    public function testDestroyNonExistingDeliveryRequest()
    {
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(route('delivery.destroy', 9999))
            ->assertStatus(404)
            ->assertJson(['error' => 'deliveryRequest not found']);
    }

}
