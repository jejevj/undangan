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
        Schema::create('payment_channels', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['virtual_account', 'ewallet']); // Payment type
            $table->string('code', 50)->unique(); // Channel code (e.g., VIRTUAL_ACCOUNT_BANK_CIMB)
            $table->string('name', 100); // Display name (e.g., CIMB Niaga)
            $table->string('icon', 255)->nullable(); // Icon path or URL
            $table->text('description')->nullable(); // Description
            $table->boolean('is_active')->default(true); // Admin can enable/disable
            $table->boolean('is_available')->default(false); // Auto-detected availability
            $table->timestamp('last_checked_at')->nullable(); // Last availability check
            $table->text('last_error')->nullable(); // Last error message
            $table->integer('sort_order')->default(0); // Display order
            $table->timestamps();
            
            // Indexes
            $table->index('type');
            $table->index('is_active');
            $table->index('is_available');
            $table->index(['type', 'is_active', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_channels');
    }
};
