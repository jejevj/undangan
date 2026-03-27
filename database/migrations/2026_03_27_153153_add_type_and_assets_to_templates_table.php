<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            // Tipe template
            $table->enum('type', ['free', 'premium', 'custom'])
                  ->default('free')
                  ->after('slug');

            // Harga (0 = gratis)
            $table->unsignedInteger('price')
                  ->default(0)
                  ->after('type')
                  ->comment('Harga dalam Rupiah, 0 = gratis');

            // Nama folder assets: public/invitation-assets/{asset_folder}/
            // dan views: resources/views/invitation-templates/{asset_folder}/
            $table->string('asset_folder')
                  ->nullable()
                  ->after('blade_view')
                  ->comment('Nama folder di public/invitation-assets/ dan resources/views/invitation-templates/');

            // Versi template
            $table->string('version', 20)
                  ->default('1.0.0')
                  ->after('asset_folder');

            // Preview URL (screenshot/demo link)
            $table->string('preview_url')->nullable()->after('thumbnail');
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['type', 'price', 'asset_folder', 'version', 'preview_url']);
        });
    }
};
