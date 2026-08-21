<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('safepay_api_key')->nullable();
            $table->text('safepay_webhook_secret')->nullable();
            $table->string('safepay_environment', 20)->nullable()->default('sandbox');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('safepay_tracker_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['safepay_api_key', 'safepay_webhook_secret', 'safepay_environment']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('safepay_tracker_id');
        });
    }
};