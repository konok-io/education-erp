<?php

declare(strict_types=1);

namespace App\Enums\DevSecOps;

enum ArtifactType: string
{
    case DOCKER = 'docker';
    case NPM = 'npm';
    case COMPOSER = 'composer';
    case ANDROID_APK = 'android_apk';
    case ANDROID_AAB = 'android_aab';
    case ELECTRON = 'electron';
    case ARCHIVE = 'archive';
    case HELM = 'helm';

    public function label(): string
    {
        return match($this) {
            self::DOCKER => 'Docker Image',
            self::NPM => 'NPM Package',
            self::COMPOSER => 'Composer Package',
            self::ANDROID_APK => 'Android APK',
            self::ANDROID_AAB => 'Android AAB',
            self::ELECTRON => 'Electron App',
            self::ARCHIVE => 'Archive',
            self::HELM => 'Helm Chart',
        };
    }

    public function extension(): string
    {
        return match($this) {
            self::DOCKER => 'tar',
            self::NPM => 'tgz',
            self::COMPOSER => 'zip',
            self::ANDROID_APK => 'apk',
            self::ANDROID_AAB => 'aab',
            self::ELECTRON => 'exe',
            self::ARCHIVE => 'tar.gz',
            self::HELM => 'tgz',
        };
    }
}
