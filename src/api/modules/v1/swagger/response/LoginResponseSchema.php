<?php

namespace api\modules\v1\swagger\response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginResponse',
    description: 'Ответ на успешную авторизацию',
    properties: [
        new OA\Property(
            property: 'result',
            ref: '#/components/schemas/UserResult'
        ),
        new OA\Property(
            property: 'auth',
            ref: '#/components/schemas/AuthData'
        ),
    ]
)]
class LoginResponseSchema
{

}