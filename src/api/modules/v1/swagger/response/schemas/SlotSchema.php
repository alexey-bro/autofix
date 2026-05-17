<?php

namespace api\modules\v1\swagger\response\schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Slot',
    description: 'Временной слот рабочей смены',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'startTime', type: 'string', example: '2025-12-09T09:00'),
        new OA\Property(property: 'endTime', type: 'string', example: '2025-12-09T10:00'),
        new OA\Property(property: 'isAvailable', type: 'boolean', example: true),
    ]
)]
class SlotSchema
{

}