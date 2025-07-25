<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Console\Commands;

use Exception;
use Grazulex\LaravelDevtoolbox\DevtoolboxManager;
use Illuminate\Console\Command;

final class DevViewsCommand extends Command
{
    protected $signature = 'dev:views {--format=table : Output format (table, json)} {--output= : Output file path}';

    protected $description = 'Scan and list all Blade views';

    public function handle(DevtoolboxManager $manager): int
    {
        $format = $this->option('format');
        $output = $this->option('output');

        $this->info('Scanning Blade views...');

        try {
            $result = $manager->scan('views', [
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
            $this->error('Error scanning views: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function displayResults(array $result): void
    {
        $data = $result['data']['views'] ?? [];
        $this->line('Found '.count($data).' views:');
        $this->newLine();

        foreach ($data as $view) {
            $this->line("📄 {$view['name']}");
            if (isset($view['path'])) {
                $this->line("   Path: {$view['path']}");
            }
            $this->newLine();
        }
    }
}
