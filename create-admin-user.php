<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel application
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREATING ADMIN USER ===\n";

// Create or update admin user
$admin = User::updateOrCreate(
    ['email' => 'admin@admin.com'],
    [
        'name' => 'Administrator',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
    ]
);

echo "Admin user created/updated:\n";
echo "Email: admin@admin.com\n";
echo "Password: password\n";
echo "ID: {$admin->id}\n";

echo "\nNow you can access: http://127.0.0.1:8000/admin\n";