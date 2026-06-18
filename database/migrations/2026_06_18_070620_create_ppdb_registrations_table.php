<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->foreignId('ppdb_period_id')->constrained('ppdb_periods')->cascadeOnDelete();
            $table->string('registration_number')->unique();
            $table->string('full_name');
            $table->smallInteger('gender');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->text('address')->nullable();
            $table->string('previous_school')->nullable();
            $table->jsonb('documents')->nullable();
            $table->string('parent_name');
            $table->smallInteger('parent_relation');
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();
            $table->string('parent_job')->nullable();
            $table->text('parent_address')->nullable();
            $table->smallInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_registrations');
    }
};