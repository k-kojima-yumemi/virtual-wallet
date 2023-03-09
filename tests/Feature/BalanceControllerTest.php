<?php

namespace Tests\Feature;

use Database\Seeders\UsageLogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BalanceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * DBの名前でテスト時かどうか判断しているのでその前提についてテストする。
     * @return void
     */
    public function test_assumption_db_name(): void
    {
        $this->assertEquals("testing", DB::getDatabaseName());
    }

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
        $this->seed(UsageLogSeeder::class);
        $response = $this->withHeaders(["user_id" => 2])->get("/api/balance");
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
        $this->seed(UsageLogSeeder::class);
        $response = $this->withHeaders(["user_id" => 3])->get("/api/balance");
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
        $this->seed(UsageLogSeeder::class);
        $response = $this->withHeaders(["user_id" => -1])->get("/api/balance");
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 0]);
    }
}
