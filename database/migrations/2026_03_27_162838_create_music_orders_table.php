<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('music_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();   // INV-MUSIC-20260327-XXXX
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('music_id')->constrained('music')->onDelete('cascade');
            $table->unsignedInteger('amount');          // nominal yang dibayar
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable(); // 'simulation', 'midtrans', dll
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_orders');
    }
};
