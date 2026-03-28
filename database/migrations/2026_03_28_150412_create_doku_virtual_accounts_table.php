<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doku_virtual_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // VA Details
            $table->string('partner_service_id', 20); // From DOKU config
            $table->string('customer_no', 20); // User ID or unique identifier
            $table->string('virtual_account_no', 20)->unique(); // Full VA number
            $table->string('virtual_account_name', 255);
            $table->string('virtual_account_email', 255)->nullable();
            $table->string('virtual_account_phone', 30)->nullable();
            
            // Transaction Details
            $table->string('trx_id', 64)->unique(); // Invoice number
            $table->decimal('amount', 16, 2);
            $table->string('currency', 3)->default('IDR');
            
            // Payment Type
            $table->enum('payment_type', ['subscription', 'gift', 'gallery', 'music_upload']); // What is being paid
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of subscription/gift/gallery/music order
            
            // VA Configuration
            $table->string('channel', 50); // VIRTUAL_ACCOUNT_BANK_CIMB, etc
            $table->enum('trx_type', ['C', 'O'])->default('C'); // C=Closed Amount, O=Open Amount
            $table->boolean('reusable')->default(false);
            $table->decimal('min_amount', 16, 2)->nullable();
            $table->decimal('max_amount', 16, 2)->nullable();
            $table->timestamp('expired_at')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'active', 'paid', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            
            // DOKU Response
            $table->text('doku_response')->nullable(); // JSON response from DOKU
            $table->string('doku_reference_no', 128)->nullable(); // Reference from DOKU
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('virtual_account_no');
            $table->index('trx_id');
            $table->index('status');
            $table->index('payment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doku_virtual_accounts');
    }
};
