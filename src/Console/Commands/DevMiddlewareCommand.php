<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Console\Commands;

use Exception;
use Grazulex\LaravelDevtoolbox\DevtoolboxManager;
use Illuminate\Console\Command;

final class DevMiddlewareCommand extends Command
{
    protected $signature = 'dev:middleware {--format=table : Output format (table, json)} {--output= : Output file path}';

    protected $description = 'Scan and list all middleware';

    public function handle(DevtoolboxManager $manager): int
    {
        $format = $this->option('format');
        $output = $this->option('output');

        $this->info('Scanning middleware...');

        try {
            $result = $manager->scan('middleware', [
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
            $this->error('Error scanning middleware: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function displayResults(array $result): void
    {
        $data = $result['data'] ?? [];
        $this->line('Found '.count($data).' middleware:');
        $this->newLine();

        foreach ($data as $middleware) {
            $this->line("🛡️  {$middleware['class']}");
            if (isset($middleware['alias'])) {
                $this->line("   Alias: {$middleware['alias']}");
            }
            $this->newLine();
        }
    }
}
