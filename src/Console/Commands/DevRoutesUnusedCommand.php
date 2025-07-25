<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Console\Commands;

use Grazulex\LaravelDevtoolbox\DevtoolboxManager;
use Illuminate\Console\Command;

final class DevRoutesUnusedCommand extends Command
{
    protected $signature = 'dev:routes:unused 
                            {--format=table : Output format (table, json)}
                            {--output= : Save output to file}';

    protected $description = 'Detect potentially unused routes in your application';

    public function handle(DevtoolboxManager $manager): int
    {
        $format = $this->option('format');
        $output = $this->option('output');

        // Only show progress message if not outputting JSON directly
        if ($format !== 'json') {
            $this->info('Analyzing routes for unused ones...');
        }

        $result = $manager->scan('routes', [
            'detect_unused' => true,
            'format' => $format,
        ]);

        if ($output) {
            file_put_contents($output, json_encode($result, JSON_PRETTY_PRINT));
            if ($format !== 'json') {
                $this->info("Results saved to: {$output}");
            }
        } elseif ($format === 'json') {
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
        } else {
            $this->displayResults($result);
        }

        return self::SUCCESS;
    }

    private function displayResults(array $result): void
    {
        $data = $result['data'] ?? [];
        $routes = $data['routes'] ?? [];
        $unusedRoutes = array_filter($routes, fn ($route) => ($route['unused'] ?? false));

        $this->line('Found '.count($unusedRoutes).' potentially unused routes:');
        $this->newLine();

        foreach ($unusedRoutes as $route) {
            $methods = implode('|', $route['methods'] ?? ['GET']);
            $this->line("🛣️  {$methods} {$route['uri']}");
            if (isset($route['name'])) {
                $this->line("   Name: {$route['name']}");
            }
            if (isset($route['action'])) {
                $this->line("   Action: {$route['action']}");
            }
            $this->newLine();
        }

        if ($unusedRoutes === []) {
            $this->info('✅ No unused routes detected!');
        }
    }
}
