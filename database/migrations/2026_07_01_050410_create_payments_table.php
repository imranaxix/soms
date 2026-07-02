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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Order & parties
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payer_id')->constrained('users')->cascadeOnDelete();   // shop owner
            $table->foreignId('payee_id')->constrained('users')->cascadeOnDelete();   // manufacturer

            // Amount
            $table->decimal('amount', 12, 2);

            // Our unique reference generated per payment attempt 
            $table->string('txn_ref_no')->unique();

            // JazzCash response fields (filled after API call / callback)
            $table->string('pp_txn_id')->nullable();           // JazzCash transaction ID
            $table->string('pp_response_code')->nullable();    // e.g. 000 = success
            $table->string('pp_response_message')->nullable();
            $table->string('pp_retrieval_ref_no')->nullable(); // JazzCash retrieval ref

            // Status: pending | completed | failed
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
