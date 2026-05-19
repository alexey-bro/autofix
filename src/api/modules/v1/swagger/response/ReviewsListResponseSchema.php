<?php

namespace api\modules\v1\swagger\response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ReviewsListResponse',
    description: 'Список отзывов',
    properties: [
        new OA\Property(
            property: 'result',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/MasterReview'),
            example: [
                [
                    'id' => 1,
                    'authorName' => 'Даниил Ч.',
                    'authorPhotoUrl' => null,
                    'rating' => 5,
                    'text' => 'Все понравилось, быстро, качественно',
                    'title' => null,
                    'date' => '29 апреля 2026 г.',
                ],
                [
                    'id' => 6,
                    'authorName' => 'Alexey Bro',
                    'authorPhotoUrl' => null,
                    'rating' => 1,
                    'text' => 'Test review bro',
                    'title' => null,
                    'date' => '29 апреля 2026 г.',
                ],
            ]
        ),
    ]
)]
class ReviewsListResponseSchema
{

}