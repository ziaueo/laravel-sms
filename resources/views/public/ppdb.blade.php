@extends('layouts.public')

@section('title', 'PPDB Online')

@section('content')

<section class="phead">
  <div class="pcontainer">
    <h1>PPDB Online</h1>
    <p>Pendaftaran Peserta Didik Baru {{ $school->name }}</p>
  </div>
</section>

<section class="psec">
  <div class="pcontainer" style="max-width:800px;">

    @if(session('success'))
      <div class="palert"><i class="ti ti-circle-check"></i> <div>{{ session('success') }}</div></div>
    @endif
    @if(session('error'))
      <div class="palert palert-err"><i class="ti ti-alert-circle"></i> <div>{{ session('error') }}</div></div>
    @endif
    @if($errors->any())
      <div class="palert palert-err">
        <i class="ti ti-alert-circle"></i>
        <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
      </div>
    @endif

    @if(!$period)
      <div class="pinfo" style="text-align:center;padding:48px 26px;">
        <div class="pinfo-icon" style="margin:0 auto 14px;"><i class="ti ti-calendar-off"></i></div>
        <h3>Pendaftaran Belum Dibuka</h3>
        <p>Saat ini tidak ada gelombang PPDB yang sedang dibuka. Silakan cek kembali nanti.</p>
      </div>
    @else
      <div class="palert">
        <i class="ti ti-info-circle"></i>
        <div>Gelombang <strong>{{ $period->name }}</strong> — ditutup {{ format_date($period->close_date) }}</div>
      </div>

      <form method="POST" action="{{ route('public.ppdb.store', $school->slug) }}" class="pinfo" style="margin-top:18px;">
        @csrf

        <div class="psec-head" style="margin-bottom:20px;">
          <div class="psec-title" style="font-size:19px;">Data Calon Siswa</div>
        </div>

        <div class="pform-row">
          <label>Nama Lengkap <span style="color:var(--color-danger);">*</span></label>
          <input type="text" name="full_name" value="{{ old('full_name') }}" required>
        </div>

        <div class="pgrid pgrid-2" style="gap:0 18px;">
          <div class="pform-row">
            <label>Jenis Kelamin <span style="color:var(--color-danger);">*</span></label>
            <select name="gender" required>
              <option value="">-- Pilih --</option>
              @foreach(\App\Constants\GenderConstant::getAll() as $v => $l)
                <option value="{{ $v }}" {{ old('gender') == $v ? 'selected' : '' }}>{{ $l }}</option>
              @endforeach
            </select>
          </div>
          <div class="pform-row">
            <label>Agama</label>
            <select name="religion">
              <option value="">-- Pilih --</option>
              @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $r)
                <option {{ old('religion') == $r ? 'selected' : '' }}>{{ $r }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="pgrid pgrid-2" style="gap:0 18px;">
          <div class="pform-row">
            <label>Tempat Lahir</label>
            <input type="text" name="birth_place" value="{{ old('birth_place') }}">
          </div>
          <div class="pform-row">
            <label>Tanggal Lahir</label>
            <input type="date" name="birth_date" value="{{ old('birth_date') }}">
          </div>
        </div>

        <div class="pform-row">
          <label>Asal Sekolah</label>
          <input type="text" name="previous_school" value="{{ old('previous_school') }}">
        </div>

        <div class="pform-row">
          <label>Alamat</label>
          <textarea name="address" rows="2">{{ old('address') }}</textarea>
        </div>

        <div class="psec-head" style="margin:28px 0 20px;">
          <div class="psec-title" style="font-size:19px;">Data Orang Tua / Wali</div>
        </div>

        <div class="pgrid pgrid-2" style="gap:0 18px;">
          <div class="pform-row">
            <label>Nama Orang Tua/Wali <span style="color:var(--color-danger);">*</span></label>
            <input type="text" name="parent_name" value="{{ old('parent_name') }}" required>
          </div>
          <div class="pform-row">
            <label>Hubungan <span style="color:var(--color-danger);">*</span></label>
            <select name="parent_relation" required>
              @foreach(\App\Constants\ParentRelationConstant::getAll() as $v => $l)
                <option value="{{ $v }}" {{ old('parent_relation') == $v ? 'selected' : '' }}>{{ $l }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="pgrid pgrid-2" style="gap:0 18px;">
          <div class="pform-row">
            <label>No. Telepon <span style="color:var(--color-danger);">*</span></label>
            <input type="text" name="parent_phone" value="{{ old('parent_phone') }}" required>
          </div>
          <div class="pform-row">
            <label>Email</label>
            <input type="email" name="parent_email" value="{{ old('parent_email') }}">
          </div>
        </div>

        <div class="pform-row">
          <label>Pekerjaan</label>
          <input type="text" name="parent_job" value="{{ old('parent_job') }}">
        </div>

        <button type="submit" class="pbtn" style="margin-top:10px;">
          <i class="ti ti-send"></i> Kirim Pendaftaran
        </button>
      </form>
    @endif

  </div>
</section>

@endsection
