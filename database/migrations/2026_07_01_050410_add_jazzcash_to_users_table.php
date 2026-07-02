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
            // JazzCash payment method details (primarily for manufacturers)
            $table->string('jazzcash_mobile', 15)->nullable()->after('role');
            $table->string('jazzcash_account_title', 100)->nullable()->after('jazzcash_mobile');
            $table->boolean('jazzcash_verified')->default(false)->after('jazzcash_account_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jazzcash_mobile', 'jazzcash_account_title', 'jazzcash_verified']);
        });
    }
};
