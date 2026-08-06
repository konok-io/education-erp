<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibraryMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = LibraryMember::with(['user']);

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        if (!empty($request->member_type)) {
            $query->where('member_type', $request->member_type);
        }

        if (!empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $members = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $members->items(),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'per_page' => $members->perPage(),
                'total' => $members->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'member_type' => 'required|in:student,teacher,staff,researcher,guest,alumni',
            'user_id' => 'nullable|exists:users,id',
            'department' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'max_books' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $member = LibraryMember::create([
            'uuid' => (string) Str::uuid(),
            'member_id' => LibraryMember::generateMemberId(),
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'member_type' => $validated['member_type'],
            'user_id' => $validated['user_id'] ?? null,
            'department' => $validated['department'] ?? null,
            'institution' => $validated['institution'] ?? null,
            'address' => $validated['address'] ?? null,
            'join_date' => now(),
            'expiry_date' => $validated['expiry_date'] ?? now()->addYear(),
            'status' => LibraryMember::STATUS_ACTIVE,
            'max_books' => $validated['max_books'] ?? $this->getDefaultMaxBooks($validated['member_type']),
            'current_issued' => 0,
            'outstanding_fine' => 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json(['data' => $member], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $member = LibraryMember::with(['user', 'issues.bookCopy.book', 'fines'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $member]);
    }

    public function getMemberTypes(): JsonResponse
    {
        return response()->json(['data' => LibraryMember::memberTypes()]);
    }

    private function getDefaultMaxBooks(string $type): int
    {
        return match ($type) {
            LibraryMember::TYPE_TEACHER => 20,
            LibraryMember::TYPE_EMPLOYEE => 10,
            LibraryMember::TYPE_RESEARCHER => 15,
            LibraryMember::TYPE_GUEST => 2,
            default => 5,
        };
    }
}
