<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->foreignId('grade_level_id')->constrained('grade_levels');
            $table->unsignedBigInteger('homeroom_teacher_id')->nullable();
            $table->string('name');
            $table->smallInteger('capacity')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};