<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Console\Commands;

use Illuminate\Console\Command;

final class DevEnvDiffCommand extends Command
{
    protected $signature = 'dev:env:diff 
                            {--against=.env.example : Compare against this file}
                            {--format=array : Output format (array, json, count)}
                            {--output= : Save output to file}';

    protected $description = 'Compare environment configuration files';

    public function handle(): int
    {
        $against = $this->option('against');
        $this->option('format');
        $output = $this->option('output');

        $this->info("Comparing .env with {$against}...");

        $envFile = base_path('.env');
        $compareFile = base_path($against);

        if (! file_exists($envFile)) {
            $this->error('.env file not found');

            return self::FAILURE;
        }

        if (! file_exists($compareFile)) {
            $this->error("Comparison file {$against} not found");

            return self::FAILURE;
        }

        $envVars = $this->parseEnvFile($envFile);
        $compareVars = $this->parseEnvFile($compareFile);

        $result = [
            'missing_in_env' => array_diff_key($compareVars, $envVars),
            'missing_in_compare' => array_diff_key($envVars, $compareVars),
            'different_values' => [],
            'scanned_at' => now()->toISOString(),
        ];

        // Check for different values
        foreach ($envVars as $key => $value) {
            if (isset($compareVars[$key]) && $compareVars[$key] !== $value) {
                $result['different_values'][$key] = [
                    'env' => $value,
                    'compare' => $compareVars[$key],
                ];
            }
        }

        if ($output) {
            file_put_contents($output, json_encode($result, JSON_PRETTY_PRINT));
            $this->info("Results saved to: {$output}");
        } else {
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
        }

        return self::SUCCESS;
    }

    private function parseEnvFile(string $filePath): array
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $vars = [];

        foreach ($lines as $line) {
            if (str_starts_with(mb_trim($line), '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $vars[mb_trim($key)] = mb_trim($value);
            }
        }

        return $vars;
    }
}
