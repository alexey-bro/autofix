<?php

namespace api\modules\v1\swagger\response\schemas;


use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BookingClient',
    description: 'Клиент в записи',
    properties: [
        new OA\Property(property: 'userId', type: 'integer', example: 22),
        new OA\Property(property: 'phone', type: 'string', example: '+79521008732'),
        new OA\Property(property: 'photoUrl', type: 'string', example: 'https://findmymechanic.ru/files/1/3dc3b95e3793b8cdb42a7561e7701ef0.png'),
        new OA\Property(property: 'city', type: 'string', nullable: true, example: 'Воронеж'),
        new OA\Property(property: 'firstName', type: 'string', example: 'Даниил'),
        new OA\Property(property: 'carBrand', type: 'string', nullable: true, example: 'Mazda 6 2013'),
    ]
)]
class ShortClientSchema
{

}