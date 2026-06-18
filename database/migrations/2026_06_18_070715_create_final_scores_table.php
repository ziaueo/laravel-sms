<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->string('predicate')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'school_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_scores');
    }
};