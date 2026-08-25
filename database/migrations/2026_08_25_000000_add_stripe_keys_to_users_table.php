<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('stripe_publishable_key')->nullable()->after('stripe_onboarding_completed');
            $table->text('stripe_secret_key')->nullable()->after('stripe_publishable_key');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_publishable_key', 'stripe_secret_key']);
        });
    }
};
