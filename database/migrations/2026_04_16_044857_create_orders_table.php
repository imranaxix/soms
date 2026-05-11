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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            // Relationships
            $table->foreignId('shop_owner_id')->constrained('users');
            $table->foreignId('manufacturer_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('variant_id')->constrained('product_variants');
            
            $table->integer('quantity');
            $table->string('unit')->default('pieces'); // pieces, meters, kilograms
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('payment_terms'); // full_advance, 50_advance, on_delivery

            $table->date('due_date');
            $table->string('status')->default('Pending'); // Pending, In Progress, Completed, Cancelled
            $table->integer('progress_percent')->default(0);
            $table->text('special_instructions')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
