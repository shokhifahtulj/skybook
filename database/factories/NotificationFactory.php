<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?: User::factory()->create();

        return [
            'user_id' => $user->id,
            'pesan' => $this->faker->sentence(8),
            'status_baca' => $this->faker->boolean(40),
        ];
    }
}
