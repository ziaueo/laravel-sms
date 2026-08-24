{{--
  Modal Ganti Sekolah — dipasang sekali di layouts/app.blade.php.

  Dua mode:
  - biasa  : dibuka dari tombol navbar / kartu sidebar, bisa ditutup
  - terkunci ($lockSchoolPicker): user belum punya sekolah aktif padahal
    punya lebih dari satu. Terbuka sejak halaman dimuat dan tidak bisa
    ditutup sampai satu sekolah dipilih. Ditandai oleh CheckActiveSchool.
--}}
@php $locked = $lockSchoolPicker ?? false; @endphp

<div class="modal-backdrop {{ $locked ? 'show' : '' }}"
     id="schoolSwitcherModal"
     data-list-url="{{ route('select.school.list') }}"
     @if($locked) data-locked @endif>
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">
        <i class="ti ti-building-school"></i>
        {{ $locked ? 'Pilih Sekolah' : 'Ganti Sekolah' }}
      </div>
      @unless($locked)
        <button type="button" class="modal-close" data-ss-close aria-label="Tutup"><i class="ti ti-x"></i></button>
      @endunless
    </div>

    <div class="modal-body">
      @if($locked)
        <p class="ss-note">
          Akunmu terhubung ke lebih dari satu sekolah. Pilih dulu sekolah yang ingin kamu kelola.
        </p>
      @endif

      <div class="ss-search">
        <i class="ti ti-search"></i>
        <input type="text" id="schoolSwitcherSearch" placeholder="Cari sekolah..." autocomplete="off">
      </div>

      <div class="ss-list" id="schoolSwitcherList"></div>
    </div>
  </div>
</div>

{{--
  Form pengirim. Dikirim lewat form.submit(), yang tidak memicu event 'submit',
  sehingga penyergap globalConfirmModal di layouts/app.blade.php terlewati dengan
  sendirinya. data-no-confirm dipasang supaya itu tetap benar kalau suatu saat
  diubah ke requestSubmit().
--}}
<form method="POST" action="{{ route('select.school.store') }}" id="schoolSwitcherForm" data-no-confirm hidden>
  @csrf
  <input type="hidden" name="school_id" id="schoolSwitcherInput">
</form>
