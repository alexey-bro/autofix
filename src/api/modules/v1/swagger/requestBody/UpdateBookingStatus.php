<?php

namespace api\modules\v1\swagger\requestBody;



use common\models\Booking;
use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'UpdateBookingStatus',
    required: true,
    content: new OA\JsonContent(
        required: ['bookingId', 'status'],
        properties: [
            new OA\Property(
                property: 'bookingId',
                type: 'integer',
                example: 7
            ),
            new OA\Property(
                property: 'status',
                type: 'integer',
                example: 1
            ),
        ]
    )
)]
class UpdateBookingStatus
{

}