<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('music_upload_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->integer('amount')->default(5000); // Rp 5.000 per upload
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Data musik yang akan diupload (disimpan sementara)
            $table->string('temp_title')->nullable();
            $table->string('temp_artist')->nullable();
            $table->string('temp_file_path')->nullable(); // Path temporary file
            
            $table->foreignId('music_id')->nullable()->constrained()->onDelete('set null'); // ID musik setelah diupload
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_upload_orders');
    }
};
