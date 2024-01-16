<?php

declare(strict_types=1);

namespace Browser\Main;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class MainTest extends DuskTestCase
{
    use DatabaseMigrations;

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
}
