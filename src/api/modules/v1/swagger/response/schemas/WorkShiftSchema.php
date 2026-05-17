<?php

namespace api\modules\v1\swagger\response\schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'WorkShift',
    description: 'Рабочая смена мастера со слотами',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'date', type: 'string', format: 'date', example: '2025-12-09'),
        new OA\Property(
            property: 'slots',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Slot'),
            example: [
                ['id' => 1, 'startTime' => '2025-12-09T09:00', 'endTime' => '2025-12-09T10:00', 'isAvailable' => true],
                ['id' => 2, 'startTime' => '2025-12-09T10:00', 'endTime' => '2025-12-09T11:00', 'isAvailable' => true],
            ]
        ),
    ]
)]
class WorkShiftSchema
{

}