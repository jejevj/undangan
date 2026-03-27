<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            // Pesan pengantar, bisa mengandung placeholder {link} dan {nama_tamu}
            // Contoh: "Kepada Yth. {nama_tamu}, kami mengundang Anda. Klik {link} untuk membuka undangan."
            $table->text('greeting')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('greeting');
        });
    }
};
