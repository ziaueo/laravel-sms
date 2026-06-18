<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_paud_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->boolean('special_needs')->default(false);
            $table->text('special_needs_description')->nullable();
            $table->text('health_condition')->nullable();
            $table->string('allergy')->nullable();
            $table->string('pediatrician_name')->nullable();
            $table->jsonb('vaccine_status')->nullable();
            $table->boolean('is_toilet_trained')->default(false);
            $table->string('language_at_home')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_paud_details');
    }
};