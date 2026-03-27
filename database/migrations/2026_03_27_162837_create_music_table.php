<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('music', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('file_path');              // path di storage/app/public/music/ atau public/invitation-assets/music/
            $table->string('duration')->nullable();   // "3:45"
            $table->enum('type', ['free', 'premium'])->default('free');
            $table->unsignedInteger('price')->default(10000); // Rp 10.000 untuk premium
            $table->string('cover')->nullable();      // thumbnail album art
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot: user yang sudah punya akses ke lagu premium
        Schema::create('music_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('music_id')->constrained('music')->onDelete('cascade');
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'music_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_user');
        Schema::dropIfExists('music');
    }
};
