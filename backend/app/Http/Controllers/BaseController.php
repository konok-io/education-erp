<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

abstract class BaseController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse;

    /**
     * Return a success response.
     */
    protected function ok(
        mixed $data = null,
        ?string $message = null
    ): JsonResponse {
        return $this->success($data, $message);
    }

    /**
     * Return a created response.
     */
    protected function created(
        mixed $data = null,
        ?string $message = null
    ): JsonResponse {
        return $this->success($data, $message ?? 'Created successfully', 201);
    }

    /**
     * Return an updated response.
     */
    protected function updated(
        mixed $data = null,
        ?string $message = null
    ): JsonResponse {
        return $this->success($data, $message ?? 'Updated successfully');
    }

    /**
     * Return a deleted response.
     */
    protected function deleted(?string $message = null): JsonResponse
    {
        return $this->success(null, $message ?? 'Deleted successfully');
    }

    /**
     * Return a not found response.
     */
    protected function notFound(?string $message = null): JsonResponse
    {
        return $this->error($message ?? 'Not found', 404);
    }

    /**
     * Return a forbidden response.
     */
    protected function forbidden(?string $message = null): JsonResponse
    {
        return $this->error($message ?? 'Forbidden', 403);
    }

    /**
     * Return an unauthorized response.
     */
    protected function unauthorized(?string $message = null): JsonResponse
    {
        return $this->error($message ?? 'Unauthorized', 401);
    }
}
