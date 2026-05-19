<?php

namespace api\modules\v1\swagger\requestBody;


use OpenApi\Attributes as OA;

#[OA\RequestBody(
//    request: 'SendCodeRequest',
    request: 'SubmitReview',
    required: true,
    content: new OA\JsonContent(
        required: ['masterId', 'authorName', 'rating', 'text'],
        properties: [
            new OA\Property(
                property: 'masterId',
                type: 'integer',
                example: '51'
            ),
            new OA\Property(
                property: 'authorName',
                type: 'string',
                example: '51'
            ),
            new OA\Property(
                property: 'rating',
                type: 'integer',
                example: '1'
            ),
            new OA\Property(
                property: 'text',
                type: 'string',
                example: 'Test review bro'
            ),
        ]
    )
)]
class Review
{

}
