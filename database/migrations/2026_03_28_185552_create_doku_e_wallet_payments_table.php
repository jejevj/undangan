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
        Schema::create('doku_e_wallet_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Payment Details
            $table->string('partner_reference_no', 64)->unique(); // Unique reference
            $table->decimal('amount', 16, 2);
            $table->string('currency', 3)->default('IDR');
            
            // Payment Type
            $table->enum('payment_type', ['subscription', 'gift', 'gallery', 'music_upload']);
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of subscription/gift/gallery/music order
            
            // E-Wallet Configuration
            $table->string('channel', 50); // EMONEY_SHOPEE_PAY_SNAP, EMONEY_DANA_SNAP, EMONEY_OVO_SNAP
            
            // Customer Info
            $table->string('customer_name', 255);
            $table->string('customer_email', 255)->nullable();
            $table->string('customer_phone', 30)->nullable();
            
            // Payment URLs
            $table->text('web_redirect_url')->nullable(); // URL to redirect user
            $table->text('mobile_deep_link')->nullable(); // Deep link for mobile app
            
            // Status
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // DOKU Response
            $table->text('doku_response')->nullable(); // JSON response from DOKU
            $table->string('doku_transaction_id', 128)->nullable(); // Transaction ID from DOKU
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('partner_reference_no');
            $table->index('status');
            $table->index('payment_type');
            $table->index(['payment_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doku_e_wallet_payments');
    }
};
