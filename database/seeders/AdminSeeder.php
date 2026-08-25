<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the default admin account.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@soms.test'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'is_active' => true,
                'is_verified' => true,
                'business_name'=> 'Admin Business',
            ],
        );
    }
}
