<?php

namespace api\modules\v1\swagger\requestBody;

//new OA\RequestBody(
//    required: true,
//    content: new OA\JsonContent(
//        required: ['phoneNumber'],
//        properties: [
//            new OA\Property(
//                property: 'phoneNumber',
//                type: 'string',
//                example: '+79507606922'
//            ),
//        ]
//    )
//),


use OpenApi\Attributes as OA;

#[OA\RequestBody(
//    request: 'SendCodeRequest',
    request: 'phoneNumber',
    required: true,
    content: new OA\JsonContent(
        required: ['phoneNumber'],
        properties: [
            new OA\Property(
                property: 'phoneNumber',
                type: 'string',
                example: '+79507606922'
            ),
        ]
    )
)]
class PhoneNumber
{

}