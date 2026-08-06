<?php

declare(strict_types=1);

namespace App\Enums\SmartCampus;

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
