<?php

namespace api\modules\v1\swagger\response\schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AuthData',
    x: ['tags' => ['User Models', 'Schemas']],
    description: 'Данные авторизации и токен',
    properties: [
        new OA\Property(property: 'token', type: 'string', example: 'AxNUwFNNwn9qAOpMN7kbAPOJam3O491s'),
        new OA\Property(property: 'expired_at', type: 'string', example: '2026-06-14 13:27:28'),
        new OA\Property(
            property: 'user',
            ref: '#/components/schemas/AuthUser'
        ),
    ]
)]
class AuthDataSchema
{

}