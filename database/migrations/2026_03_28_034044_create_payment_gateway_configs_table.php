<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50)->default('doku');
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->string('client_id');
            $table->text('secret_key'); // Will be encrypted
            $table->string('base_url');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['provider', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_configs');
    }
};
