<?php

namespace api\modules\v1\swagger\response\schemas;


use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DeleteWorkShift',
    description: 'Акция мастера',
    properties: [
        new OA\Property(property: 'success', type: 'bool', example: true),
        new OA\Property(property: 'shiftRemoved', type: 'bool', example: true),
        new OA\Property(
            property: 'updatedWorkShifts',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/WorkShift'),
        ),
    ]
)]
class DeleteWorkShiftSchema
{

}