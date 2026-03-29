<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->onDelete('cascade');
            $table->string('guest_name'); // From 'to' parameter
            $table->text('message');
            $table->string('ip_address')->nullable();
            $table->boolean('is_approved')->default(true); // Auto-approve by default
            $table->timestamps();
            
            $table->index(['invitation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_messages');
    }
};
