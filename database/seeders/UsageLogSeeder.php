<?php

namespace Database\Seeders;

use App\ConstMessages;
use App\Models\UsageLog;
use Illuminate\Database\Seeder;

class UsageLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ここのFactoryではUserId=1のデータを作成している
        UsageLog::factory(10)->create();
        $this->addUser2Data();
        $this->addUser3Data();
        $this->addUser100Data();
    }

    function addUser2Data(): void
    {
        // ダミーデータの配列
        // 左から金額、使用目的、日時
        $data = array(
            array(2000, ConstMessages::CHARGE_DESCRIPTION, "2023-02-04T00:00:00"),
            array(-100, "チョコレート", "2023-02-05T07:00:00"),
            array(-800, "ラーメン", "2023-02-05T012:00:00"),
            array(3000, ConstMessages::CHARGE_DESCRIPTION, "2023-02-05T012:00:00"),
            array(-600, "たこ焼き", "2023-02-05T018:00:00"),
            array(-200, "チョコレート", "2023-02-08T07:30:00"),
            array(-600, "たこ焼き", "2023-02-09T12:30:00"),
            array(-500, "たこ焼き", "2023-02-10T17:30:00"),
        );
        // 合計は2200円
        $this->addData($data, 2);
    }

    function addUser3Data(): void
    {
        // ダミーデータの配列
        // 左から金額、使用目的、日時
        $data = array(
            array(2000, ConstMessages::CHARGE_DESCRIPTION, "2023-02-04T00:00:00"),
            array(-1500, "ラーメン", "2023-02-04T17:00:00"),
            array(-1000, "ラーメン", "2023-02-05T18:00:00"),
        );
        // 合計は-500円
        $this->addData($data, 3);
    }

    function addUser100Data(): void
    {
        // ダミーデータの配列
        // 左から金額、使用目的、日時
        $data = array(
            array(5000, ConstMessages::CHARGE_DESCRIPTION, "2023-02-01T00:00:00"),
            array(-100, "アイス", "2023-02-01T07:00:00"),
            array(-800, "ラーメン", "2023-02-01T012:00:00"),
            array(-600, "たこ焼き", "2023-02-01T018:00:00"),
            array(-200, "アイス", "2023-02-02T07:30:00"),
            array(-600, "たこ焼き", "2023-02-02T12:30:00"),
            array(-1000, "ラーメン", "2023-02-02T17:30:00"),
        );
        // 金額欄の合計は1700円
        $this->addData($data, 100);
    }

    private function addData(array $data_array, int $userId): void
    {
        foreach ($data_array as $datum) {
            UsageLog::factory()->create([
                UsageLog::KEY_USER_ID => $userId,
                UsageLog::KEY_CHANGED_AMOUNT => $datum[0],
                UsageLog::KEY_DESCRIPTION => $datum[1],
                UsageLog::KEY_USED_AT => $datum[2],
            ]);
        }
    }
}
