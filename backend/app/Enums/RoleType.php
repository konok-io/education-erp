<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleType: string
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case ACCOUNTANT = 'accountant';
    case LIBRARIAN = 'librarian';
    case HOSTEL_WARDEN = 'hostel-warden';
    case TRANSPORT_INCHARGE = 'transport-incharge';
    case STAFF = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Administrator',
            self::TEACHER => 'Teacher',
            self::STUDENT => 'Student',
            self::ACCOUNTANT => 'Accountant',
            self::LIBRARIAN => 'Librarian',
            self::HOSTEL_WARDEN => 'Hostel Warden',
            self::TRANSPORT_INCHARGE => 'Transport Incharge',
            self::STAFF => 'Staff',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
