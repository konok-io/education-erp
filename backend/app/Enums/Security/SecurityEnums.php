<?php

declare(strict_types=1);

namespace App\Enums\Security;

enum VulnerabilitySeverity: string
{
    case CRITICAL = 'critical';
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';
    case INFO = 'info';

    public function label(): string
    {
        return match($this) {
            self::CRITICAL => 'Critical',
            self::HIGH => 'High',
            self::MEDIUM => 'Medium',
            self::LOW => 'Low',
            self::INFO => 'Info',
        };
    }

    public function score(): int
    {
        return match($this) {
            self::CRITICAL => 10,
            self::HIGH => 7,
            self::MEDIUM => 5,
            self::LOW => 2,
            self::INFO => 0,
        };
    }

    public function color(): string
    {
        return match($this) {
            self::CRITICAL => 'red',
            self::HIGH => 'orange',
            self::MEDIUM => 'yellow',
            self::LOW => 'blue',
            self::INFO => 'gray',
        };
    }
}

enum ComplianceStandard: string
{
    case GDPR = 'gdpr';
    case FERPA = 'ferpa';
    case SOC2 = 'soc2';
    case HIPAA = 'hipaa';
    case ISO27001 = 'iso27001';
    case PCI_DSS = 'pci_dss';

    public function label(): string
    {
        return match($this) {
            self::GDPR => 'GDPR',
            self::FERPA => 'FERPA',
            self::SOC2 => 'SOC 2',
            self::HIPAA => 'HIPAA',
            self::ISO27001 => 'ISO 27001',
            self::PCI_DSS => 'PCI DSS',
        };
    }
}

enum IncidentStatus: string
{
    case DETECTED = 'detected';
    case INVESTIGATING = 'investigating';
    case CONTAINED = 'contained';
    case ERADICATED = 'eradicated';
    case RECOVERED = 'recovered';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match($this) {
            self::DETECTED => 'Detected',
            self::INVESTIGATING => 'Investigating',
            self::CONTAINED => 'Contained',
            self::ERADICATED => 'Eradicated',
            self::RECOVERED => 'Recovered',
            self::CLOSED => 'Closed',
        };
    }
}

enum IncidentSeverity: string
{
    case P1_CRITICAL = 'p1_critical';
    case P2_HIGH = 'p2_high';
    case P3_MEDIUM = 'p3_medium';
    case P4_LOW = 'p4_low';

    public function label(): string
    {
        return match($this) {
            self::P1_CRITICAL => 'P1 - Critical',
            self::P2_HIGH => 'P2 - High',
            self::P3_MEDIUM => 'P3 - Medium',
            self::P4_LOW => 'P4 - Low',
        };
    }

    public function slaMinutes(): int
    {
        return match($this) {
            self::P1_CRITICAL => 15,
            self::P2_HIGH => 60,
            self::P3_MEDIUM => 240,
            self::P4_LOW => 1440,
        };
    }
}
