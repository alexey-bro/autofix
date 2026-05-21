<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'shiftDate',
    required: true,
    content: new OA\JsonContent(
        required: ['shiftDate'],
        properties: [
            new OA\Property(
                property: 'shiftDate',
                type: 'string',
                example: '2025-12-01'
            ),
        ]
    )
)]
class ShiftDate
{

}