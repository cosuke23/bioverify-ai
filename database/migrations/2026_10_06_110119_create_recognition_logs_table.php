<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recognition_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')
                ->nullable()
                ->constrained('people')
                ->nullOnDelete();

            $table->enum('recognition_type', [
                'Face',
                'Voice',
                'Face + Voice'
            ]);

            $table->decimal('face_score', 8, 5)->nullable();
            $table->decimal('voice_score', 8, 5)->nullable();
            $table->decimal('combined_score', 8, 5)->nullable();

            $table->enum('result', [
                'Matched',
                'Not Matched',
                'Review Required'
            ]);

            $table->string('source')->nullable();

            $table->string('ip_address')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('person_id');
            $table->index('recognition_type');
            $table->index('result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recognition_logs');
    }
};