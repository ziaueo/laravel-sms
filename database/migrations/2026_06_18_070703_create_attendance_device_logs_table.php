<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_device_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('attendance_devices')->cascadeOnDelete();
            $table->string('pin');
            $table->timestamp('punch_time');
            $table->smallInteger('type');
            $table->smallInteger('role_context')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_device_logs');
    }
};