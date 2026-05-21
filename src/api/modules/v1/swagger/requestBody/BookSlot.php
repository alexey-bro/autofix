<?php

namespace api\modules\v1\swagger\requestBody;


use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'BookSlot',
    required: true,
    content: new OA\JsonContent(
        required: ['masterId', 'serviceName', 'startTime', 'endTime'],
        properties: [
            new OA\Property(
                property: 'masterId',
                type: 'string',
                example: 51
            ),
            new OA\Property(
                property: 'serviceName',
                type: 'string',
                example: 'Ремонт и покраска капота'
            ),
            new OA\Property(
                property: 'startTime',
                type: 'string',
                format: 'date-time',
                example: '2025-12-31 10:00:00'
            ),
            new OA\Property(
                property: 'endTime',
                type: 'string',
                format: 'date-time',
                example: '2025-12-31 11:00:00'
            )
        ]
    )
)]
class BookSlot
{

}
