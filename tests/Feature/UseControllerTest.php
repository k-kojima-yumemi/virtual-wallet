<?php

namespace Tests\Feature;

use App\ConstMessages;
use App\Models\UsageLog;
use Database\Seeders\UsageLogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UseControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * "test_post"という目的で700円使用する。
     * UserIdの指定なし(UserId=100)での実行。
     *
     * レスポンスコードの確認、レスポンスに残高が含まれていることの確認、DBのレコードの目的と金額があっているかの確認。
     * @return void
     */
    public function test_default_user_use_700(): void
    {
        // もともとの残高は1700円
        $this->seed(UsageLogSeeder::class);

        $response = $this->postJson("/api/use", array(
            "amount" => 700,
            "description" => "test_post",
        ));
        // レスポンスに残高が正しく含まれていることを確認。
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(array("balance" => 1000));

        /** @noinspection PhpUndefinedMethodInspection */
        $lastRecord = UsageLog::orderBy('id', 'DESC')->first();

        // Assumption to get the record inserted by above code.
        $this->assertEquals("test_post", $lastRecord->description, "test assumption");
        $this->assertEquals(-700, $lastRecord->changed_amount);
        $this->assertEquals(1000, UsageLog::getUserBalance(100));
    }

    /**
     * "test_post"という目的で1699円使用する。
     * UserIdの指定なし(UserId=100)での実行。
     * チャージのメッセージが含まれないことを確認。
     */
    public function test_default_user_use_1699(): void
    {
        // もともとの残高は1700円
        $this->seed(UsageLogSeeder::class);

        $response = $this->postJson("/api/use", array(
            "amount" => 1699,
            "description" => "test_post",
        ));
        // レスポンスに残高が正しく含まれていることを確認。またチャージのメッセージが入っていることを確認。
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(array(
                "balance" => 1,
            ))
            ->assertJsonMissingExact(array(
                "message" => ConstMessages::CHARGE_SUGGESTION_MESSAGE,
            ));
        $this->assertEquals(1, UsageLog::getUserBalance(100));
    }

    /**
     * "test_post"という目的で1700円使用する。
     * UserIdの指定なし(UserId=100)での実行。
     * 残高が0円になるのでチャージのメッセージが含まれるか確認。
     */
    public function test_default_user_use_1700(): void
    {
        // もともとの残高は1700円
        $this->seed(UsageLogSeeder::class);

        $response = $this->postJson("/api/use", array(
            "amount" => 1700,
            "description" => "test_post",
        ));
        // レスポンスに残高が正しく含まれていることを確認。またチャージのメッセージが入っていることを確認。
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(array(
                "balance" => 0,
                "message" => ConstMessages::CHARGE_SUGGESTION_MESSAGE,
            ));
        $this->assertEquals(0, UsageLog::getUserBalance(100));
    }

    /**
     * "test_post"という目的で1701円使用する。
     * UserIdの指定なし(UserId=100)での実行。
     * 残高が0円になるのでチャージのメッセージが含まれるか確認。
     */
    public function test_default_user_use_1701(): void
    {
        // もともとの残高は1700円
        $this->seed(UsageLogSeeder::class);

        $response = $this->postJson("/api/use", array(
            "amount" => 1701,
            "description" => "test_post",
        ));
        // レスポンスに残高が正しく含まれていることを確認。またチャージのメッセージが入っていることを確認。
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(array(
                "balance" => -1,
                "message" => ConstMessages::CHARGE_SUGGESTION_MESSAGE,
            ));
        $this->assertEquals(-1, UsageLog::getUserBalance(100));
    }

    /**
     * "test_post"という目的で700円使用する。
     * UserId=3で実行。
     * 元々の残高がマイナスなので400となる。
     * @return void
     */
    public function test_user_3_use(): void
    {
        Config::set("app.user_id", 3);
        $this->seed(UsageLogSeeder::class);
        $balance = UsageLog::getUserBalance(3);
        $this->assertLessThan(0, $balance, "test assumption");
        $response = $this->postJson("/api/use", array(
            "amount" => 700,
            "description" => "test_post",
        ));
        $response
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(array("message" => ConstMessages::BALANCE_MINUS_MESSAGE));
        // 残高が変わっていないことを確認。
        $this->assertEquals($balance, UsageLog::getUserBalance(3));
    }

    /**
     * 残高0円のUserId=201が使用する。
     * もともと0円なので400
     * @return void
     */
    public function test_user_201_use(): void
    {
        Config::set("app.user_id", 201);
        $response = $this->postJson("/api/use", array(
            "amount" => 700,
            "description" => "test_user_201_use",
        ));
        $response
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(array("message" => ConstMessages::BALANCE_MINUS_MESSAGE));
        $this->assertEquals(0, UsageLog::getUserBalance(201));
    }

    /**
     * dataなし
     */
    public function test_no_data(): void
    {
        $response = $this->postJson("/api/use");
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * descriptionなし
     */
    public function test_no_description(): void
    {
        $response = $this->postJson("/api/use", array(
            "amount" => 1000,
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * empty description
     */
    public function test_empty_description(): void
    {
        $response = $this->postJson("/api/use", array(
            "amount" => 1000,
            "description" => "",
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * descriptionが文字列でなく数字の時ははじく
     */
    public function test_number_description(): void
    {
        $response = $this->postJson("/api/use", array(
            "amount" => 1000,
            "description" => 100,
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * descriptionが数字の文字列ならOK
     */
    public function test_number_string_description(): void
    {
        $this->seed(UsageLogSeeder::class);
        $response = $this->postJson("/api/use", array(
            "amount" => 1000,
            "description" => "100",
        ));
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * descriptionが文字列でなくboolの時ははじく
     */
    public function test_bool_description(): void
    {
        $response = $this->postJson("/api/use", array(
            "amount" => 1000,
            "description" => true,
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * amountなし
     */
    public function test_no_amount(): void
    {
        $response = $this->postJson("/api/use", array(
            "description" => "test_no_amount",
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * amount == 0
     */
    public function test_amount_is_0(): void
    {
        $response = $this->postJson("/api/use", array(
            "description" => "test_amount_is_0",
            "amount" => 0,
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * amount < 0
     */
    public function test_amount_is_minus(): void
    {
        $response = $this->postJson("/api/use", array(
            "description" => "test_amount_is_minus",
            "amount" => -1,
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
