<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jazzcash_merchant_id')->nullable()->after('jazzcash_account_title');
            $table->text('jazzcash_password')->nullable()->after('jazzcash_merchant_id');
            $table->text('jazzcash_integrity_salt')->nullable()->after('jazzcash_password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'jazzcash_merchant_id',
                'jazzcash_password',
                'jazzcash_integrity_salt'
            ]);
        });
    }
};