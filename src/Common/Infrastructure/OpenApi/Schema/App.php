<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\OpenApi\Schema;

use Exception;
use OpenApi\Attributes as OA;

#[
    OA\OpenApi(
        security: [['bearerAuth' => []]]
    ),
    OA\SecurityScheme(
        securityScheme: 'bearerAuth',
        type: 'http',
        scheme: 'bearer'
    ),
    OA\Server(url: 'http://localhost:8080', description: 'Local server'),
    OA\Info(version: '1.0', title: 'APP API'),
]
final class App
{
    public function __construct()
    {
        throw new Exception('stub class for api docs');
    }
}
