<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Kode kampanye untuk URL
            $table->text('description')->nullable();
            $table->foreignId('pricing_plan_id')->constrained()->onDelete('cascade'); // Plan yang diberikan
            $table->integer('max_users')->default(0); // 0 = unlimited
            $table->integer('used_count')->default(0); // Jumlah user yang sudah menggunakan
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add campaign_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['campaign_id']);
            $table->dropColumn('campaign_id');
        });
        
        Schema::dropIfExists('campaigns');
    }
};
