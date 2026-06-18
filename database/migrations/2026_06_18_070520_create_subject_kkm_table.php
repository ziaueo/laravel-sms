<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_kkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->decimal('kkm_score', 5, 2)->default(75.00);
            $table->timestamps();

            $table->unique(['subject_id', 'school_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_kkm');
    }
};