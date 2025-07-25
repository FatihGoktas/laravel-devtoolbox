<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Console\Commands;

use Grazulex\LaravelDevtoolbox\DevtoolboxManager;
use Illuminate\Console\Command;

final class DevRoutesUnusedCommand extends Command
{
    protected $signature = 'dev:routes:unused 
                            {--format=array : Output format (array, json, count)}
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
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
        }

        return self::SUCCESS;
    }
}
