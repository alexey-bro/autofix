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
    public function tryCreatePromotion(ApiTester $I): void
    {
        // Write your test content here.
        $I->sendPost('functions/payment-promotion', [
            "userId" => 51,
            "title" => "Скидка 99%",
            "description" => "Дорожная 46",
            "validUntil" => "До 2030",
            "phoneNumber" => "+79609998877",
            "conditions" => "Необходимо налить воды в стакан",
            "imageUrl" => "imageUrl",
            "amount" =>  13
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
        ]);

//        // Проверяем, что ключ promotionId существует и его значение - число
//        $I->seeResponseJsonMatchesJsonPath('$.promotionId');
//
//        // Проверяем, что значение - целое число (дополнительная проверка)
//        $response = $I->grabResponse();
//        $data = json_decode($response, true);
//        $I->assertIsInt($data['promotionId']);
    }

    // All `public` methods will be executed as tests.
//    public function tryToTest(ApiTester $I): void
//    {
//        // Write your test content here.
//        $I->sendPost('functions/payment-promotion', [
//            'promotion_id' => 1, // подставь реальный ID
//        ]);
//
//        $I->seeResponseCodeIs(200);
//        $I->seeResponseIsJson();
//        $I->seeResponseContainsJson([
////            'success' => true,
//            'code' => 141,
//        ]);
//    }
}
