<?php

namespace api\modules\v1\swagger\requestBody;


use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginByPhoneRequest',
    title: 'Login by phone request',
    description: 'Request body for phone login',
    required: ['phone'],
    type: 'object'
)]

class LoginByPhoneRequestDto
{
    #[OA\Property(
        property: 'phone',
        type: 'string',
        example: '+79521008732',
        description: 'Phone number in international format',
        pattern: '^\+[1-9]\d{1,14}$'
    )]
    public string $phone;

}