<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
//    request: 'SendCodeRequest',
    request: 'masterId',
    required: true,
    content: new OA\JsonContent(
        required: ['masterId'],
        properties: [
            new OA\Property(
                property: 'masterId',
                type: 'integer',
                example: '51'
            ),
        ]
    )
)]
class MasterId
{

}