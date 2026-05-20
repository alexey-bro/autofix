<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'NewRating',
    required: true,
    content: new OA\JsonContent(
        required: ['newRating'],
        properties: [
            new OA\Property(
                property: 'newRating',
                type: 'integer',
                description: 'Поставить оценку мастеру (Оценка должна быть от 1 до 5)',
                example: 1
            )
        ]
    )
)]
class NewRating
{

}