<?php

namespace api\modules\v1\swagger\response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BookingsListResponse',
    description: 'Список записей',
    properties: [
        new OA\Property(
            property: 'result',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/BookingClientItem'),
            example: [
                [
                    'bookingId' => 1,
                    'status' => 'rejected',
                    'serviceName' => 'Диагностика и ремонт кузова',
                    'createdAt' => '2026-05-06T12:18:42.000Z',
                    'slotStart' => '2025-12-10T13:00:00.000Z',
                    'slotEnd' => '2025-12-10T14:00:00.000Z',
                    'master' => [
                        'userId' => 11,
                        'phone' => '+79300112777',
                        'specialization' => 'Кузов и покраска',
                        'photoUrl' => '',
                        'city' => 'Воронеж',
                        'firstName' => 'Дмитрий',
                        'latitude' => '51.64227614688',
                        'longitude' => '39.139450261372',
                        'workAddress' => 'пр-т. Ленинский, 11а, Воронеж',
                        'rating' => 5,
                        'reviewsCount' => 0,
                        'carBrand' => null,
                    ],
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
class ClientBookingsResponseSchema
{

}


/*

{
    "result": [
        {
            "bookingId": 1,
            "status": "rejected",
            "serviceName": "Ремонт и покраска двери",
            "createdAt": "2026-05-06T12:18:42.000Z",
            "slotStart": "2025-12-10T13:00:00.000Z",
            "slotEnd": "2025-12-10T14:00:00.000Z",
            "master": {
                "userId": 11,
                "phone": "+79300112777",
                "specialization": "Кузов и покраска",
                "photoUrl": "",
                "city": "Воронеж",
                "firstName": "Стапель",
                "latitude": "51.64227614688",
                "longitude": "39.139450261372",
                "workAddress": "пр-т. Патриотов, 11а, Воронеж, Воронежская обл., Россия, 394065",
                "rating": 5,
                "reviewsCount": 0,
                "carBrand": null
            },
            "client": {
                "userId": 22,
                "phone": "+79521008732",
                "photoUrl": "https://findmymechanic.ru/files/1/3dc3b95e3793b8cdb42a7561e7701ef0.png",
                "city": "Воронеж",
                "firstName": "Даниилыч",
                "carBrand": "Mazda 6 2013"
            }
        }
    ]
}

 */