<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — {{ active_school()?->name ?? config('app.name') }}</title>

  {{-- Tabler Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.44.0/dist/tabler-icons.min.css">

  {{-- Vite CSS --}}
  @vite(['resources/css/app.css'])

  @stack('styles')
</head>
<body>

<div class="app-root">

  {{-- NAVBAR --}}
  @include('components.navbar')

  {{-- BODY --}}
  <div class="app-body">

    {{-- SIDEBAR OVERLAY (mobile) --}}
    <div class="sb-overlay" id="sbOverlay"></div>

    {{-- SIDEBAR --}}
    @include('components.sidebar')

    {{-- CONTENT --}}
    <div class="app-content" id="appContent">

      {{-- ALERT --}}
      @if(session('success'))
        <x-alert type="success" :message="session('success')" />
      @endif
      @if(session('error'))
        <x-alert type="error" :message="session('error')" />
      @endif
      @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" />
      @endif

      {{-- PAGE CONTENT --}}
      @yield('content')

    </div>
  </div>
</div>

{{-- Vite JS --}}
@vite(['resources/js/app.js'])

@stack('scripts')
</body>
</html>
