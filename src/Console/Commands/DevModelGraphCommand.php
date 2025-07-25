<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Console\Commands;

use Grazulex\LaravelDevtoolbox\DevtoolboxManager;
use Illuminate\Console\Command;

final class DevModelGraphCommand extends Command
{
    protected $signature = 'dev:model:graph 
                            {--format=table : Output format (table, json)}
                            {--output= : Save output to file}
                            {--direction=TB : Graph direction (TB, BT, LR, RL)}';

    protected $description = 'Generate a graph of model relationships';

    public function handle(DevtoolboxManager $manager): int
    {
        $format = $this->option('format');
        $output = $this->option('output');
        $this->option('direction');

        // Only show progress message if not outputting JSON directly
        if ($format !== 'json') {
            $this->info('Generating model relationship graph...');
        }

        $modelData = $manager->scan('models', [
            'include_relationships' => true,
        ]);

        // Always use the model data as result (no more mermaid format)
        $result = $modelData;

        if ($output) {
            file_put_contents($output, json_encode($result, JSON_PRETTY_PRINT));
            if ($format !== 'json') {
                $this->info("Graph saved to: {$output}");
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
        $this->line('Found '.count($data).' models with relationships:');
        $this->newLine();

        foreach ($data as $model) {
            $modelName = $model['name'] ?? 'Unknown';
            $this->line("📄 {$modelName}");

            if (isset($model['relationships']) && ! empty($model['relationships'])) {
                foreach ($model['relationships'] as $relationshipName => $relationshipData) {
                    $type = $relationshipData['type'] ?? 'unknown';
                    $related = $relationshipData['related'] ?? 'unknown';
                    $this->line("   → {$relationshipName}: {$type} ({$related})");
                }
            } else {
                $this->line('   (no relationships found)');
            }
            $this->newLine();
        }
    }
}
