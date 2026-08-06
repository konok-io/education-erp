<?php

declare(strict_types=1);

namespace App\Enums\DevSecOps;

enum SecurityScanType: string
{
    case SAST = 'sast';
    case DAST = 'dast';
    case SCA = 'sca';
    case SECRET = 'secret';
    case CONTAINER = 'container';
    case IAC = 'iac';
    case SBOM = 'sbom';
    case LICENSE = 'license';

    public function label(): string
    {
        return match($this) {
            self::SAST => 'Static Application Security Testing',
            self::DAST => 'Dynamic Application Security Testing',
            self::SCA => 'Software Composition Analysis',
            self::SECRET => 'Secret Scanning',
            self::CONTAINER => 'Container Scanning',
            self::IAC => 'Infrastructure as Code Scanning',
            self::SBOM => 'Software Bill of Materials',
            self::LICENSE => 'License Compliance',
        };
    }

    public function tool(): string
    {
        return match($this) {
            self::SAST => 'SonarQube',
            self::DAST => 'OWASP ZAP',
            self::SCA => 'Snyk',
            self::SECRET => 'Gitleaks',
            self::CONTAINER => 'Trivy',
            self::IAC => 'Checkov',
            self::SBOM => 'Syft',
            self::LICENSE => 'FOSSA',
        };
    }
}
