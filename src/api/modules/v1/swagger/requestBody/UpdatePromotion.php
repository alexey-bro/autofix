<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
//    request: 'SendCodeRequest',
    request: 'UpdatePromotion',
    required: true,
    content: new OA\JsonContent(
        required: ['promotionId'],
        properties: [
            new OA\Property(
                property: 'promotionId',
                type: 'integer',
                example: '66'
            ),
            new OA\Property(
                property: 'title',
                type: 'string',
                example: 'Скидка 99%'
            ),
            new OA\Property(
                property: 'description',
                type: 'string',
                example: 'Дорожная 46'
            ),
            new OA\Property(
                property: 'validUntil',
                type: 'string',
                example: 'До 2030'
            ),
            new OA\Property(
                property: 'phoneNumber',
                type: 'string',
                example: '+79609998877'
            ),
            new OA\Property(
                property: 'conditions',
                type: 'string',
                example: 'Необходимо налить воды в стакан'
            ),
        ]
    )
)]
class UpdatePromotion
{

}