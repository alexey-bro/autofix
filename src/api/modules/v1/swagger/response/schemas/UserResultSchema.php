<?php

namespace api\modules\v1\swagger\response\schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserResult',
    description: 'Полный профиль пользователя',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 22),
        new OA\Property(property: 'userId', type: 'integer', example: 22),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'Vop201@yandex.ru'),
        new OA\Property(property: 'phone', type: 'string', example: '+79521008732'),
        new OA\Property(property: 'role', type: 'string', enum: ['CLIENT', 'MECHANIC', 'ADMIN'], example: 'CLIENT'),
        new OA\Property(property: 'role_int', type: 'integer', example: 1),
        new OA\Property(property: 'specialization', type: 'string', example: ''),
        new OA\Property(
            property: 'photos',
            type: 'array',
            items: new OA\Items(type: 'string'),
            example: []
        ),
        new OA\Property(property: 'photoUrl', type: 'string', format: 'uri', example: 'https://findmymechanic.ru/files/1/photo.png'),
        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time', example: '2026-05-06T12:18:27.000Z'),
        new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time', example: '2026-05-06T12:18:27.000Z'),
        new OA\Property(
            property: 'services',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Service',),
        ),
        new OA\Property(
            property: 'workShifts',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/WorkShift'),
        ),
        new OA\Property(
            property: 'customShiftTemplates',
            type: 'array',
            items: new OA\Items(type: 'string'),
            example: [
                '09:00|17:00|09:00-17:00-1778059115',
                '09:00|14:00|09:00-14:00-1778059115',
            ],
        ),
        new OA\Property(property: 'city', type: 'string', example: 'Воронеж'),
        new OA\Property(property: 'companyName', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'experience', type: 'integer', example: 0),
        new OA\Property(property: 'firstName', type: 'string', example: 'Даниил'),
        new OA\Property(property: 'lastName', type: 'string', example: 'Чеботарев'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true, example: null),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true, example: null),
        new OA\Property(property: 'workAddress', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'rating', type: 'number', format: 'float', example: 0),
        new OA\Property(property: 'reviewsCount', type: 'integer', example: 0),
        new OA\Property(property: 'carBrand', type: 'string', example: 'Mazda 6 2013'),
    ]
)]
class UserResultSchema
{

}