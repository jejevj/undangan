<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_messages', function (Blueprint $table) {
            $table->unsignedInteger('likes_count')->default(0)->after('message');
            $table->index('likes_count');
        });
    }

    public function down(): void
    {
        Schema::table('guest_messages', function (Blueprint $table) {
            $table->dropIndex(['likes_count']);
            $table->dropColumn('likes_count');
        });
    }
};
