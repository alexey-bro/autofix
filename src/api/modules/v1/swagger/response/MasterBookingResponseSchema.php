<?php

namespace api\modules\v1\swagger\response;


use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MasterBookingsListResponse',
    description: 'Список записей со стороны мастера',
    properties: [
        new OA\Property(
            property: 'result',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/MasterBooking'),
            example: [
                [
                    'bookingId' => 2,
                    'status' => 'rejected',
                    'serviceName' => 'Диагностика двигателя',
                    'createdAt' => '2026-05-06T12:18:42.000Z',
                    'slotStart' => '2025-12-31T17:00:00.000Z',
                    'slotEnd' => '2025-12-31T18:00:00.000Z',
                    'client' => [
                        'userId' => 22,
                        'phone' => '+79521008732',
                        'photoUrl' => 'https://findmymechanic.ru/files/1/3dc3b95e3793b8cdb42a7561e7701ef0.png',
                        'city' => 'Воронеж',
                        'firstName' => 'Даниил',
                        'carBrand' => 'Mazda 6 2013',
                    ],
                ],
            ]
        ),
    ]
)]
class MasterBookingResponseSchema
{

}