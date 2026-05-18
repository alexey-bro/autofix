<?php

namespace api\modules\v1\swagger\response\schemas;


use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Promotion',
    description: 'Акция мастера',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 4),
        new OA\Property(property: 'objectId', type: 'integer', example: 4),
        new OA\Property(property: 'title', type: 'string', example: 'Скидка 101%'),
        new OA\Property(property: 'description', type: 'string', example: 'Дорожная 46'),
        new OA\Property(property: 'validUntil', type: 'string', example: 'До 2031'),
        new OA\Property(property: 'publishedUntil', type: 'string', format: 'date-time', example: '2027-01-28T10:25:37.000Z'),
        new OA\Property(property: 'phoneNumber', type: 'string', example: '+79609998879'),
        new OA\Property(property: 'conditions', type: 'string', example: 'Необходимо налить воды в стакан'),
        new OA\Property(
            property: 'imageUrl',
            type: 'array',
            items: new OA\Items(type: 'string', format: 'uri'),
            example: [
                'http://autofix.loc/files/1/dc6bbc55557da3e279e7dc31cf9467fc.png',
                'http://autofix.loc/files/1/85c1bd281db3eb027f55e145fe3b0a4f.png',
            ]
        ),
        new OA\Property(property: 'createdByMasterId', type: 'integer', example: 4),
        new OA\Property(property: 'master_id', type: 'integer', example: 4),
        new OA\Property(property: 'order', type: 'integer', nullable: true, example: null),
    ]
)]
class PromotionSchema
{

}