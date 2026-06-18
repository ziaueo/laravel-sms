<?php

use App\Constants\RoleConstant;
use App\Constants\AttendanceConstant;
use App\Constants\GenderConstant;
use App\Constants\StudentStatusConstant;
use App\Constants\MutationConstant;
use App\Constants\EmploymentConstant;
use App\Constants\ScheduleConstant;
use App\Constants\SubstitutionConstant;
use App\Constants\PpdbConstant;
use App\Constants\RegistrationConstant;
use App\Constants\NotificationConstant;
use App\Constants\CurriculumConstant;
use App\Constants\GalleryConstant;
use App\Constants\RecapTypeConstant;
use App\Constants\DeviceLogConstant;
use App\Constants\ActivityConstant;
use App\Constants\PositionConstant;

// ── ROLE ──────────────────────────────────────────
if (!function_exists('role_label')) {
    function role_label(int $value): string {
        return RoleConstant::getLabel($value);
    }
}

// ── GENDER ────────────────────────────────────────
if (!function_exists('gender_label')) {
    function gender_label(int $value): string {
        return GenderConstant::getLabel($value);
    }
}

// ── ATTENDANCE ────────────────────────────────────
if (!function_exists('attendance_status_label')) {
    function attendance_status_label(int $value): string {
        return AttendanceConstant::getStatusLabel($value);
    }
}

if (!function_exists('attendance_badge')) {
    function attendance_badge(int $value): string {
        $class = AttendanceConstant::getStatusBadgeClass($value);
        $label = AttendanceConstant::getStatusLabel($value);
        return "<span class='badge {$class}'>{$label}</span>";
    }
}

if (!function_exists('attendance_source_label')) {
    function attendance_source_label(int $value): string {
        return AttendanceConstant::getSourceLabel($value);
    }
}

// ── STUDENT ───────────────────────────────────────
if (!function_exists('student_status_label')) {
    function student_status_label(int $value): string {
        return StudentStatusConstant::getLabel($value);
    }
}

if (!function_exists('student_status_badge')) {
    function student_status_badge(int $value): string {
        $class = StudentStatusConstant::getBadgeClass($value);
        $label = StudentStatusConstant::getLabel($value);
        return "<span class='badge {$class}'>{$label}</span>";
    }
}

// ── EMPLOYMENT ────────────────────────────────────
if (!function_exists('employment_label')) {
    function employment_label(int $value): string {
        return EmploymentConstant::getLabel($value);
    }
}

// ── SCHEDULE ──────────────────────────────────────
if (!function_exists('schedule_type_label')) {
    function schedule_type_label(int $value): string {
        return ScheduleConstant::getTypeLabel($value);
    }
}

if (!function_exists('day_label')) {
    function day_label(int $value): string {
        return ScheduleConstant::getDayLabel($value);
    }
}

// ── PPDB ──────────────────────────────────────────
if (!function_exists('ppdb_status_label')) {
    function ppdb_status_label(int $value): string {
        return PpdbConstant::getLabel($value);
    }
}

if (!function_exists('ppdb_status_badge')) {
    function ppdb_status_badge(int $value): string {
        $class = PpdbConstant::getBadgeClass($value);
        $label = PpdbConstant::getLabel($value);
        return "<span class='badge {$class}'>{$label}</span>";
    }
}

// ── REGISTRATION ──────────────────────────────────
if (!function_exists('registration_status_label')) {
    function registration_status_label(int $value): string {
        return RegistrationConstant::getLabel($value);
    }
}

if (!function_exists('registration_status_badge')) {
    function registration_status_badge(int $value): string {
        $class = RegistrationConstant::getBadgeClass($value);
        $label = RegistrationConstant::getLabel($value);
        return "<span class='badge {$class}'>{$label}</span>";
    }
}

// ── MUTATION ──────────────────────────────────────
if (!function_exists('mutation_type_label')) {
    function mutation_type_label(int $value): string {
        return MutationConstant::getTypeLabel($value);
    }
}

if (!function_exists('mutation_status_label')) {
    function mutation_status_label(int $value): string {
        return MutationConstant::getStatusLabel($value);
    }
}

// ── CURRICULUM ────────────────────────────────────
if (!function_exists('curriculum_label')) {
    function curriculum_label(int $value): string {
        return CurriculumConstant::getLabel($value);
    }
}

// ── GALLERY ───────────────────────────────────────
if (!function_exists('gallery_type_label')) {
    function gallery_type_label(int $value): string {
        return GalleryConstant::getLabel($value);
    }
}

// ── NOTIFICATION ──────────────────────────────────
if (!function_exists('notification_type_label')) {
    function notification_type_label(int $value): string {
        return NotificationConstant::getLabel($value);
    }
}

// ── DEVICE LOG ────────────────────────────────────
if (!function_exists('device_log_label')) {
    function device_log_label(int $value): string {
        return DeviceLogConstant::getLabel($value);
    }
}

// ── RECAP TYPE ────────────────────────────────────
if (!function_exists('recap_type_label')) {
    function recap_type_label(int $value): string {
        return RecapTypeConstant::getLabel($value);
    }
}

// ── ACTIVITY ──────────────────────────────────────
if (!function_exists('activity_label')) {
    function activity_label(int $value): string {
        return ActivityConstant::getLabel($value);
    }
}

// ── POSITION ──────────────────────────────────────
if (!function_exists('position_type_label')) {
    function position_type_label(int $value): string {
        return PositionConstant::getLabel($value);
    }
}

// ── SUBSTITUTION ──────────────────────────────────
if (!function_exists('substitution_label')) {
    function substitution_label(int $value): string {
        return SubstitutionConstant::getLabel($value);
    }
}

// ── UTILITY ───────────────────────────────────────
if (!function_exists('active_school')) {
    function active_school(): ?object {
        $id = session('active_school_id');
        if (!$id) return null;
        return \App\Models\School::find($id);
    }
}

if (!function_exists('initials')) {
    function initials(string $name): string {
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $date, string $format = 'd M Y'): string {
        if (!$date) return '-';
        return \Carbon\Carbon::parse($date)->translatedFormat($format);
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime(?string $date): string {
        if (!$date) return '-';
        return \Carbon\Carbon::parse($date)->translatedFormat('d M Y, H:i');
    }
}

if (!function_exists('time_ago')) {
    function time_ago(?string $date): string {
        if (!$date) return '-';
        return \Carbon\Carbon::parse($date)->diffForHumans();
    }
}
