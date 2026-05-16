<?php

namespace api\modules\v1\swagger\response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UsersListResponse',
    description: 'Список пользователей',
    properties: [
        new OA\Property(
            property: 'result',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/User'),
        ),
    ]
)]
class UsersListResponseSchema
{

}