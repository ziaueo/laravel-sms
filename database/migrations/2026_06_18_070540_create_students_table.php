<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('nisn')->nullable()->unique();
            $table->string('nis')->nullable();
            $table->string('full_name');
            $table->smallInteger('gender');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('blood_type', 5)->nullable();
            $table->year('entry_year')->nullable();
            $table->foreignId('entry_class_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->smallInteger('status')->default(1);
            $table->date('exit_date')->nullable();
            $table->text('exit_reason')->nullable();
            $table->jsonb('documents')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};