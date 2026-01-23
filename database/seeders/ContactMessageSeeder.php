<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            // Fallback to random data if no users exist
            ContactMessage::factory()->count(10)->create();
            return;
        }

        foreach ($users as $user) {
            // Create 1-2 messages per user
            ContactMessage::factory()->count(rand(1, 2))->create([
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }
    }
}
