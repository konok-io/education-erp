<?php

declare(strict_types=1);

namespace App\Helpers;

use NumberFormatter;

class NumberHelper
{
    /**
     * Format currency.
     */
    public static function formatCurrency(
        float|int $amount,
        string $currency = 'BDT',
        string $locale = 'bn_BD'
    ): string {
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currency);
    }

    /**
     * Format number with decimals.
     */
    public static function formatNumber(
        float|int $number,
        int $decimals = 2
    ): string {
        return number_format((float) $number, $decimals, '.', ',');
    }

    /**
     * Calculate percentage.
     */
    public static function percentage(
        float|int $value,
        float|int $total
    ): float {
        if ($total == 0) {
            return 0;
        }
        
        return round(($value / $total) * 100, 2);
    }

    /**
     * Format percentage.
     */
    public static function formatPercentage(
        float|int $value,
        int $decimals = 2
    ): string {
        return number_format((float) $value, $decimals) . '%';
    }

    /**
     * Convert to Bangla number.
     */
    public static function toBangla(string|int|float $number): string
    {
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bangla = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        
        return str_replace($english, $bangla, (string) $number);
    }

    /**
     * Convert to English number.
     */
    public static function toEnglish(string $number): string
    {
        $bangla = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        return str_replace($bangla, $english, $number);
    }

    /**
     * Format phone number.
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) == 11 && substr($phone, 0, 2) == '01') {
            return '+88' . $phone;
        }
        
        if (strlen($phone) == 10) {
            return '+88' . $phone;
        }
        
        return $phone;
    }

    /**
     * Generate admission number.
     */
    public static function generateAdmissionNo(int $year, int $serial): string
    {
        return $year . str_pad((string) $serial, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate employee ID.
     */
    public static function generateEmployeeId(string $prefix, int $serial): string
    {
        return $prefix . str_pad((string) $serial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate invoice number.
     */
    public static function generateInvoiceNo(int $year, string $prefix = 'INV'): string
    {
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        return $prefix . '-' . $year . '-' . strtoupper($random);
    }

    /**
     * Round to nearest.
     */
    public static function roundToNearest(float $value, float $nearest = 5): float
    {
        return round($value / $nearest) * $nearest;
    }

    /**
     * Calculate grade point.
     */
    public static function calculateGPA(array $marks): float
    {
        $totalPoints = 0;
        $totalCredits = 0;
        
        foreach ($marks as $mark) {
            $totalPoints += self::getGradePoint($mark['score']) * $mark['credit'];
            $totalCredits += $mark['credit'];
        }
        
        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
    }

    /**
     * Get grade point from score.
     */
    public static function getGradePoint(float $score): float
    {
        if ($score >= 80) return 5.0;
        if ($score >= 70) return 4.0;
        if ($score >= 60) return 3.5;
        if ($score >= 50) return 3.0;
        if ($score >= 40) return 2.0;
        if ($score >= 33) return 1.0;
        return 0.0;
    }

    /**
     * Get grade from score.
     */
    public static function getGrade(float $score): string
    {
        if ($score >= 80) return 'A+';
        if ($score >= 70) return 'A';
        if ($score >= 60) return 'A-';
        if ($score >= 50) return 'B';
        if ($score >= 40) return 'C';
        if ($score >= 33) return 'D';
        return 'F';
    }

    /**
     * Words for amount.
     */
    public static function numberToWords(float|int $number): string
    {
        $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($number));
    }
}
