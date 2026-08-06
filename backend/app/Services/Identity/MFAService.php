<?php

declare(strict_types=1);

namespace App\Services\Identity;

use App\Enums\Identity\MFAType;
use App\Models\Identity\IdentityAuditLog;
use App\Models\Identity\MFAFactor;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ParagonIE\ConstantTime\Base32;

class MFAService
{
    public function getActiveFactorsForUser(string $userId): \Illuminate\Database\Eloquent\Collection
    {
        return MFAFactor::where('user_id', $userId)
            ->where('status', 'active')
            ->where('verified', true)
            ->get();
    }

    public function getFactorById(string $factorId): ?MFAFactor
    {
        return MFAFactor::find($factorId);
    }

    public function setupTOTP(User $user, string $name): array
    {
        $secret = $this->generateTOTPSecret();

        $factor = MFAFactor::create([
            'user_id' => $user->id,
            'name' => $name,
            'type' => MFAType::TOTP->value,
            'factor_type' => 'primary',
            'status' => 'active',
            'secret' => $secret,
            'authenticator_type' => 'google',
            'verified' => false,
        ]);

        $qrCodeUrl = $this->generateTOTPQrCodeUrl($user->email, $secret, 'Education ERP');

        return [
            'factor_id' => $factor->id,
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ];
    }

    public function setupSMS(User $user, string $name, string $phoneNumber): MFAFactor
    {
        $code = $this->generateOTP();

        $factor = MFAFactor::create([
            'user_id' => $user->id,
            'name' => $name,
            'type' => MFAType::SMS->value,
            'factor_type' => 'primary',
            'status' => 'active',
            'phone_number' => $phoneNumber,
            'verified' => false,
        ]);

        $this->sendSMSCode($phoneNumber, $code);
        $this->cacheOTPCode($factor->id, $code);

        return $factor;
    }

    public function setupEmail(User $user, string $name, string $email): MFAFactor
    {
        $code = $this->generateOTP();

        $factor = MFAFactor::create([
            'user_id' => $user->id,
            'name' => $name,
            'type' => MFAType::EMAIL->value,
            'factor_type' => 'primary',
            'status' => 'active',
            'email' => $email,
            'verified' => false,
        ]);

        $this->sendEmailCode($email, $code);
        $this->cacheOTPCode($factor->id, $code);

        return $factor;
    }

    public function verifySetup(MFAFactor $factor, string $code): bool
    {
        if ($factor->type === MFAType::TOTP->value) {
            $valid = $this->verifyTOTP($factor->secret, $code);
        } else {
            $valid = $this->verifyCachedOTP($factor->id, $code);
        }

        if ($valid) {
            $factor->verify();
            $this->clearCachedOTP($factor->id);

            IdentityAuditLog::log(
                eventType: IdentityAuditLog::EVENT_MFA_ENABLED,
                severity: 'info',
                category: IdentityAuditLog::CATEGORY_MFA,
                description: 'MFA factor verified and enabled',
                userId: $factor->user_id,
                eventData: [
                    'factor_type' => $factor->type,
                ],
            );
        }

        return $valid;
    }

    public function verifyCode(MFAFactor $factor, string $code): bool
    {
        if ($factor->type === MFAType::TOTP->value) {
            $valid = $this->verifyTOTP($factor->secret, $code);
        } else {
            $valid = $this->verifyCachedOTP($factor->id, $code);
        }

        if ($valid) {
            $factor->markUsed();
        }

        return $valid;
    }

    public function disableFactor(string $factorId, string $userId): bool
    {
        $factor = MFAFactor::where('id', $factorId)
            ->where('user_id', $userId)
            ->first();

        if (!$factor) {
            return false;
        }

        $factor->deactivate();

        IdentityAuditLog::log(
            eventType: IdentityAuditLog::EVENT_MFA_DISABLED,
            severity: 'info',
            category: IdentityAuditLog::CATEGORY_MFA,
            description: 'MFA factor disabled',
            userId: $factor->user_id,
            eventData: [
                'factor_type' => $factor->type,
            ],
        );

        return true;
    }

    public function generateBackupCodes(string $userId): array
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = $this->generateBackupCode();
        }

        Cache::put("mfa_backup_codes_{$userId}", $codes, now()->addDays(30));

        return $codes;
    }

    public function verifyBackupCode(string $userId, string $code): bool
    {
        $codes = Cache::get("mfa_backup_codes_{$userId}", []);

        $index = array_search($code, $codes);
        if ($index !== false) {
            unset($codes[$index]);
            Cache::put("mfa_backup_codes_{$userId}", array_values($codes), now()->addDays(30));
            return true;
        }

        return false;
    }

    protected function generateTOTPSecret(): string
    {
        return Base32::encodeUpper(random_bytes(20));
    }

    protected function generateTOTPQrCodeUrl(string $email, string $secret, string $issuer): string
    {
        $otpauthUrl = "otpauth://totp/{$issuer}:{$email}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauthUrl);
    }

    protected function verifyTOTP(string $secret, string $code): bool
    {
        $time = floor(time() / 30);

        for ($i = -1; $i <= 1; $i++) {
            $testTime = $time + $i;
            $expectedCode = $this->calculateTOTP($secret, $testTime);
            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    protected function calculateTOTP(string $secret, int $time): string
    {
        $secretKey = Base32::decodeUpper($secret);
        $timeBytes = pack('N*', 0) . pack('N*', $time);
        $hash = hash_hmac('sha1', $timeBytes, $secretKey, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;

        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }

    protected function generateOTP(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    protected function generateBackupCode(): string
    {
        return strtoupper(Str::random(4) . '-' . Str::random(4));
    }

    protected function cacheOTPCode(string $factorId, string $code): void
    {
        Cache::put("mfa_otp_{$factorId}", $code, now()->addMinutes(5));
    }

    protected function verifyCachedOTP(string $factorId, string $code): bool
    {
        $cachedCode = Cache::get("mfa_otp_{$factorId}");
        if ($cachedCode && hash_equals($cachedCode, $code)) {
            $this->clearCachedOTP($factorId);
            return true;
        }
        return false;
    }

    protected function clearCachedOTP(string $factorId): void
    {
        Cache::forget("mfa_otp_{$factorId}");
    }

    protected function sendSMSCode(string $phoneNumber, string $code): void
    {
        // Implement SMS sending logic
    }

    protected function sendEmailCode(string $email, string $code): void
    {
        // Implement email sending logic
    }
}
