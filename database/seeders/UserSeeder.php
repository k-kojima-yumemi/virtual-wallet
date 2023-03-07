<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createTestUser();
        User::factory(10)->create();
    }

    function createTestUser(): void
    {
        User::factory()->create([
            "name" => "test user1",
            "password" => Hash::make("test-user-1"),
        ]);
        User::factory()->create([
            "name" => "test user2",
            "password" => Hash::make("test-user-2"),
        ]);
    }
}
