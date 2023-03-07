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
    }
}
