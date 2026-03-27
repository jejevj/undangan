<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            // true = gift section aktif (premium selalu true, free harus bayar dulu)
            $table->boolean('gift_enabled')->default(false)->after('gallery_display');
        });

        Schema::table('templates', function (Blueprint $table) {
            // Harga buka fitur gift untuk free template (0 = gratis/selalu aktif)
            $table->unsignedInteger('gift_feature_price')
                  ->default(0)
                  ->after('extra_photo_price')
                  ->comment('0=gratis/selalu aktif, >0=harus bayar untuk free template');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('gift_enabled');
        });
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('gift_feature_price');
        });
    }
};
