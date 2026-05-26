<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$emails = ['admin@demo.com', 'admin@gmail.com', 'user@demo.com'];
foreach ($emails as $email) {
    $user = User::where('email', $email)->first();
    echo $email, ' | exists=', $user ? 'yes' : 'no', PHP_EOL;
    if ($user) {
        echo ' role=', $user->role, ' | has_roles=', $user->getRoleNames()->implode(','), PHP_EOL;
        echo ' password_hash=', substr($user->password, 0, 60), PHP_EOL;
        echo ' check(Admin123!)=', Hash::check('Admin123!', $user->password) ? 'yes' : 'no', PHP_EOL;
        echo ' check(User123!)=', Hash::check('User123!', $user->password) ? 'yes' : 'no', PHP_EOL;
        echo ' check(123456)=', Hash::check('123456', $user->password) ? 'yes' : 'no', PHP_EOL;
    }
}
