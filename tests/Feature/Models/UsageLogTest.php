<?php

namespace Tests\Feature\Models;

use App\Models\UsageLog;
use Database\Seeders\UsageLogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsageLogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * UsageLog::getUserBalanceがintを返すことを確認する
     * 何も入っていないユーザー(0)、残高がマイナスのユーザー(3)、プラスのユーザー(100)で検証
     * @return void
     */
    public function test_get_balance_return_type(): void
    {
        $this->seed(UsageLogSeeder::class);
        foreach ([0, 3, 100] as $userId) {
            $this->assertIsInt(UsageLog::getUserBalance($userId));
        }
    }

}
