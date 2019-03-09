<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\WineSpectatorCommand;
use Tests\TestCase;

/**
 * @coversDefaultClass \App\Console\Commands\WineSpectatorCommand
 */
class WineSpectatorCommandTest extends TestCase
{
    /**
     * @test
     */
    public function testCommandExists()
    {
        $this->assertTrue(class_exists(WineSpectatorCommand::class));
    }
}
