<?php

declare(strict_types=1);

namespace App\Enums\Security;

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
