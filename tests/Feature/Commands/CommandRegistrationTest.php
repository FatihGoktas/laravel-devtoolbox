<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Tests\Feature\Commands;

use Exception;
use Tests\TestCase;

final class CommandRegistrationTest extends TestCase
{
    public function test_all_devtoolbox_commands_are_registered(): void
    {
        $commands = $this->artisan('list')->run();

        // List all available commands to debug
        $this->artisan('list')
            ->expectsOutput('Available commands:')
            ->assertExitCode(0);

        // Test individual commands registration
        $expectedCommands = [
            'dev:models',
            'dev:routes',
            'dev:commands',
            'dev:services',
            'dev:middleware',
            'dev:views',
            'dev:model:where-used',
        ];

        foreach ($expectedCommands as $command) {
            try {
                $this->artisan($command.' --help')->assertExitCode(0);
                $this->assertTrue(true, "Command {$command} is registered");
            } catch (Exception $e) {
                $this->fail("Command {$command} is not registered: ".$e->getMessage());
            }
        }
    }
}
