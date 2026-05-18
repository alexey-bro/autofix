<?php

namespace api\modules\v1\swagger\response\schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MasterBooking',
    description: 'Запись на обслуживание со стороны мастера (без данных мастера)',
    properties: [
        new OA\Property(property: 'bookingId', type: 'integer', example: 2),
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: ['pending', 'confirmed', 'rejected', 'completed', 'cancelled'],
            example: 'rejected'
        ),
        new OA\Property(property: 'serviceName', type: 'string', example: 'Диагностика двигателя'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2026-05-06T12:18:42.000Z'),
        new OA\Property(property: 'slotStart', type: 'string', format: 'date-time', example: '2025-12-31T17:00:00.000Z'),
        new OA\Property(property: 'slotEnd', type: 'string', format: 'date-time', example: '2025-12-31T18:00:00.000Z'),
        new OA\Property(
            property: 'client',
            ref: '#/components/schemas/MasterBookingClient'
        ),
    ]
)]
class MasterBookingItemSchema
{

}