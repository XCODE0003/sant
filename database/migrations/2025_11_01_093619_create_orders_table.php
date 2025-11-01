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
            $table->uuid('uuid')->unique();
            $table->string('number')->unique();
            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->string('customer_phone');
            $table->string('customer_email');
            $table->json('products')->default('[]');
            $table->unsignedInteger('items_count');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['cash', 'card'])->default('cash');
            $table->enum('delivery_method', ['courier', 'pickup'])->default('courier');
            $table->boolean('delivery_is_private_house')->default(false);
            $table->string('delivery_city')->default('Челябинск');
            $table->string('delivery_street')->nullable();
            $table->string('delivery_house')->nullable();
            $table->string('delivery_apartment')->nullable();
            $table->string('delivery_entrance')->nullable();
            $table->text('delivery_comment')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('agreement')->default(false);
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
