<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_documents', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_hash', 64)->index();

            $table->string('status')->default('pending')->index();
            // pending, processing, completed, failed

            $table->unsignedInteger('page_count')->nullable();
            $table->longText('summary')->nullable();
            $table->text('processing_error')->nullable();

            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_documents');
    }
};
