<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_smp_sma_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->text('health_condition')->nullable();
            $table->string('allergy')->nullable();
            $table->string('hobby')->nullable();
            $table->string('extracurricular_interest')->nullable();
            $table->decimal('distance_to_school', 5, 2)->nullable();
            $table->smallInteger('transportation')->nullable();
            $table->string('previous_school')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_smp_sma_details');
    }
};
