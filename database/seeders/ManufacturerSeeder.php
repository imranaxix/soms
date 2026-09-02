<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManufacturerSeeder extends Seeder
{
    /**
     * Seed the demo admin/manufacturer account.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'mfg@demo.com'],
            [
                'name' => 'Muhammad Ali',
                'role' => 'manufacturer',
                'business_name' => 'XYZ Manufacturers',
                'password' => Hash::make('password'),
                'is_active' => true,
                'is_verified' => true,
                'safepay_api_key' => 'sec_ed9ccaca-5ff2-413a-892c-fb6a33ef1c74',
                'safepay_secret_key' => 'df73de3c34cffc694e06e06ff678ebae6582be23c4f8cb0d1f3d3df468baa84f',
                'safepay_webhook_secret' => 'd4cc352d0a53501f196a8cfc28dc967d8486c753961b526f1fd3154f95204177',
                'safepay_environment' => 'sandbox',
                'stripe_publishable_key' => 'pk_test_51TrBCAFwaleMgv4SIck0duOdF3qlVwPJMWnU4a1veKf1DvcF5kc0k3VgOl7S7OOUXFWCgYUlEDeKSeRtdAKstsah00HeJQwnSY',
                'stripe_secret_key' => 'sk_test_51TrBCAFwaleMgv4SFKE1lidmTepcnfGDQgAEe24nENbSC4u5icjkK6MHErKqucVttJKTPilGM54APfdRfOINPYj900eop79A1o',
                'stripe_onboarding_completed' => true,
            ],
        );
    }
}
