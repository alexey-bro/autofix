<?php

namespace api\modules\v1\swagger\response\schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BookingMaster',
    description: 'Мастер в записи',
    properties: [
        new OA\Property(property: 'userId', type: 'integer', example: 11),
        new OA\Property(property: 'phone', type: 'string', example: '+79300112777'),
        new OA\Property(property: 'specialization', type: 'string', example: 'Кузов и покраска'),
        new OA\Property(property: 'photoUrl', type: 'string', example: ''),
        new OA\Property(property: 'city', type: 'string', example: 'Воронеж'),
        new OA\Property(property: 'firstName', type: 'string', example: 'Дмитрий'),
        new OA\Property(property: 'latitude', type: 'string', nullable: true, example: '51.64227614688'),
        new OA\Property(property: 'longitude', type: 'string', nullable: true, example: '39.139450261372'),
        new OA\Property(property: 'workAddress', type: 'string', nullable: true, example: 'пр-т. Ленинский, 11а, Воронеж, Воронежская обл., Россия, 394065'),
        new OA\Property(property: 'rating', type: 'number', format: 'float', example: 5),
        new OA\Property(property: 'reviewsCount', type: 'integer', example: 0),
        new OA\Property(property: 'carBrand', type: 'string', nullable: true, example: null),
    ]
)]
class ShortMasterSchema
{

}


/*

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


 */