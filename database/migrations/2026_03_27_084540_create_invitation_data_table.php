<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menyimpan nilai tiap field per undangan (EAV pattern)
        Schema::create('invitation_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_field_id')->constrained()->onDelete('cascade');
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->unique(['invitation_id', 'template_field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_data');
    }
};
