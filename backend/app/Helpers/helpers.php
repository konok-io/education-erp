<?php

declare(strict_types=1);

use Illuminate\Support\Str;

/**
 * Generate UUID v4.
 */
if (!function_exists('generate_uuid')) {
    function generate_uuid(): string
    {
        return (string) Str::uuid();
    }
}

/**
 * Get current campus ID.
 */
if (!function_exists('current_campus_id')) {
    function current_campus_id(): ?int
    {
        return auth()->check() ? auth()->user()->campus_id : null;
    }
}

/**
 * Get current academic session ID.
 */
if (!function_exists('current_session_id')) {
    function current_session_id(): ?int
    {
        $session = \App\Models\AcademicSession::where('is_current', true)->first();
        return $session?->id;
    }
}

/**
 * Check if user is super admin.
 */
if (!function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        return auth()->check() && auth()->user()->email === config('auth.super_admin_email');
    }
}

/**
 * Check if user is admin.
 */
if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();
        return $user->role && in_array($user->role->name, ['super-admin', 'admin']);
    }
}

/**
 * Format date.
 */
if (!function_exists('format_date')) {
    function format_date($date, string $format = 'd M Y'): ?string
    {
        if (!$date) {
            return null;
        }

        return \Carbon\Carbon::parse($date)->format($format);
    }
}

/**
 * Format datetime.
 */
if (!function_exists('format_datetime')) {
    function format_datetime($datetime, string $format = 'd M Y, h:i A'): ?string
    {
        if (!$datetime) {
            return null;
        }

        return \Carbon\Carbon::parse($datetime)->format($format);
    }
}

/**
 * Format currency.
 */
if (!function_exists('format_currency')) {
    function format_currency(float|int $amount, string $symbol = '৳'): string
    {
        return $symbol . number_format((float) $amount, 2);
    }
}

/**
 * Get status color.
 */
if (!function_exists('status_color')) {
    function status_color(string $status): string
    {
        return match ($status) {
            'active', 'approved', 'published', 'paid', 'present' => 'success',
            'inactive', 'archived', 'closed' => 'secondary',
            'pending', 'draft', 'processing' => 'warning',
            'rejected', 'suspended', 'cancelled', 'absent' => 'danger',
            'excused', 'partial' => 'info',
            default => 'dark',
        };
    }
}

/**
 * Get avatar URL.
 */
if (!function_exists('avatar_url')) {
    function avatar_url($user): ?string
    {
        if (!$user) {
            return null;
        }

        return $user->avatar 
            ? asset('storage/' . $user->avatar)
            : null;
    }
}

/**
 * Get initials from name.
 */
if (!function_exists('get_initials')) {
    function get_initials(string $name): string
    {
        $words = explode(' ', $name);
        $initials = '';
        
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return $initials;
    }
}

/**
 * Sanitize filename.
 */
if (!function_exists('sanitize_filename')) {
    function sanitize_filename(string $filename): string
    {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        return time() . '_' . $filename;
    }
}

/**
 * Truncate text.
 */
if (!function_exists('truncate_text')) {
    function truncate_text(string $text, int $length = 100): string
    {
        return Str::limit($text, $length, '...');
    }
}

/**
 * Get file extension.
 */
if (!function_exists('get_file_extension')) {
    function get_file_extension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}

/**
 * Check if file is image.
 */
if (!function_exists('is_image_file')) {
    function is_image_file(string $filename): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        return in_array(get_file_extension($filename), $imageExtensions);
    }
}

/**
 * Check if file is PDF.
 */
if (!function_exists('is_pdf_file')) {
    function is_pdf_file(string $filename): bool
    {
        return get_file_extension($filename) === 'pdf';
    }
}

/**
 * Get app setting.
 */
if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        $settings = cache()->remember('settings', 3600, function () {
            return \App\Models\Setting::pluck('value', 'key')->toArray();
        });

        $value = $settings[$key] ?? $default;

        return is_string($value) && is_array(json_decode($value, true)) 
            ? json_decode($value, true) 
            : $value;
    }
}

/**
 * Log activity.
 */
if (!function_exists('log_activity')) {
    function log_activity(
        string $description,
        $model = null,
        ?string $action = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        activity()
            ->performedOn($model ?? null)
            ->withProperties([
                'old_values' => $oldValues,
                'new_values' => $newValues,
            ])
            ->log($description);
    }
}
