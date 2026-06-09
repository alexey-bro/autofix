<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
//    request: 'SendCodeRequest',
    request: 'email',
    required: true,
    content: new OA\JsonContent(
        required: ['email'],
        properties: [
            new OA\Property(
                property: 'email',
                type: 'string',
                example: 'dimon@creml.ru'
            ),
        ]
    )
)]
class Email
{

}