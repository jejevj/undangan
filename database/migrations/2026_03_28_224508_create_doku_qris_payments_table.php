<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doku_qris_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // DOKU QRIS identifiers
            $table->string('partner_reference_no')->unique(); // Our transaction ID
            $table->string('reference_no')->nullable(); // DOKU reference number
            $table->string('merchant_id'); // DOKU merchant ID
            $table->string('terminal_id'); // Terminal ID
            
            // Payment details
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->string('payment_type'); // subscription, gift, etc
            $table->unsignedBigInteger('reference_id')->nullable(); // FK to related table
            
            // QRIS content
            $table->text('qr_content')->nullable(); // QRIS string
            $table->text('qr_image_url')->nullable(); // URL to QR image if needed
            
            // Status and timestamps
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled', 'failed'])->default('pending');
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Additional info
            $table->string('postal_code', 5)->nullable();
            $table->string('fee_type', 10)->default('1'); // 1 = No Tips
            $table->string('approval_code')->nullable(); // From payment response
            
            // DOKU response data
            $table->json('doku_response')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_type');
            $table->index('partner_reference_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doku_qris_payments');
    }
};
