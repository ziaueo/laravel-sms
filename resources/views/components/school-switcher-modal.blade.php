{{-- Modal Ganti Sekolah — dipasang sekali di layouts/app.blade.php --}}
<div class="modal-backdrop" id="schoolSwitcherModal" data-list-url="{{ route('select.school.list') }}">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-building-school"></i> Ganti Sekolah</div>
      <button type="button" class="modal-close" data-ss-close aria-label="Tutup">
        <i class="ti ti-x"></i>
      </button>
    </div>

    <div class="modal-body">
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
<form method="POST" action="{{ route('select.school') }}" id="schoolSwitcherForm" data-no-confirm hidden>
  @csrf
  <input type="hidden" name="school_id" id="schoolSwitcherInput">
</form>
