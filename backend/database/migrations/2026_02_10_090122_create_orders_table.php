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

        // 1. Add nullable()
        // 2. Change cascade to set null
        $table->foreignId('service_id')
              ->nullable()
              ->constrained()
              ->onDelete('set null');

        $table->foreignId('client_id')
              ->nullable()
              ->constrained('users')
              ->onDelete('set null');

        $table->foreignId('freelancer_id')
              ->nullable()
              ->constrained('users')
              ->onDelete('set null');

        $table->enum('status', ['pending', 'accepted', 'completed', 'cancelled'])->default('pending');
        $table->timestamps();

        $table->index(['client_id', 'status']);
        $table->index(['freelancer_id', 'status']);
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
