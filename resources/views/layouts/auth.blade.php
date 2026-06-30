<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/png" href="{{ asset('images/logo_sms.png') }}">
  <title>@yield('title', 'Login') — {{ config('app.name') }}</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.44.0/dist/tabler-icons.min.css">

  @vite(['resources/css/app.css'])

  @stack('styles')
</head>
<body class="auth-body">

  @yield('content')

  @vite(['resources/js/app.js'])

  @stack('scripts')
</body>
</html>
