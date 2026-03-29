<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('music_upload_orders', function (Blueprint $table) {
            // Check if foreign key exists before dropping
            if (Schema::hasColumn('music_upload_orders', 'music_id')) {
                // Try to drop foreign key if it exists
                try {
                    $table->dropForeign(['music_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                
                // Drop old columns
                $table->dropColumn(['temp_title', 'temp_artist', 'temp_file_path', 'music_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('music_upload_orders', function (Blueprint $table) {
            // Restore old columns
            $table->string('temp_title')->nullable();
            $table->string('temp_artist')->nullable();
            $table->string('temp_file_path')->nullable();
            $table->unsignedBigInteger('music_id')->nullable();
            
            // Restore foreign key
            $table->foreign('music_id')->references('id')->on('music')->onDelete('set null');
        });
    }
};
