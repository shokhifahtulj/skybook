<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class LoginAccountsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'user'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $accounts = [
            [
                'email' => 'admin@demo.com',
                'name' => 'Admin',
                'password' => 'Admin123!',
                'role' => 'admin',
            ],
            [
                'email' => 'user@demo.com',
                'name' => 'User Biasa',
                'password' => 'User123!',
                'role' => 'user',
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'role' => $account['role'],
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$account['role']]);
        }
    }
}
