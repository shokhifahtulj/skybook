<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

it('seeds the default admin and user accounts for login', function () {
    $this->seed(DatabaseSeeder::class);

    $expectedAccounts = [
        ['email' => 'admin@gmail.com', 'password' => '123456', 'role' => 'admin'],
        ['email' => 'user@gmail.com', 'password' => '123456', 'role' => 'user'],
        ['email' => 'admin@demo.com', 'password' => 'Admin123!', 'role' => 'admin'],
        ['email' => 'user@demo.com', 'password' => 'User123!', 'role' => 'user'],
    ];

    foreach ($expectedAccounts as $account) {
        $user = User::where('email', $account['email'])->first();

        expect($user)->not->toBeNull();
        expect($user->role)->toBe($account['role']);
        expect(Hash::check($account['password'], $user->password))->toBeTrue();
    }
});
