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
        Schema::table('pricing_plans', function (Blueprint $table) {
            $table->enum('visibility', ['public', 'business'])->default('public')->after('slug');
            $table->enum('billing_period', ['lifetime', 'monthly', 'yearly'])->default('lifetime')->after('price');
            $table->boolean('show_partnership_logo')->default(false)->after('gift_section_included');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            $table->dropColumn(['visibility', 'billing_period', 'show_partnership_logo']);
        });
    }
};
