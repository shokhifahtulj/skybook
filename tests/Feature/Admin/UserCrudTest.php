<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('allows admin to create, update, and delete users through the admin ui', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin);

    $response = $this->get(route('admin.users.index'));
    $response->assertStatus(200);

    $createResponse = $this->post(route('admin.users.store'), [
        'name' => 'New Admin User',
        'email' => 'admin.user@example.com',
        'password' => 'Secret123!',
        'password_confirmation' => 'Secret123!',
        'role' => 'admin',
    ]);

    $createResponse->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $createdUser = User::where('email', 'admin.user@example.com')->firstOrFail();

    expect($createdUser->role)->toBe('admin');
    expect(Hash::check('Secret123!', $createdUser->password))->toBeTrue();

    $updateResponse = $this->put(route('admin.users.update', $createdUser), [
        'name' => 'Updated Admin User',
        'email' => 'admin.user.updated@example.com',
        'role' => 'user',
        'password' => '',
        'password_confirmation' => '',
    ]);

    $updateResponse->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $createdUser->refresh();

    expect($createdUser->name)->toBe('Updated Admin User');
    expect($createdUser->email)->toBe('admin.user.updated@example.com');
    expect($createdUser->role)->toBe('user');

    $deleteResponse = $this->delete(route('admin.users.destroy', $createdUser));

    $deleteResponse->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    expect(User::where('id', $createdUser->id)->exists())->toBeFalse();
});
