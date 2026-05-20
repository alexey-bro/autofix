<?php

namespace api\modules\v1\swagger\requestBody;

use common\models\User;
use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'UploadPhotoPromotionRequest',
    required: true,
    content: new OA\MediaType(
        mediaType: 'multipart/form-data',
        schema: new OA\Schema(
            required: ['photo[]'],
            properties: [
                new OA\Property(
                    property: 'photo[]',
                    type: 'array',
                    items: new OA\Items(
                        type: 'string',
                        format: 'binary',
                    ),
                    minItems: 1,
                    maxItems: 5,
                    description: 'Массив фотографий для загрузки'
                ),
            ]
        )
    )
)]
class UploadPhotoPromotionRequestSchema
{

}