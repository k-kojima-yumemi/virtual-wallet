<?php

namespace Tests\Feature;

use Database\Seeders\UsageLogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

const USER_ID_KEY = "app.user_id";

class BalanceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seederで流し込まれているユーザーの残高を取得する。
     * UserIdの指定なし(UserId=100)での実行。
     * @return void
     */
    public function test_user_default_balance(): void
    {
        $this->seed(UsageLogSeeder::class);
        $response = $this->get("/api/balance");
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 1700]);
    }

    /**
     * Seederで流し込まれているユーザーの残高を取得する。
     * UserId=2での実行。
     * 別ユーザーの結果が計算に紛れていないかの確認。
     * @return void
     */
    public function test_user_2_balance(): void
    {
        Config::set(USER_ID_KEY, 2);
        $this->seed(UsageLogSeeder::class);
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
        Config::set(USER_ID_KEY, 3);
        $this->seed(UsageLogSeeder::class);
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
        Config::set(USER_ID_KEY, -1);
        $this->seed(UsageLogSeeder::class);
        $response = $this->get("/api/balance");
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 0]);
    }
}
