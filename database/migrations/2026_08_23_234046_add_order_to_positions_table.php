<?php

use App\Constants\PositionConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Urutan diberi jarak supaya jabatan baru bisa disisipkan tanpa menomori ulang.
     * name => [type, order]
     */
    private const POSITIONS = [
        'Kepala Sekolah'        => [PositionConstant::PIMPINAN, 1],
        'Wakil Kepala Sekolah'  => [PositionConstant::PIMPINAN, 2],
        'Guru Kelas'            => [PositionConstant::GURU,     10],
        'Guru Mata Pelajaran'   => [PositionConstant::GURU,     11],
        'Guru BK'               => [PositionConstant::GURU,     12],
        'Staff TU'              => [PositionConstant::STAFF,    20],
        'Bendahara'             => [PositionConstant::STAFF,    21],
        'Pustakawan'            => [PositionConstant::STAFF,    22],
        'Satpam'                => [PositionConstant::STAFF,    23],
        'Penjaga Sekolah'       => [PositionConstant::STAFF,    24],
    ];

    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->smallInteger('order')->default(0)->after('type');
        });

        // Dikerjakan di sini, bukan di seeder: PositionSeeder memakai firstOrCreate
        // yang tidak menyentuh baris yang sudah ada.
        foreach (self::POSITIONS as $name => [$type, $order]) {
            DB::table('positions')
                ->where('name', $name)
                ->update(['type' => $type, 'order' => $order]);
        }
    }

    public function down(): void
    {
        // Kembalikan pimpinan ke Staff seperti sebelumnya.
        DB::table('positions')
            ->whereIn('name', ['Kepala Sekolah', 'Wakil Kepala Sekolah'])
            ->update(['type' => PositionConstant::STAFF]);

        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
