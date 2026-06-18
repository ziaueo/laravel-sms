<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('activity_categories')->nullOnDelete();
            $table->string('title');
            $table->text('content')->nullable();
            $table->jsonb('media')->nullable();
            $table->date('activity_date');
            $table->boolean('is_published')->default(false);
            $table->boolean('show_in_feed')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_activities');
    }
};