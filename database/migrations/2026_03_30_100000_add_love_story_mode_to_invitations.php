<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            // Mode cerita cinta: 'longtext' atau 'timeline'
            $table->enum('love_story_mode', ['longtext', 'timeline'])->default('longtext')->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('love_story_mode');
        });
    }
};
