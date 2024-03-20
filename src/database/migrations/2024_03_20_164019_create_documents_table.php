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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_signed')->nullable();
            $table->integer('size')->nullable();
            $table->integer('size_signed')->nullable();
            $table->string('full_path');
            $table->string('full_path_signed')->nullable();
            $table->string('url');
            $table->string('url_signed')->nullable();
            $table->string('sha256_original_file');
            $table->string('sha256_file')->nullable();
            $table->string('contract_address')->nullable();
            $table->string('transaction_hash')->nullable();
            $table->string('smart_contract_address')->nullable();
            $table->enum('status', ['Completed', 'Draft', 'Void', 'In-Progress', 'Sent'])->default('Draft');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('reciever')->nullable();
            $table->string('type_document')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
