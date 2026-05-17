<?php

namespace api\modules\v1\swagger\response\schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UnauthorizedData',
    description: 'Ошибка авторизации',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Unauthorized'),
        new OA\Property(property: 'message', type: 'string', example: 'Ошибка авторизации'),
        new OA\Property(property: 'code', type: 'integer', example: '0'),
        new OA\Property(property: 'status', type: 'string', example: '401'),
        new OA\Property(property: 'type', type: 'string', example: 'yii\\web\\UnauthorizedHttpException'),
    ]
)]
class UnauthorizedSchema
{

//"name": "Unauthorized",
//"message": "Ошибка авторизации",
//"code": 0,
//"status": 401,
//"type": "yii\\web\\UnauthorizedHttpException"

}