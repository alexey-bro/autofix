<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'promotionId',
    required: true,
    content: new OA\JsonContent(
        required: ['promotionId'],
        properties: [
            new OA\Property(
                property: 'promotionId',
                type: 'integer',
                example: '51'
            ),
        ]
    )
)]
class PromotionId
{

}