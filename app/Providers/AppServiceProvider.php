<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\FlightPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Flight::class, FlightPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);

        Gate::define('isAdmin', fn (?User $user) => $user?->role === 'admin');
        Gate::define('isUser', fn (?User $user) => $user?->role === 'user');
    }
}
