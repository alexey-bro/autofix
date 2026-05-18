<?php

namespace api\modules\v1\swagger\response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PromotionsListResponse',
    description: 'Список акций',
    properties: [
        new OA\Property(
            property: 'result',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Promotion'),
            example: [
                [
                    'id' => 4,
                    'objectId' => 4,
                    'title' => 'Скидка 101%',
                    'description' => 'Дорожная 46',
                    'validUntil' => 'До 2031',
                    'publishedUntil' => '2027-01-28T10:25:37.000Z',
                    'phoneNumber' => '+79609998879',
                    'conditions' => 'Необходимо налить воды в стакан',
                    'imageUrl' => [
                        'http://autofix.loc/files/1/dc6bbc55557da3e279e7dc31cf9467fc.png',
                        'http://autofix.loc/files/1/85c1bd281db3eb027f55e145fe3b0a4f.png',
                    ],
                    'createdByMasterId' => 4,
                    'master_id' => 4,
                    'order' => null,
                ],
            ]
        ),
    ]
)]
class PromotionsListResponseSchema
{

}