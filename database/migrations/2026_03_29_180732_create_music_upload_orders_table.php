<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('music_upload_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('qty')->default(1); // Number of upload slots
            $table->decimal('amount', 10, 2);
            $table->decimal('price_per_slot', 10, 2)->default(10000);
            $table->decimal('admin_fee', 10, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->foreignId('payment_channel_id')->nullable()->constrained('payment_channels')->onDelete('set null');
            $table->string('va_number')->nullable();
            $table->string('payment_url')->nullable();
            $table->text('qr_string')->nullable();
            $table->string('qr_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_upload_orders');
    }
};
