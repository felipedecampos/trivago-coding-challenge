<?php

namespace Tests\Unit\Console\Commands;

use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @coversDefaultClass \App\Console\Commands\WineSpectatorCommand
 */
class WineSpectatorCommandTest extends TestCase
{
    /**
     * @covers ::handle
     */
    public function testUpdateWinesWithoutAnyArgumentProvided()
    {
        $this->artisan('wine-spectator:watch', []);
    }

    /**
     * @covers ::handle
     */
    public function testUpdateAllWines()
    {
        $this->artisan('wine-spectator:watch', ['day' => 'all']);
    }

    /**
     * @covers ::handle
     */
    public function testUpdateWinesForToday()
    {
        $this->artisan('wine-spectator:watch', ['day' => 'today']);
    }
}
