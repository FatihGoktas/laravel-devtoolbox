<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Console\Commands;

use Illuminate\Console\Command;
use Grazulex\LaravelDevtoolbox\DevtoolboxManager;

class DevModelWhereUsedCommand extends Command
{
    protected $signature = 'dev:model:where-used 
                            {model : The model class name or path}
                            {--format=array : Output format (array, json, count)}
                            {--output= : Save output to file}';

    protected $description = 'Find where a specific model is used throughout the application';

    public function handle(DevtoolboxManager $manager): int
    {
        $model = $this->argument('model');
        $format = $this->option('format');
        $output = $this->option('output');

        $this->info("Analyzing usage of model: {$model}");

        // This would be implemented with a specialized scanner
        $result = [
            'model' => $model,
            'usage' => [
                'controllers' => [],
                'routes' => [],
                'views' => [],
                'other_models' => [],
            ],
            'scanned_at' => now()->toISOString(),
        ];

        if ($output) {
            file_put_contents($output, json_encode($result, JSON_PRETTY_PRINT));
            $this->info("Results saved to: {$output}");
        } else {
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
        }

        return self::SUCCESS;
    }
}
