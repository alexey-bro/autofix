<?php

namespace api\modules\v1\swagger\response\schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Service',
    description: 'Услуга мастера',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'category', type: 'string', example: 'Коробка передач и трансмиссия'),
        new OA\Property(property: 'name', type: 'string', example: 'Диагностика Акпп'),
        new OA\Property(property: 'description', type: 'string', example: ''),
        new OA\Property(property: 'priceFrom', type: 'number', format: 'float', example: 1),
        new OA\Property(property: 'priceTo', type: 'number', format: 'float', example: 1),
        new OA\Property(
            property: 'photos',
            type: 'array',
            items: new OA\Items(type: 'string', format: 'uri'),
            example: [
                'https://findmymechanic.ru/files/1/74e5dbde1dc680ead55aafe92d2f1e88.png',
                'https://findmymechanic.ru/files/1/cd4d25fa179b498d6503741760906f49.png',
            ]
        ),
    ]
)]
class ServiceSchema
{

}