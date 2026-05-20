<?php

namespace api\modules\v1\swagger\requestBody\parts;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ServiceRequest',
    description: 'Услуга мастера',
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '1763968381370'),
        new OA\Property(property: 'category', type: 'string', example: 'Кузов и покраска'),
        new OA\Property(property: 'name', type: 'string', example: 'Ремонт бампера'),
        new OA\Property(property: 'description', type: 'string', example: 'desc'),
        new OA\Property(property: 'priceFrom', type: 'number', format: 'float', example: 5000),
        new OA\Property(property: 'priceTo', type: 'number', format: 'float', nullable: true, example: null),
        new OA\Property(
            property: 'photos',
            type: 'array',
            items: new OA\Items(type: 'string', format: 'uri'),
            example: []
        ),
    ]
)]
class ServiceRequestSchema
{

}