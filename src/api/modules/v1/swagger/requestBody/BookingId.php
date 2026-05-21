<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'BookingId',
    required: true,
    content: new OA\JsonContent(
        required: ['bookingId'],
        properties: [
            new OA\Property(
                property: 'bookingId',
                type: 'integer',
                example: 32
            ),
        ]
    )
)]
class BookingId
{

}