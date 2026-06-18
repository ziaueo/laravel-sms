<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->smallInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->smallInteger('type')->default(1);
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->timestamps();

            $table->unique(['classroom_id', 'day_of_week', 'start_time', 'school_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};