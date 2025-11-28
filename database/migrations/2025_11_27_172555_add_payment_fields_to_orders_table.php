<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_id')->nullable()->after('payment_method');
            $table->string('payment_status')->nullable()->after('payment_id');
            $table->string('payment_url')->nullable()->after('payment_status');
            $table->json('payment_data')->nullable()->after('payment_url');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_id', 'payment_status', 'payment_url', 'payment_data']);
        });
    }
};