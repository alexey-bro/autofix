<?php

namespace api\modules\v1\swagger\requestBody;

use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'UpdateMasterBasic',
    required: true,
    content: new OA\JsonContent(
        required: ['promotionId'],
        properties: [
            new OA\Property(
                property: 'city',
                type: 'string',
                description: 'Город',
                example: 'Воронеж'
            ),
            new OA\Property(
                property: 'companyName',
                description: 'Название организации',
                type: 'string',
                example: 'Лукойл'
            ),
            new OA\Property(
                property: 'experience',
                description: 'Стаж работы',
                type: 'integer',
                example: 7
            ),
            new OA\Property(
                property: 'specialization',
                description: 'Список специализаций, которыми занимается организация',
                type: 'string',
                example: 'Кузов и покраска, test, Шиномонтаж и колёса'
            ),

        ]
    )
)]
class UpdateMasterBasic
{

}