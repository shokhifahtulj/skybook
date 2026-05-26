<?php

namespace Tests\Feature\PassengerPortal;

use Tests\TestCase;

class SharedLayoutLookupTest extends TestCase
{
    public function test_manage_booking_index_uses_the_shared_passenger_layout(): void
    {
        $this->get(route('manage-booking.index'))
            ->assertStatus(200)
            ->assertSee('Manage Your Booking')
            ->assertSee('Passenger workspace');
    }

    public function test_check_in_lookup_uses_the_shared_passenger_layout(): void
    {
        $this->get(route('checkin.index'))
            ->assertStatus(200)
            ->assertSee('Web Check-In')
            ->assertSee('Passenger workspace');
    }
}
