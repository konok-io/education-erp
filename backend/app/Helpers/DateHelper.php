<?php

declare(strict_types=1);

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class DateHelper
{
    /**
     * Format date for display.
     */
    public static function formatDate(
        string|Carbon|null $date,
        string $format = 'd M Y'
    ): ?string {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format($format);
    }

    /**
     * Format date with time.
     */
    public static function formatDateTime(
        string|Carbon|null $datetime,
        string $format = 'd M Y, h:i A'
    ): ?string {
        if (!$datetime) {
            return null;
        }

        return Carbon::parse($datetime)->format($format);
    }

    /**
     * Get human readable date.
     */
    public static function humanReadable(
        string|Carbon|null $date
    ): ?string {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->diffForHumans();
    }

    /**
     * Convert to ISO 8601 format.
     */
    public static function toIso8601(string|Carbon|null $date): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->toIso8601String();
    }

    /**
     * Get start of day.
     */
    public static function startOfDay(string|Carbon $date): Carbon
    {
        return Carbon::parse($date)->startOfDay();
    }

    /**
     * Get end of day.
     */
    public static function endOfDay(string|Carbon $date): Carbon
    {
        return Carbon::parse($date)->endOfDay();
    }

    /**
     * Get age from date of birth.
     */
    public static function getAge(string|Carbon $dob): int
    {
        return Carbon::parse($dob)->age;
    }

    /**
     * Check if date is today.
     */
    public static function isToday(string|Carbon $date): bool
    {
        return Carbon::parse($date)->isToday();
    }

    /**
     * Check if date is weekend.
     */
    public static function isWeekend(string|Carbon $date): bool
    {
        return Carbon::parse($date)->isWeekend();
    }

    /**
     * Get academic year from date.
     */
    public static function getAcademicYear(string|Carbon $date): string
    {
        $year = Carbon::parse($date)->year;
        return $year . '-' . ($year + 1);
    }

    /**
     * Get Bangla date format.
     */
    public static function banglaDate(string|Carbon|null $date): ?string
    {
        if (!$date) {
            return null;
        }

        // Simple conversion - can be enhanced with proper Bangla conversion
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bangla = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

        $formatted = self::formatDate($date);
        return str_replace($english, $bangla, $formatted);
    }
}
