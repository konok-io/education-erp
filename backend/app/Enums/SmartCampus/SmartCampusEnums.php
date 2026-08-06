<?php

declare(strict_types=1);

namespace App\Enums\SmartCampus;

enum IoTStatus: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case MAINTENANCE = 'maintenance';
    case ERROR = 'error';

    public function label(): string
    {
        return match($this) {
            self::ONLINE => 'Online',
            self::OFFLINE => 'Offline',
            self::MAINTENANCE => 'Maintenance',
            self::ERROR => 'Error',
        };
    }
}

enum DeviceStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case MALFUNCTION = 'malfunction';
    case REPLACED = 'replaced';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::MALFUNCTION => 'Malfunction',
            self::REPLACED => 'Replaced',
        };
    }
}

enum DeviceType: string
{
    case CAMERA = 'camera';
    case SENSOR = 'sensor';
    case ACCESS_CONTROL = 'access_control';
    case ATTENDANCE = 'attendance';
    case ENVIRONMENTAL = 'environmental';
    case ENERGY = 'energy';
    case PARKING = 'parking';

    public function label(): string
    {
        return match($this) {
            self::CAMERA => 'Camera',
            self::SENSOR => 'Sensor',
            self::ACCESS_CONTROL => 'Access Control',
            self::ATTENDANCE => 'Attendance',
            self::ENVIRONMENTAL => 'Environmental',
            self::ENERGY => 'Energy',
            self::PARKING => 'Parking',
        };
    }
}
