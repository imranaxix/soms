<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the admin already exists to prevent duplicate entries
        if (!User::where('email', 'admin@soms.test')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@soms.test',
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // Or your chosen password
                'role' => 'admin',
                'business_name' => 'SOMS Admin Headquarters',
                'is_active' => 1,
                'is_verified' => 1,
            ]);
        }
    }
}
