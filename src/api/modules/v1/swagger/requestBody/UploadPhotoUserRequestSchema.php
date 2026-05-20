<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'UploadPhotoUserRequest',
    required: true,
    content: new OA\MediaType(
        mediaType: 'multipart/form-data',
        schema: new OA\Schema(
            required: ['photo'],
            properties: [
                new OA\Property(
                    property: 'photo',
                    type: 'string',
                    format: 'binary',
                    description: 'Фото для загрузки'
                ),
            ]
        )
    )
)]
class UploadPhotoUserRequestSchema
{

}