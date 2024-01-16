<?php

declare(strict_types=1);

namespace Browser\Main;

use Database\Seeders\UsageLogSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class MainTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            UserSeeder::class,
            UsageLogSeeder::class,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function testLoadMainPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit("/")
                ->assertSee("今の残高");
        });
    }

    /**
     * @throws Throwable
     */
    public function testLoadUsePage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit("/use")
                ->assertSee("残高の使用");
        });
    }

    /**
     * @throws Throwable
     */
    public function testLoadChargePage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit("/charge")
                ->assertSee("チャージする金額");
        });
    }

    /**
     * @throws Throwable
     */
    public function testLoadLogPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit("/logs")
                ->assertSee("使用履歴");
        });
    }

    /**
     * @throws Throwable
     */
    public function testAccessCharge(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit("/")
                ->clickAndWaitForReload("#chargeButton")
                ->assertPathIs("/charge");
        });
    }

    /**
     * @throws Throwable
     */
    public function testAccessUse(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit("/")
                ->clickAndWaitForReload("#useButton")
                ->assertPathIs("/use");
        });
    }

    /**
     * @throws Throwable
     */
    public function testAccessLog(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit("/")
                ->clickAndWaitForReload("#logsButton")
                ->assertPathIs("/logs");
        });
    }
}
