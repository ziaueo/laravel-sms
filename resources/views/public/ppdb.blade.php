@extends('layouts.public')

@section('title', 'PPDB Online')

@section('content')

<section class="hero" style="padding:50px 0;">
  <div class="container"><h1 style="font-size:30px;">PPDB Online</h1>
    <p>Pendaftaran Peserta Didik Baru {{ $school->name }}</p></div>
</section>

<section class="sec">
  <div class="container" style="max-width:760px;">

    @if(session('success'))<div class="palert"><i class="ti ti-circle-check"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="palert palert-err"><i class="ti ti-alert-circle"></i> {{ session('error') }}</div>@endif
    @if($errors->any())
      <div class="palert palert-err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    @if(!$period)
      <div class="vmcard" style="text-align:center;">
        <div class="ic" style="margin:0 auto 12px;"><i class="ti ti-calendar-off"></i></div>
        <h3>Pendaftaran Belum Dibuka</h3>
        <p style="color:var(--muted);">Saat ini tidak ada gelombang PPDB yang sedang dibuka. Silakan cek kembali nanti.</p>
      </div>
    @else
      <div class="palert"><i class="ti ti-info-circle"></i> Gelombang: <strong>{{ $period->name }}</strong> · Ditutup {{ format_date($period->close_date) }}</div>

      <form method="POST" action="{{ route('public.ppdb.store', $school->slug) }}" class="vmcard">
        @csrf
        <h3 style="margin-bottom:16px;">Data Calon Siswa</h3>
        <div class="form-row"><label>Nama Lengkap *</label><input type="text" name="full_name" value="{{ old('full_name') }}" required></div>
        <div class="grid grid-2">
          <div class="form-row"><label>Jenis Kelamin *</label>
            <select name="gender" required><option value="">-- Pilih --</option>
              @foreach(\App\Constants\GenderConstant::getAll() as $v=>$l)<option value="{{ $v }}" {{ old('gender')==$v?'selected':'' }}>{{ $l }}</option>@endforeach
            </select></div>
          <div class="form-row"><label>Agama</label>
            <select name="religion"><option value="">-- Pilih --</option>
              @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $r)<option {{ old('religion')==$r?'selected':'' }}>{{ $r }}</option>@endforeach
            </select></div>
        </div>
        <div class="grid grid-2">
          <div class="form-row"><label>Tempat Lahir</label><input type="text" name="birth_place" value="{{ old('birth_place') }}"></div>
          <div class="form-row"><label>Tanggal Lahir</label><input type="date" name="birth_date" value="{{ old('birth_date') }}"></div>
        </div>
        <div class="form-row"><label>Asal Sekolah</label><input type="text" name="previous_school" value="{{ old('previous_school') }}"></div>
        <div class="form-row"><label>Alamat</label><textarea name="address" rows="2">{{ old('address') }}</textarea></div>

        <h3 style="margin:20px 0 16px;">Data Orang Tua/Wali</h3>
        <div class="grid grid-2">
          <div class="form-row"><label>Nama Orang Tua/Wali *</label><input type="text" name="parent_name" value="{{ old('parent_name') }}" required></div>
          <div class="form-row"><label>Hubungan *</label>
            <select name="parent_relation" required>
              @foreach(\App\Constants\ParentRelationConstant::getAll() as $v=>$l)<option value="{{ $v }}" {{ old('parent_relation')==$v?'selected':'' }}>{{ $l }}</option>@endforeach
            </select></div>
        </div>
        <div class="grid grid-2">
          <div class="form-row"><label>No. Telepon *</label><input type="text" name="parent_phone" value="{{ old('parent_phone') }}" required></div>
          <div class="form-row"><label>Email</label><input type="email" name="parent_email" value="{{ old('parent_email') }}"></div>
        </div>
        <div class="form-row"><label>Pekerjaan</label><input type="text" name="parent_job" value="{{ old('parent_job') }}"></div>

        <button type="submit" class="pbtn" style="margin-top:8px;"><i class="ti ti-send"></i> Kirim Pendaftaran</button>
      </form>
    @endif

  </div>
</section>

@endsection
