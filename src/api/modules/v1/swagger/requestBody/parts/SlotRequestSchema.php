<?php

namespace api\modules\v1\swagger\requestBody\parts;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SlotRequest',
    description: 'Временной слот рабочей смены',
    properties: [
        new OA\Property(property: 'startTime', type: 'string', example: '2025-11-25T09:00'),
        new OA\Property(property: 'endTime', type: 'string', example: '2025-11-25T10:00'),
        new OA\Property(property: 'isAvailable', type: 'boolean', example: true),
    ]
)]
class SlotRequestSchema
{

}