<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();           // 'free', 'basic', 'pro'
            $table->string('name');                     // 'Free', 'Basic', 'Pro'
            $table->unsignedInteger('price')->default(0); // Rp per bulan
            $table->string('badge_color')->default('secondary'); // bootstrap color
            $table->boolean('is_popular')->default(false);

            // Limits (null = unlimited)
            $table->unsignedTinyInteger('max_invitations')->default(1);
            $table->unsignedSmallInteger('max_gallery_photos')->nullable(); // total semua undangan
            $table->unsignedTinyInteger('max_music_uploads')->default(0);   // 0 = tidak bisa upload
            $table->boolean('gift_section_included')->default(false);
            $table->boolean('can_delete_music')->default(true);

            $table->text('features')->nullable(); // JSON array deskripsi fitur untuk UI
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_plans');
    }
};
