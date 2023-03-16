<?php

namespace Tests\Feature;

use App\Http\UserConstant;
use Database\Seeders\UsageLogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class BalanceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsageLogSeeder::class);
    }

    /**
     * Seederで流し込まれているユーザーの残高を取得する。
     * UserIdの指定なし(UserId=100)での実行。
     * @return void
     */
    public function test_user_default_balance(): void
    {
        $response = $this->get("/api/balance");
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 1700]);
    }

    /**
     * レスポンスのbalanceがintであることを確認
     */
    public function test_response_has_int(): void
    {
        $response = $this->get("/api/balance");
        $response->assertStatus(200);
        $balance = $response->json("balance");
        $this->assertIsInt($balance, "Response is expected to be an Int");
    }

    /**
     * Seederで流し込まれているユーザーの残高を取得する。
     * UserId=2での実行。
     * 別ユーザーの結果が計算に紛れていないかの確認。
     * @return void
     */
    public function test_user_2_balance(): void
    {
        Config::set(UserConstant::USER_ID_KEY, 2);
        $response = $this->get("/api/balance");
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 2200]);
    }

    /**
     * Seederで流し込まれているユーザーの残高を取得する。
     * UserId=3での実行。
     * 最終的な残高がマイナスになるケースの確認。
     * @return void
     */
    public function test_user_3_balance(): void
    {
        Config::set(UserConstant::USER_ID_KEY, 3);
        $response = $this->get("/api/balance");
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => -500]);
    }

    /**
     * 存在しないユーザーの残高を取得する。
     * 存在していない場合は残高0になっていることを確認する。
     * @return void
     */
    public function test_user_no_existence_balance(): void
    {
        Config::set(UserConstant::USER_ID_KEY, -1);
        $response = $this->get("/api/balance");
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 0]);
    }
}
