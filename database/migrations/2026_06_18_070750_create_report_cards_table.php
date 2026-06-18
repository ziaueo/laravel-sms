<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->decimal('gpa', 5, 2)->nullable();
            $table->smallInteger('rank_in_class')->nullable();
            $table->smallInteger('rank_in_grade')->nullable();
            $table->smallInteger('total_hadir')->default(0);
            $table->smallInteger('total_sakit')->default(0);
            $table->smallInteger('total_izin')->default(0);
            $table->smallInteger('total_alpa')->default(0);
            $table->text('homeroom_notes')->nullable();
            $table->text('principal_notes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'school_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};