<?php

declare(strict_types=1);

namespace App\Enums\APIGateway;

enum HTTPMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';

    public function label(): string
    {
        return $this->value;
    }
}
