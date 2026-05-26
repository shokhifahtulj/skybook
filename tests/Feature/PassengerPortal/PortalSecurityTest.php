<?php

namespace Tests\Feature\PassengerPortal;

use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }

    public function test_user_can_generate_signed_portal_url()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $schedule = \App\Models\FlightSchedule::first();

        // 1. Create Draft
        $response = $this->postJson('/api/bookings/create-draft', [
            'segments' => [
                ['flight_schedule_id' => $schedule->id, 'cabin_class' => 'economy']
            ],
            'passengers' => [
                [
                    'title' => 'Mr', 'first_name' => 'John', 'last_name' => 'Doe', 'date_of_birth' => '1990-01-01',
                    'passenger_type' => 'adult', 'identity_type' => 'KTP', 'identity_number' => '123'
                ]
            ]
        ]);

        $pnr = $response->json('data.pnr');

        // 2. Search booking
        $lookupResponse = $this->post('/manage-booking', [
            'pnr' => $pnr,
            'last_name' => 'Doe'
        ]);

        $lookupResponse->assertRedirect();
        
        $redirectUrl = $lookupResponse->headers->get('Location');
        $this->assertStringContainsString('signature=', $redirectUrl);
        $this->assertStringContainsString("/manage-booking/portal/{$pnr}", $redirectUrl);

        // 3. Access Portal using signed URL
        $portalResponse = $this->get($redirectUrl);
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee($pnr);
    }

    public function test_user_cannot_access_portal_without_signature()
    {
        $response = $this->get('/manage-booking/portal/RANDOM');
        $response->assertStatus(401);
    }
}
