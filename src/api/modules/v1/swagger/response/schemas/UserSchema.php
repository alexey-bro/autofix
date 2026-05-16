<?php

namespace api\modules\v1\swagger\response\schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    description: 'Профиль пользователя. Поля мастера (services, workShifts, latitude, longitude, workAddress, companyName, specialization) заполнены только для role=MASTER',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'userId', type: 'integer', example: 1),
        new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true, example: 'ekurbatov11@gmail.com'),
        new OA\Property(property: 'phone', type: 'string', example: '+79003051900'),
        new OA\Property(
            property: 'role',
            type: 'string',
            enum: ['CLIENT', 'MASTER', 'ADMIN'],
            example: 'MASTER'
        ),
        new OA\Property(property: 'role_int', type: 'integer', example: 2),
        new OA\Property(property: 'specialization', type: 'string', example: 'Коробка передач и трансмиссия'),
        new OA\Property(
            property: 'photos',
            type: 'array',
            items: new OA\Items(type: 'string', format: 'uri'),
            example: []
        ),
        new OA\Property(property: 'photoUrl', type: 'string', format: 'uri', example: 'https://findmymechanic.ru/files/1/febc633f29dd1dae6b44cf28b8afede6.png'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2026-05-06T12:18:16.000Z'),
        new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2026-05-06T12:18:16.000Z'),

        // Поля мастера — массивы со схемами
        new OA\Property(
            property: 'services',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Service',
//                example: [
//                    'id' => 1,
//                    'category' => 'Коробка передач и трансмиссия',
//                    'name' => 'Диагностика Акпп',
//                    'description' => '',
//                    'priceFrom' => 1000,
//                    'priceTo' => 3000,
//                    'photos' => [
//                        'https://findmymechanic.ru/files/1/74e5dbde1dc680ead55aafe92d2f1e88.png',
//                    ],
//                ],
            ),
//            example: []
        ),
        new OA\Property(
            property: 'workShifts',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/WorkShift'),
//            example: []
        ),
        new OA\Property(
            property: 'customShiftTemplates',
            type: 'array',
//            items: new OA\Items(type: 'object'),
//            example: []
            items: new OA\Items(type: 'string'),
            example: [
                '09:00|17:00|09:00-17:00-1778059115',
                '09:00|14:00|09:00-14:00-1778059115',
            ],
        ),

        new OA\Property(property: 'city', type: 'string', nullable: true, example: 'Воронеж'),
        new OA\Property(property: 'companyName', type: 'string', nullable: true, example: 'AutomaticDrive'),
        new OA\Property(property: 'experience', type: 'integer', example: 6),
        new OA\Property(property: 'firstName', type: 'string', example: 'Automatic'),
        new OA\Property(property: 'lastName', type: 'string', example: 'Drive'),
        new OA\Property(property: 'latitude', type: 'string', nullable: true, example: '51.662611917102'),
        new OA\Property(property: 'longitude', type: 'string', nullable: true, example: '39.132036798672'),
        new OA\Property(property: 'workAddress', type: 'string', nullable: true, example: 'ул. Дорожная, 84, Воронеж'),
        new OA\Property(property: 'rating', type: 'number', format: 'float', example: 0),
        new OA\Property(property: 'reviewsCount', type: 'integer', example: 0),
        new OA\Property(property: 'carBrand', type: 'string', nullable: true, example: null),
    ]
)]
class UserSchema
{

}