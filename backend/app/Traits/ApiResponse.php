<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Return a success response.
     */
    protected function success(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = 200,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message ?? 'Operation successful',
            'data' => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return an error response.
     */
    protected function error(
        ?string $message = null,
        int $statusCode = 400,
        array $errors = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message ?? 'An error occurred',
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a paginated response.
     */
    protected function paginated(
        LengthAwarePaginator $paginator,
        ?string $message = null
    ): JsonResponse {
        $data = $paginator->items();

        return response()->json([
            'success' => true,
            'message' => $message ?? 'Data retrieved successfully',
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Return a created response.
     */
    protected function created(
        mixed $data = null,
        ?string $message = 'Resource created successfully'
    ): JsonResponse {
        return $this->success($data, $message, 201);
    }

    /**
     * Return an updated response.
     */
    protected function updated(
        mixed $data = null,
        ?string $message = 'Resource updated successfully'
    ): JsonResponse {
        return $this->success($data, $message);
    }

    /**
     * Return a deleted response.
     */
    protected function deleted(
        ?string $message = 'Resource deleted successfully'
    ): JsonResponse {
        return $this->success(null, $message);
    }

    /**
     * Return a not found response.
     */
    protected function notFound(
        ?string $message = 'Resource not found'
    ): JsonResponse {
        return $this->error($message, 404);
    }

    /**
     * Return a validation error response.
     */
    protected function validationError(
        array $errors,
        ?string $message = 'Validation failed'
    ): JsonResponse {
        return $this->error($message, 422, $errors);
    }

    /**
     * Return an unauthorized response.
     */
    protected function unauthorized(
        ?string $message = 'Unauthorized access'
    ): JsonResponse {
        return $this->error($message, 401);
    }

    /**
     * Return a forbidden response.
     */
    protected function forbidden(
        ?string $message = 'Access forbidden'
    ): JsonResponse {
        return $this->error($message, 403);
    }
}
