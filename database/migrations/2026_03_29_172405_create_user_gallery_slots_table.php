<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table untuk menyimpan slot gallery user
        Schema::create('user_gallery_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('free_slots')->default(10); // Slot gratis dari paket
            $table->integer('purchased_slots')->default(0); // Slot yang dibeli
            $table->timestamps();
            
            $table->unique('user_id');
        });

        // Update gallery_orders untuk link ke user (bukan invitation)
        Schema::table('gallery_orders', function (Blueprint $table) {
            $table->dropForeign(['invitation_id']);
            $table->dropColumn('invitation_id');
        });

        // Update invitation_gallery untuk optional invitation_id
        Schema::table('invitation_gallery', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('invitation_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('invitation_gallery', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->unsignedBigInteger('invitation_id')->nullable(false)->change();
        });

        Schema::table('gallery_orders', function (Blueprint $table) {
            $table->foreignId('invitation_id')->after('user_id')->constrained()->onDelete('cascade');
        });

        Schema::dropIfExists('user_gallery_slots');
    }
};
