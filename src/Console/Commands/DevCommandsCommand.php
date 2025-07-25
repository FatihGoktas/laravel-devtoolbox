<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Console\Commands;

use Exception;
use Grazulex\LaravelDevtoolbox\DevtoolboxManager;
use Illuminate\Console\Command;

final class DevCommandsCommand extends Command
{
    protected $signature = 'dev:commands {--format=table : Output format (table, json)} {--output= : Output file path}';

    protected $description = 'Scan and list all Artisan commands';

    public function handle(DevtoolboxManager $manager): int
    {
        $format = $this->option('format');
        $output = $this->option('output');

        $this->info('Scanning Artisan commands...');

        try {
            $result = $manager->scan('commands', [
                'format' => $format,
            ]);

            if ($output) {
                file_put_contents($output, json_encode($result, JSON_PRETTY_PRINT));
                $this->info("Results saved to: {$output}");
            } else {
                $this->displayResults($result);
            }

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error scanning commands: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function displayResults(array $result): void
    {
        $data = $result['data'] ?? [];
        $this->line('Found '.count($data).' commands:');
        $this->newLine();

        foreach ($data as $command) {
            $this->line("⚡ {$command['name']}");
            if (isset($command['description'])) {
                $this->line("   {$command['description']}");
            }
            $this->newLine();
        }
    }
}
