<?php

namespace api\modules\v1\swagger\requestBody;


use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'UpdateMasterWorkData',
    required: true,
    content: new OA\JsonContent(
        required: [],
        properties: [
            new OA\Property(
                property: 'customShiftTemplates',
                type: 'array',
                items: new OA\Items(type: 'string'),
                example: [
                    "09:00|21:00|09:00-21:00-1765439174516",
                    "08:00|17:00|08:00-17:00-1765440005699",
                ],
            ),
            new OA\Property(
                property: 'services',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/ServiceRequest'),
                example: [
                    [
                        'id' => '1763968381370',
                        'category' => 'Кузов и покраска',
                        'name' => 'Ремонт бампера',
                        'description' => 'desc',
                        'priceFrom' => 5000,
                        'priceTo' => null,
                        'photos' => [],
                    ],
                    [
                        'id' => '1763968381371',
                        'category' => 'Кузов и покраска',
                        'name' => 'Акумулятор и зарядка',
                        'description' => 'desc',
                        'priceFrom' => 5001,
                        'priceTo' => 1,
                        'photos' => [],
                    ],
                ]
            ),
            new OA\Property(
                property: 'workShifts',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/WorkShiftRequest'),
                example: [
                    [
                        'date' => '2025-11-25',
                        'slots' => [
                            ['startTime' => '2025-11-25T09:00', 'endTime' => '2025-11-25T10:00', 'isAvailable' => true],
                            ['startTime' => '2025-11-25T10:00', 'endTime' => '2025-11-25T11:00', 'isAvailable' => true],
                        ],
                    ],
                    [
                        'date' => '2025-11-26',
                        'slots' => [
                            ['startTime' => '2025-11-26T09:00', 'endTime' => '2025-11-26T10:00', 'isAvailable' => true],
                            ['startTime' => '2025-11-26T10:00', 'endTime' => '2025-11-26T11:00', 'isAvailable' => true],
                        ],
                    ],
                ]
            ),

        ]
    )
)]
class UpdateMasterWorkData
{

}