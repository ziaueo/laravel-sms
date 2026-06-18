<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_substitutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->date('date');
            $table->smallInteger('type');
            $table->foreignId('original_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('substitute_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('original_subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('substitute_subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_substitutions');
    }
};