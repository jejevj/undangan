<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_gallery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->onDelete('cascade');
            $table->string('path');           // storage path
            $table->string('caption')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_paid')->default(false); // false = slot gratis, true = slot berbayar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_gallery');
    }
};
