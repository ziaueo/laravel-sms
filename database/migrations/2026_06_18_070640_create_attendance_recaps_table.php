<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_recaps', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('recap_type');
            $table->unsignedBigInteger('reference_id');
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->smallInteger('month');
            $table->year('year');
            $table->smallInteger('total_days')->default(0);
            $table->smallInteger('total_hadir')->default(0);
            $table->smallInteger('total_sakit')->default(0);
            $table->smallInteger('total_izin')->default(0);
            $table->smallInteger('total_alpa')->default(0);
            $table->timestamps();

            $table->unique(['recap_type', 'reference_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_recaps');
    }
};