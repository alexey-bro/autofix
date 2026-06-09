<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'VerifyCode',
    required: true,
    content: new OA\JsonContent(
        required: ['code', 'type_user', 'email'],
        properties: [
            new OA\Property(
                property: 'code',
                type: 'integer',
                description: 'Код верификации',
                example: 6666
            ),
            new OA\Property(
                property: 'type_user',
                type: 'integer',
                description: 'Тип пользователя 1 - мастер, 2 - клиент',
                example: 1
            ),
            new OA\Property(
                property: 'email',
                description: 'Email пользователя',
                type: 'string',
                example: "dimon@creml.ru"
            ),
        ]
    )
)]
class VerifyCode
{

}