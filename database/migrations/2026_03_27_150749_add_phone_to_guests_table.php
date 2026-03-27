<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('phone_code', 10)->nullable()->default('+62')->after('name'); // kode negara, e.g. +62
            $table->string('phone', 20)->nullable()->after('phone_code');               // nomor tanpa kode negara
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['phone_code', 'phone']);
        });
    }
};
