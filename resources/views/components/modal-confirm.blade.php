@props(['id' => 'modalConfirm', 'title' => 'Konfirmasi', 'message' => 'Apakah kamu yakin?', 'action' => '#', 'method' => 'DELETE'])

<div class="modal-backdrop" id="{{ $id }}" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:999;backdrop-filter:blur(2px);align-items:center;justify-content:center;">
  <div class="modal-box" style="background:#fff;border-radius:16px;padding:24px;width:100%;max-width:400px;margin:16px;box-shadow:0 20px 40px rgba(0,0,0,0.15);">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
      <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#fee2e2,#fecaca);display:flex;align-items:center;justify-content:center;">
        <i class="ti ti-alert-triangle" style="color:#991b1b;font-size:20px;"></i>
      </div>
      <div>
        <div style="font-size:14px;font-weight:700;color:#1b2e24;">{{ $title }}</div>
        <div style="font-size:12px;color:#6c8f7a;">{{ $message }}</div>
      </div>
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;">
      <button onclick="document.getElementById('{{ $id }}').style.display='none'"
              class="btn btn-outline btn-sm">
        Batal
      </button>
      <form id="{{ $id }}Form" method="POST" action="{{ $action }}">
        @csrf
        @if($method !== 'POST')
          @method($method)
        @endif
        <button type="submit" class="btn btn-danger btn-sm">
          Ya, Lanjutkan
        </button>
      </form>
    </div>
  </div>
</div>
