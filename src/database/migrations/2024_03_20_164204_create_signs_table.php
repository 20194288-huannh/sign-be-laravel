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
        Schema::create('signs', function (Blueprint $table) {
            $table->id();
            $table->integer('x')->nullable();
            $table->integer('y')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('number_page')->nullable();
            $table->string('url')->nullable();
            $table->integer('size')->nullable();
            $table->string('name')->nullable();
            $table->string('full_path');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('sha256_original_file');
            $table->integer('priority')->nullable();
            $table->enum('type', ['Name', 'Text', 'Signature', 'Checkbox', 'Radio', 'Date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signs');
    }
};
