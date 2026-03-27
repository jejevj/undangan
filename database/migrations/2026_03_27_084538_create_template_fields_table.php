<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->string('key');           // "groom_name", "bride_name", dll
            $table->string('label');         // "Nama Mempelai Pria"
            $table->enum('type', [
                'text', 'textarea', 'date', 'time', 'datetime',
                'image', 'url', 'number', 'select'
            ])->default('text');
            $table->text('options')->nullable();  // JSON untuk type=select
            $table->boolean('required')->default(false);
            $table->string('placeholder')->nullable();
            $table->text('default_value')->nullable();
            $table->string('group')->nullable();  // "mempelai", "orang_tua", "acara", dll
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['template_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_fields');
    }
};
