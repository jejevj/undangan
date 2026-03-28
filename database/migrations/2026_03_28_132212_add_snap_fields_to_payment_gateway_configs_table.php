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
        Schema::table('payment_gateway_configs', function (Blueprint $table) {
            $table->text('private_key')->nullable()->after('secret_key');
            $table->text('public_key')->nullable()->after('private_key');
            $table->text('doku_public_key')->nullable()->after('public_key');
            $table->string('issuer')->nullable()->after('doku_public_key');
            $table->text('auth_code')->nullable()->after('issuer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_configs', function (Blueprint $table) {
            $table->dropColumn(['private_key', 'public_key', 'doku_public_key', 'issuer', 'auth_code']);
        });
    }
};
