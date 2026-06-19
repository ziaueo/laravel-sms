@props(['type' => 'success', 'message' => ''])

@php
  $icons = [
    'success' => 'ti-circle-check',
    'error'   => 'ti-circle-x',
    'warning' => 'ti-alert-triangle',
    'info'    => 'ti-info-circle',
  ];
  $classes = [
    'success' => 'alert-success',
    'error'   => 'alert-error',
    'warning' => 'alert-warning',
    'info'    => 'alert-info',
  ];
@endphp

<div class="alert {{ $classes[$type] ?? 'alert-info' }}" id="alertBox">
  <i class="ti {{ $icons[$type] ?? 'ti-info-circle' }}" style="font-size:18px;flex-shrink:0;"></i>
  <span>{{ $message }}</span>
  <button onclick="document.getElementById('alertBox').remove()"
          style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;color:inherit;">
    <i class="ti ti-x"></i>
  </button>
</div>
