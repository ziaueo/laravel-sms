<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_user_pins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('attendance_devices')->cascadeOnDelete();
            $table->string('pin');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->smallInteger('role_context');
            $table->timestamps();

            $table->unique(['device_id', 'pin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_user_pins');
    }
};