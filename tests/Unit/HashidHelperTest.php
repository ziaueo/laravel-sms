<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Models\Teacher;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class HashidHelperTest extends TestCase
{
    public function test_encode_decode_round_trip(): void
    {
        $hash = hashid_encode(123, Student::class);

        $this->assertNotEmpty($hash);
        $this->assertNotEquals('123', $hash);          // tidak boleh telanjang
        $this->assertSame(123, hashid_decode($hash, Student::class));
    }

    public function test_same_id_different_context_produces_different_hash(): void
    {
        $studentHash = hashid_encode(5, Student::class);
        $teacherHash = hashid_encode(5, Teacher::class);

        // ID sama, model beda → hash harus beda (anti korelasi antar-tabel)
        $this->assertNotEquals($studentHash, $teacherHash);

        // Hash milik Student tidak boleh "nyasar" resolve ke ID 5 di konteks Teacher
        $this->assertNotSame(5, hashid_decode($studentHash, Teacher::class));
    }

    public function test_invalid_hash_returns_null(): void
    {
        $this->assertNull(hashid_decode('bukan-hash-valid', Student::class));
        $this->assertNull(hashid_decode('', Student::class));
        $this->assertNull(hashid_decode(null, Student::class));
    }

    public function test_decode_or_404_aborts_on_invalid_hash(): void
    {
        $this->expectException(NotFoundHttpException::class);
        hashid_decode_or_404('bukan-hash-valid', Student::class);
    }

    public function test_hid_helper_matches_hashid_encode(): void
    {
        $student = new Student();
        $student->id = 77;

        $this->assertSame(hashid_encode(77, Student::class), hid($student));
        $this->assertSame(77, hashid_decode(hid($student), Student::class));
    }
}
