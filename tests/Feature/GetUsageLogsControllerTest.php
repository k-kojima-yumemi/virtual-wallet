<?php

namespace Tests\Feature;

use App\Http\UserConstant;
use Database\Seeders\UsageLogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetUsageLogsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsageLogSeeder::class);
    }

    /**
     * デフォルト(UserId=100)の全履歴の取得。
     * Seederの結果とあっているか、欲しいキーは存在しているか確認する。
     * キーはused_at, changed_amount, description
     * @return void
     */
    public function test_default_user_get(): void
    {
        $expected = json_decode('
        {
          "logs": [
            {
              "used_at": "2023-02-01 00:00:00",
              "changed_amount": 5000,
              "description": "チャージ"
            },
            {
              "used_at": "2023-02-01 07:00:00",
              "changed_amount": -100,
              "description": "アイス"
            },
            {
              "used_at": "2023-02-01 12:00:00",
              "changed_amount": -800,
              "description": "ラーメン"
            },
            {
              "used_at": "2023-02-01 18:00:00",
              "changed_amount": -600,
              "description": "たこ焼き"
            },
            {
              "used_at": "2023-02-02 07:30:00",
              "changed_amount": -200,
              "description": "アイス"
            },
            {
              "used_at": "2023-02-02 12:30:00",
              "changed_amount": -600,
              "description": "たこ焼き"
            },
            {
              "used_at": "2023-02-02 17:30:00",
              "changed_amount": -1000,
              "description": "ラーメン"
            }
          ]
        }', true);
        $this->assertNotNull($expected, "Assumption failed");
        $request = $this->get("/api/usage_logs");
        $request
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($expected);

        $this->assertSameSize($expected["logs"], $request->json("logs"));
    }

    /**
     * UserId=201の履歴取得。このユーザーは空の履歴となる。
     * @return void
     */
    public function test_user_201_get(): void
    {
        Config::set(UserConstant::USER_ID_KEY, 201);
        $expected = json_decode('{"logs": []}', true);
        $this->assertNotNull($expected, "Assumption failed");
        $request = $this->get("/api/usage_logs");
        $request
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($expected);
        $this->assertSameSize($expected["logs"], $request->json("logs"));
    }

    /**
     * UserId=2の履歴取得。
     * Seederで入れたデータが全て入っているか確認。
     * @return void
     */
    public function test_user_2_get(): void
    {
        Config::set(UserConstant::USER_ID_KEY, 2);
        $expected = json_decode('
        {
          "logs": [
            {
              "used_at": "2023-02-04 00:00:00",
              "changed_amount": 2000,
              "description": "チャージ"
            },
            {
              "used_at": "2023-02-05 07:00:00",
              "changed_amount": -100,
              "description": "チョコレート"
            },
            {
              "used_at": "2023-02-05 12:00:00",
              "changed_amount": -800,
              "description": "ラーメン"
            },
            {
              "used_at": "2023-02-05 12:00:00",
              "changed_amount": 3000,
              "description": "チャージ"
            },
            {
              "used_at": "2023-02-05 18:00:00",
              "changed_amount": -600,
              "description": "たこ焼き"
            },
            {
              "used_at": "2023-02-08 07:30:00",
              "changed_amount": -200,
              "description": "チョコレート"
            },
            {
              "used_at": "2023-02-09 12:30:00",
              "changed_amount": -600,
              "description": "たこ焼き"
            },
            {
              "used_at": "2023-02-10 17:30:00",
              "changed_amount": -500,
              "description": "たこ焼き"
            }
          ]
        }
        ', true);
        $this->assertNotNull($expected, "Assumption failed");
        $request = $this->get("/api/usage_logs");
        $request
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($expected);
        $this->assertSameSize($expected["logs"], $request->json("logs"));
    }

    /**
     * 無効な期間設定をした場合は空の値が返却される。
     */
    public function test_invalid_clip(): void
    {
        $expected = json_decode('{"logs": []}', true);
        $this->assertNotNull($expected, "Assumption failed");
        $request = $this->get("/api/usage_logs?from=2023-02-01T00:00:00&to=2023-01-01T00:00:00");
        $request
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($expected);
        $this->assertSameSize($expected["logs"], $request->json("logs"));
    }

    /**
     * From: 2/1, To: 2/2の取得(UserId=100)
     */
    public function test_get_from0221_to0222(): void
    {
        $expected = json_decode('
        {
          "logs": [
            {
              "used_at": "2023-02-01 00:00:00",
              "changed_amount": 5000,
              "description": "チャージ"
            },
            {
              "used_at": "2023-02-01 07:00:00",
              "changed_amount": -100,
              "description": "アイス"
            },
            {
              "used_at": "2023-02-01 12:00:00",
              "changed_amount": -800,
              "description": "ラーメン"
            },
            {
              "used_at": "2023-02-01 18:00:00",
              "changed_amount": -600,
              "description": "たこ焼き"
            }
          ]
        }', true);
        $this->assertNotNull($expected, "Assumption failed");
        $request = $this->get("/api/usage_logs?from=2023-02-01T00:00:00&to=2023-02-02T00:00:00");
        $request
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($expected);
        $this->assertSameSize($expected["logs"], $request->json("logs"));
    }

    /**
     * FromとToがデータの範囲内の場合にフィルタしているかの確認
     * @return void
     */
    public function test_get_from0221t1200_to0222t1200(): void
    {
        $expected = json_decode('
        {
          "logs": [
            {
              "used_at": "2023-02-01 12:00:00",
              "changed_amount": -800,
              "description": "ラーメン"
            },
            {
              "used_at": "2023-02-01 18:00:00",
              "changed_amount": -600,
              "description": "たこ焼き"
            },
            {
              "used_at": "2023-02-02 07:30:00",
              "changed_amount": -200,
              "description": "アイス"
            }
          ]
        }', true);
        $this->assertNotNull($expected, "Assumption failed");
        $request = $this->get("/api/usage_logs?from=2023-02-01T12:00:00&to=2023-02-02T12:00:00");
        $request
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($expected);
        $this->assertSameSize($expected["logs"], $request->json("logs"));
    }

    /**
     * From: 2/1 00:00, To: 2/1 12:00の取得(UserId=100)
     * 12:00のデータが入っていないことを確認。
     * FromはInclusiveなので00:00のデータは入っている。
     */
    public function test_get_from0221t0000_to0221t1200(): void
    {
        $expected = json_decode('
        {
          "logs": [
            {
              "used_at": "2023-02-01 00:00:00",
              "changed_amount": 5000,
              "description": "チャージ"
            },
            {
              "used_at": "2023-02-01 07:00:00",
              "changed_amount": -100,
              "description": "アイス"
            }
          ]
        }', true);
        $this->assertNotNull($expected, "Assumption failed");
        $request = $this->get("/api/usage_logs?from=2023-02-01T00:00:00&to=2023-02-01T12:00:00");
        $request
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($expected);
        $this->assertSameSize($expected["logs"], $request->json("logs"));
    }

    /**
     * To: 2/1 12:00の取得(UserId=100)
     * Toのみの指定
     */
    public function test_get_to0221t1200(): void
    {
        $expected = json_decode('
        {
          "logs": [
            {
              "used_at": "2023-02-01 00:00:00",
              "changed_amount": 5000,
              "description": "チャージ"
            },
            {
              "used_at": "2023-02-01 07:00:00",
              "changed_amount": -100,
              "description": "アイス"
            }
          ]
        }', true);
        $this->assertNotNull($expected, "Assumption failed");
        $request = $this->get("/api/usage_logs?to=2023-02-01T12:00:00");
        $request
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($expected);
        $this->assertSameSize($expected["logs"], $request->json("logs"));
    }

    /**
     * From: 2/2 7:30の取得(UserId=100)
     * Fromのみの指定
     */
    public function test_get_from0221t1200(): void
    {
        $expected = json_decode('
        {
          "logs": [
            {
              "used_at": "2023-02-02 07:30:00",
              "changed_amount": -200,
              "description": "アイス"
            },
            {
              "used_at": "2023-02-02 12:30:00",
              "changed_amount": -600,
              "description": "たこ焼き"
            },
            {
              "used_at": "2023-02-02 17:30:00",
              "changed_amount": -1000,
              "description": "ラーメン"
            }
          ]
        }', true);
        $this->assertNotNull($expected, "Assumption failed");
        $request = $this->get("/api/usage_logs?from=2023-02-02T07:30:00");
        $request
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($expected);
        $this->assertSameSize($expected["logs"], $request->json("logs"));
    }

    /**
     * From: 2/1 00:00, To: 2/1 12:01の取得(UserId=100)
     * 12:00のデータが入っていることを確認
     */
    public function test_get_from0221t0000_to0221t1201(): void
    {
        $expected = json_decode('
        {
          "logs": [
            {
              "used_at": "2023-02-01 00:00:00",
              "changed_amount": 5000,
              "description": "チャージ"
            },
            {
              "used_at": "2023-02-01 07:00:00",
              "changed_amount": -100,
              "description": "アイス"
            },
            {
              "used_at": "2023-02-01 12:00:00",
              "changed_amount": -800,
              "description": "ラーメン"
            }
          ]
        }', true);
        $this->assertNotNull($expected, "Assumption failed");
        $request = $this->get("/api/usage_logs?from=2023-02-01T00:00:00&to=2023-02-01T12:01:00");
        $request
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson($expected);
        $this->assertSameSize($expected["logs"], $request->json("logs"));
    }

    /**
     * 無効な日付の場合
     */
    public function test_invalid_from_date(): void
    {
        $request = $this->getJson("/api/usage_logs?from=hey");
        $request->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
