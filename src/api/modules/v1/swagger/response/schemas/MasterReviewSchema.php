<?php

namespace api\modules\v1\swagger\response\schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MasterReview',
    description: 'Отзыв о мастере',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'authorName', type: 'string', example: 'Даниил Ч.'),
        new OA\Property(property: 'authorPhotoUrl', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 5),
        new OA\Property(property: 'text', type: 'string', example: 'Все понравилось, быстро, качественно'),
        new OA\Property(property: 'title', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'date', type: 'string', example: '29 апреля 2026 г.'),
    ]
)]
class MasterReviewSchema
{

}