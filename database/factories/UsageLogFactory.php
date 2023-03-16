<?php

namespace Database\Factories;

use App\ConstMessages;
use App\Models\UsageLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UsageLog>
 */
class UsageLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $changeAmount = fake()->numberBetween(-10000, 10000);
        $description = $changeAmount > 0 ? ConstMessages::CHARGE_DESCRIPTION : fake()->randomElement(["ラーメン", "たこ焼き", "アイス"]);
        return [
            UsageLog::KEY_USER_ID => 1,
            UsageLog::KEY_USED_AT => now(),
            UsageLog::KEY_CHANGED_AMOUNT => $changeAmount,
            UsageLog::KEY_DESCRIPTION => $description,
        ];
    }
}
