<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->index(); // For anonymous users
            $table->string('event_name')->index(); // e.g., 'page_view', 'button_click', 'form_submit'
            $table->string('event_category')->index(); // e.g., 'subscription', 'invitation', 'payment'
            $table->string('event_label')->nullable(); // Additional context
            $table->json('event_data')->nullable(); // Additional data
            $table->string('page_url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->index();
            
            // Indexes for funnel analysis
            $table->index(['event_category', 'event_name', 'created_at']);
            $table->index(['session_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_events');
    }
};
