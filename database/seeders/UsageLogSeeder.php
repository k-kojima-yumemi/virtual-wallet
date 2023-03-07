<?php

namespace Database\Seeders;

use App\Models\UsageLog;
use Illuminate\Database\Seeder;

class UsageLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UsageLog::factory(10)->create();
        $this->addCase2();
    }

    function addCase2(): void
    {
        // ダミーデータの配列
        // 左から金額、使用目的、日時
        $data = array(
            array(5000, "チャージ", "2023-02-01T00:00:00"),
            array(-100, "アイス", "2023-02-01T07:00:00"),
            array(-800, "ラーメン", "2023-02-01T012:00:00"),
            array(-600, "たこ焼き", "2023-02-01T018:00:00"),
            array(-200, "アイス", "2023-02-02T07:30:00"),
            array(-600, "たこ焼き", "2023-02-02T12:30:00"),
            array(-1000, "ラーメン", "2023-02-02T17:30:00"),
        );
        foreach ($data as $datum) {
            UsageLog::factory()->create([
                "user_id" => 2,
                "changed_amount" => $datum[0],
                "description" => $datum[1],
                "used_at" => $datum[2],
            ]);
        }
    }
}
