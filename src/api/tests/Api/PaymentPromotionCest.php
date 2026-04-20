<?php

//declare(strict_types=1);

namespace Tests\Api;

use Tests\Support\ApiTester;

final class PaymentPromotionCest
{

    public function _before(ApiTester $I): void
    {
        // Заголовки для всех запросов в этом классе
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        // Code here will be executed before each test function.
    }

    // All `public` methods will be executed as tests.
    public function tryToTest(ApiTester $I): void
    {
        // Write your test content here.
        $I->sendPost('functions/payment-promotion', [
            'promotion_id' => 1, // подставь реальный ID
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
//            'success' => true,
            'code' => 141,
        ]);
    }
}
