<?php

namespace Tests\Feature;

use App\Http\UserConstant;
use App\Models\UsageLog;
use Database\Seeders\UsageLogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ChargeTest extends TestCase
{
    use RefreshDatabase;

    private function getBalanceForUser(int $userId): int
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return UsageLog::where("user_id", $userId)->sum("changed_amount");
    }

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
        ], ["Accept" => "application/json"]);
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
        ], ["Accept" => "application/json"]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 500]);

        // 2回目。1000円になるはず。
        $response = $this->post("/api/charge", [
            "amount" => 500
        ], ["Accept" => "application/json"]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 1000]);
        // 実際の残高が1000円になっていることを確認。
        $this->assertEquals(1000, $this->getBalanceForUser(100));
    }

    /**
     * UserId=201でチャージを2回行う。
     * Seederには201番のユーザーは存在しないので初期値は0。
     * @return void
     */
    public function test_charge_user_201(): void
    {
        $this->seed(UsageLogSeeder::class);
        // seederでは201番のユーザーは追加していないのでこの番号を使用してテストする
        Config::set(UserConstant::USER_ID_KEY, 201);
        $response = $this->post("/api/charge", [
            "amount" => 500
        ], ["Accept" => "application/json"]);
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
        ], ["Accept" => "application/json"]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 2000]);

        // 別ユーザーのDBに変更がないか確認
        $this->assertEquals(1700, $this->getBalanceForUser(100));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(2200, $this->getBalanceForUser(2));
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
        // User 2は元々2200円残高がある
        $this->seed(UsageLogSeeder::class);
        Config::set(UserConstant::USER_ID_KEY, 2);
        $response = $this->post("/api/charge", [
            "amount" => 1500
        ], ["Accept" => "application/json"]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 3700]);
    }

    /**
     * amountが文字列でも有効な数字であれば受け付けることを確認する。
     * @return void
     */
    public function test_amount_string_number(): void
    {
        $response = $this->post("/api/charge", [
            "amount" => "300"
        ], ["Accept" => "application/json"]);
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 300]);
    }

    /**
     * Bodyがない場合にエラー
     * 残高が変わっていないことを確認。
     * @return void
     */
    public function test_no_body(): void
    {
        // 実行前の残高。0円のはず。
        $preBalance = $this->getBalanceForUser(100);
        $response = $this->post("/api/charge", [], ["Accept" => "application/json"]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals($preBalance, $this->getBalanceForUser(100));
    }

    /**
     * amount=0の場合にエラー
     * @return void
     */
    public function test_amount_0(): void
    {
        $response = $this->post("/api/charge", [
            "amount" => 0
        ], ["Accept" => "application/json"]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
        ], ["Accept" => "application/json"]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * amountが無効な文字列の場合にエラー
     * @return void
     */
    public function test_amount_string(): void
    {
        $response = $this->post("/api/charge", [
            "amount" => "Hey"
        ], ["Accept" => "application/json"]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

}
