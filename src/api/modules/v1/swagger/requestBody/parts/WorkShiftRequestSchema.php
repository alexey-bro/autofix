<?php

namespace api\modules\v1\swagger\requestBody\parts;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'WorkShiftRequest',
    description: 'Рабочая смена с временными слотами',
    properties: [
        new OA\Property(property: 'date', type: 'string', format: 'date', example: '2025-11-25'),
        new OA\Property(
            property: 'slots',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/SlotRequest'),
            example: [
                ['startTime' => '2025-11-25T09:00', 'endTime' => '2025-11-25T10:00', 'isAvailable' => true],
                ['startTime' => '2025-11-25T10:00', 'endTime' => '2025-11-25T11:00', 'isAvailable' => true],
            ]
        ),
    ]
)]
class WorkShiftRequestSchema
{

}