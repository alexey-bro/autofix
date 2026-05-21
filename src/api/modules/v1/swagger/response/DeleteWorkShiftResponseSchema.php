<?php

namespace api\modules\v1\swagger\response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DeleteWorkShiftResponse',
    description: 'Ответ на удаление рабочей смены',
    properties: [
        new OA\Property(
            property: 'result',
            ref: '#/components/schemas/DeleteWorkShift'
        ),
    ]
)]
class DeleteWorkShiftResponseSchema
{

}


/*

{
    "result": {
        "success": true,
        "shiftRemoved": false,
        "updatedWorkShifts": [
            {
                "id": 29,
                "date": "2025-12-02",
                "slots": [
                    {
                        "id": 253,
                        "startTime": "2025-12-02T09:00",
                        "endTime": "2025-12-02T10:00",
                        "isAvailable": true
                    }
                ]
            },
            {
                "id": 39,
                "date": "2025-12-13",
                "slots": [
                    {
                        "id": 343,
                        "startTime": "2025-12-13T09:00",
                        "endTime": "2025-12-13T10:00",
                        "isAvailable": true
                    }
                ]
            }
        ]
    }
}

 */

