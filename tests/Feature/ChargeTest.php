<?php

namespace Tests\Feature;

use App\Models\UsageLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

const USER_ID_KEY = "app.user_id";

class ChargeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * UserIdの指定なしでチャージを1回行う。
     * Seederの実行はないので初期値は0。
     * また、レコード内のdescriptionが"チャージ"となっていることを確認する。
     * @return void
     */
    public function test_charge_user_default_once(): void
    {
        $response = $this->post("/api/charge", [
            "amount" => 1
        ]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 1]);

        // DB内でdescriptionがチャージになっているか確認
        /** @noinspection PhpUndefinedMethodInspection */
        $log = UsageLog::firstWhere("user_id", 100);
        $this->assertEquals("チャージ", $log->description);

    }

    /**
     * UserIdの指定なしでチャージを2回行う。
     * Seederの実行はないので初期値は0。
     * @return void
     */
    public function test_charge_user_default_twice(): void
    {
        $response = $this->post("/api/charge", [
            "amount" => 500
        ]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 500]);

        // Second trial, balance must be 1000.
        $response = $this->post("/api/charge", [
            "amount" => 500
        ]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 1000]);
    }

    /**
     * UserId=201でチャージを2回行う。
     * Seederには201番のユーザーは存在しないので初期値は0。
     * @return void
     */
    public function test_charge_user_201(): void
    {
        $this->seed();
        // seederでは201番のユーザーは追加していないのでこの番号を使用してテストする
        Config::set(USER_ID_KEY, 201);
        $response = $this->post("/api/charge", [
            "amount" => 500
        ]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 500]);

        // DB内でdescriptionがチャージになっているか確認
        /** @noinspection PhpUndefinedMethodInspection */
        $log = UsageLog::firstWhere("user_id", 201);
        $this->assertEquals("チャージ", $log->description);

        // 2nd charge
        $response = $this->post("/api/charge", [
            "amount" => 1500
        ]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 2000]);

        // 別ユーザーのDBに変更がないか確認
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(0, UsageLog::where("user_id", 100)->sum("changed_amount"));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(1700, UsageLog::where("user_id", 2)->sum("changed_amount"));
    }

    /**
     * UserId=2でチャージする。
     * Seederでデータが流し込まれているので初期値は1700円。
     * 元々の残高に追加されていることを確認する。
     * @return void
     */
    public function test_charge_user_2(): void
    {
        // DBの内容に追記した際にも正常動作することを確認
        // User 2は元々1700円残高がある
        $this->seed();
        Config::set(USER_ID_KEY, 2);
        $response = $this->post("/api/charge", [
            "amount" => 1500
        ]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 3200]);
    }

    /**
     * amountが文字列でも有効な数字であれば受け付けることを確認する。
     * @return void
     */
    public function test_amount_string_number(): void
    {
        $response = $this->post("/api/charge", [
            "amount" => "300"
        ]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 300]);
    }

    /**
     * Bodyがない場合にエラー
     * @return void
     */
    public function test_no_body(): void
    {
        $response = $this->post("/api/charge");
        $response->assertStatus(400);
    }

    /**
     * amount=0の場合にエラー
     * @return void
     */
    public function test_amount_0(): void
    {
        $response = $this->post("/api/charge", [
            "amount" => 0
        ]);
        $response->assertStatus(400);
    }

    /**
     * amount < 0の場合にエラー
     * 代表値として-1を使用
     * @return void
     */
    public function test_amount_minus(): void
    {
        $response = $this->post("/api/charge", [
            "amount" => -1
        ]);
        $response->assertStatus(400);
    }

    /**
     * amountが無効な文字列の場合にエラー
     * @return void
     */
    public function test_amount_string(): void
    {
        $response = $this->post("/api/charge", [
            "amount" => "Hey"
        ]);
        $response->assertStatus(400);
    }

}
