<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            // null = unlimited (premium), angka = batas foto galeri (free)
            $table->unsignedTinyInteger('free_photo_limit')
                  ->nullable()
                  ->default(null)
                  ->after('price')
                  ->comment('null=unlimited, 2=free default');

            // Harga per foto tambahan (Rp)
            $table->unsignedInteger('extra_photo_price')
                  ->default(5000)
                  ->after('free_photo_limit')
                  ->comment('Harga per foto tambahan dalam Rupiah');
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['free_photo_limit', 'extra_photo_price']);
        });
    }
};
