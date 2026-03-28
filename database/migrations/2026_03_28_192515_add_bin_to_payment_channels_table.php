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
        Schema::table('payment_channels', function (Blueprint $table) {
            $table->string('bin', 20)->nullable()->after('code')->comment('Bank Identification Number / Partner Service ID');
            $table->text('bin_notes')->nullable()->after('bin')->comment('Notes about BIN configuration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_channels', function (Blueprint $table) {
            $table->dropColumn(['bin', 'bin_notes']);
        });
    }
};
