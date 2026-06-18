<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('curriculum_id')->constrained('curriculums');
            $table->string('name');
            $table->string('year', 9);
            $table->smallInteger('semester');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['school_id', 'year', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_years');
    }
};