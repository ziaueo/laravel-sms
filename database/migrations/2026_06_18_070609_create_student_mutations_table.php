<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->smallInteger('type');
            $table->date('mutation_date');
            $table->text('reason')->nullable();
            $table->string('origin_school_name')->nullable();
            $table->string('origin_school_address')->nullable();
            $table->string('origin_class')->nullable();
            $table->string('destination_school_name')->nullable();
            $table->string('destination_school_address')->nullable();
            $table->jsonb('documents')->nullable();
            $table->smallInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_mutations');
    }
};