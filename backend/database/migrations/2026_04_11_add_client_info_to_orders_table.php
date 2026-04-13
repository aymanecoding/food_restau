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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('client_name')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_address')->nullable();
            $table->text('client_note')->nullable();
            $table->string('payment_method')->default('cash');
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'client_phone', 'client_address', 'client_note', 'payment_method']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
