@props(['type' => 1, 'context' => 'attendance'])

@php
  $label = match($context) {
    'attendance'   => attendance_status_label($type),
    'student'      => student_status_label($type),
    'ppdb'         => ppdb_status_label($type),
    'registration' => registration_status_label($type),
    default        => $type,
  };

  $class = match($context) {
    'attendance'   => \App\Constants\AttendanceConstant::getStatusBadgeClass($type),
    'student'      => \App\Constants\StudentStatusConstant::getBadgeClass($type),
    'ppdb'         => \App\Constants\PpdbConstant::getBadgeClass($type),
    'registration' => \App\Constants\RegistrationConstant::getBadgeClass($type),
    default        => 'badge-green',
  };
@endphp

<span class="badge {{ $class }}">{{ $label }}</span>
