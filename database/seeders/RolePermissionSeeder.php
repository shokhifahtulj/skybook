<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        $accounts = [
            [
                'email' => 'admin@gmail.com',
                'name' => 'Administrator',
                'password' => '123456',
                'role' => 'admin',
            ],
            [
                'email' => 'user@gmail.com',
                'name' => 'Regular User',
                'password' => '123456',
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
                ]
            );

            $user->syncRoles([$account['role']]);
        }
    }
}