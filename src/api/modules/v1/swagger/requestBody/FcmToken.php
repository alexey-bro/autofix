<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
//    request: 'SendCodeRequest',
    request: 'fcmToken',
    required: true,
    content: new OA\JsonContent(
        required: ['email'],
        properties: [
            new OA\Property(
                property: 'fcm_token',
                type: 'string',
                example: '6a4#@5gdsg&^%sgsrth9z8sdgz6&^g67gjkl'
            ),
        ]
    )
)]
class FcmToken
{

}