<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('phone')->nullable()->after('avatar');
            $table->boolean('is_active')->default(true)->after('phone');
            $table->boolean('must_change_password')->default(false)->after('is_active');
            $table->unsignedBigInteger('registered_by')->nullable()->after('must_change_password');
            $table->timestamp('last_login_at')->nullable()->after('registered_by');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'phone',
                'is_active',
                'must_change_password',
                'registered_by',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};
