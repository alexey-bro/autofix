<?php

namespace api\modules\v1\swagger\requestBody;


use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserId',
    title: 'UserId',
    description: 'Request body for userId',
    required: ['userId'],
    type: 'object'
)]
class UserId
{
    #[OA\Property(
        property: 'userId',
        type: 'integer',
        example: '51',
        description: 'User Id',
        pattern: '^\[1-9]\$'
    )]
    public string $userId;
}
