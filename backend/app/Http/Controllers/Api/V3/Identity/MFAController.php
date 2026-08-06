<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Identity;

use App\Http\Controllers\Controller;
use App\Services\Identity\MFAService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MFAController extends Controller
{
    public function __construct(
        protected MFAService $mfaService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $factors = $this->mfaService->getActiveFactorsForUser($userId);

        return response()->json([
            'success' => true,
            'data' => $factors->map(fn($factor) => [
                'id' => $factor->id,
                'name' => $factor->name,
                'type' => $factor->type,
                'factor_type' => $factor->factor_type,
                'status' => $factor->status,
                'verified' => $factor->verified,
                'default' => $factor->default,
                'backup' => $factor->backup,
                'verified_at' => $factor->verified_at?->toIso8601String(),
                'last_used_at' => $factor->last_used_at?->toIso8601String(),
            ]),
        ]);
    }

    public function setupTOTP(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->mfaService->setupTOTP(
            $request->user(),
            $request->input('name')
        );

        return response()->json([
            'success' => true,
            'message' => 'TOTP setup initiated',
            'data' => [
                'factor_id' => $result['factor_id'],
                'secret' => $result['secret'],
                'qr_code_url' => $result['qr_code_url'],
            ],
        ]);
    }

    public function verifySetup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'factor_id' => 'required|uuid',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $factor = $this->mfaService->getFactorById($request->input('factor_id'));

        if (!$factor || $factor->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'MFA factor not found',
            ], 404);
        }

        $verified = $this->mfaService->verifySetup($factor, $request->input('code'));

        if (!$verified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'MFA verified and enabled successfully',
        ]);
    }

    public function setupSMS(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|phone:AUTO',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $factor = $this->mfaService->setupSMS(
            $request->user(),
            $request->input('name'),
            $request->input('phone_number')
        );

        return response()->json([
            'success' => true,
            'message' => 'SMS verification code sent',
            'data' => [
                'factor_id' => $factor->id,
                'phone_number' => substr($factor->phone_number, 0, 4) . '****' . substr($factor->phone_number, -3),
            ],
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'factor_id' => 'required|uuid',
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $factor = $this->mfaService->getFactorById($request->input('factor_id'));

        if (!$factor || $factor->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'MFA factor not found',
            ], 404);
        }

        $verified = $this->mfaService->verifyCode($factor, $request->input('code'));

        if (!$verified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'MFA verification successful',
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $userId = $request->user()->id;
        $result = $this->mfaService->disableFactor($id, $userId);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'MFA factor not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'MFA factor disabled successfully',
        ]);
    }

    public function generateBackupCodes(Request $request): JsonResponse
    {
        $codes = $this->mfaService->generateBackupCodes($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Backup codes generated',
            'data' => [
                'codes' => $codes,
            ],
        ]);
    }
}
