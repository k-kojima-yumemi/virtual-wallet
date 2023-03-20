<?php

namespace Tests\Feature\Models;

use App\Models\UsageLog;
use Database\Seeders\UsageLogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsageLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsageLogSeeder::class);
    }

    /**
     * 指定したユーザーの残高が正確に取得できていることを確認する
     * 何も入っていないユーザー(99)、残高がマイナスのユーザー(3)、プラスのユーザー(100)で検証
     *
     * @dataProvider userIdAndBalance
     * @param int $userId
     * @param int $expectedBalance
     * @return void
     */
    public function test_get_balance(int $userId, int $expectedBalance): void
    {
        $this->assertSame($expectedBalance, UsageLog::getUserBalance($userId),
            "User $userId");
    }

    public function userIdAndBalance(): array
    {
        // array of userId, expected-Balance
        return array(
            [100, 1700],  // default user with plus balance
            [99, 0], // not existing user
            [3, -500],  // minus balance
        );
    }
}
