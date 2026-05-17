<?php

namespace api\modules\v1\swagger\response\schemas;


use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AuthUser',
    x: ['tags' => ['Auth Models', 'Schemas']],
    description: 'Краткая информация о пользователе в токене',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 22),
        new OA\Property(property: 'username', type: 'string', example: 'Даниил Чеботарев'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'Vop201@yandex.ru'),
    ]
)]
class AuthUserSchema
{

}