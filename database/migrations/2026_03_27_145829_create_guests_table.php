<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->onDelete('cascade');
            $table->string('name');           // "J & Pasangan", "Keluarga Budi", dll
            $table->string('slug')->nullable(); // auto-generated dari name, untuk URL ?to=slug
            $table->text('notes')->nullable(); // catatan internal
            $table->boolean('is_attending')->nullable(); // konfirmasi kehadiran (null = belum)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
