<?php

namespace Tests\Feature;

use Database\Seeders\UsageLogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BalanceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_assumption_db_name(): void
    {
        $this->assertEquals("testing", DB::getDatabaseName());
    }

    public function test_user_default_balance(): void
    {
        $this->seed(UsageLogSeeder::class);
        $response = $this->get("/api/balance");
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 1700]);
    }

    public function test_user_2_balance(): void
    {
        $this->seed(UsageLogSeeder::class);
        $response = $this->withHeaders(["user_id" => 2])->get("/api/balance");
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 2200]);
    }

    public function test_user_no_existence_balance(): void
    {
        $this->seed(UsageLogSeeder::class);
        $response = $this->withHeaders(["user_id" => -1])->get("/api/balance");
        $response
            ->assertStatus(200)
            ->assertJson(["balance" => 0]);
    }
}
