<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            // null = unlimited (premium), angka = batas jumlah tamu (free)
            $table->unsignedSmallInteger('guest_limit')
                  ->nullable()
                  ->default(null)
                  ->after('gift_feature_price')
                  ->comment('null=unlimited, 40=batas free');
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('guest_limit');
        });
    }
};
