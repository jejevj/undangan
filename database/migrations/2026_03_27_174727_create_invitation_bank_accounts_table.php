<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->onDelete('cascade');
            $table->string('bank_name');          // BCA, Mandiri, BNI, dll
            $table->string('account_number');     // No rekening
            $table->string('account_name');       // Nama pemilik rekening
            $table->string('logo')->nullable();   // path logo bank (opsional)
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_bank_accounts');
    }
};
