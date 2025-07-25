<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Console\Commands;

use Illuminate\Console\Command;
use Grazulex\LaravelDevtoolbox\DevtoolboxManager;

class DevModelGraphCommand extends Command
{
    protected $signature = 'dev:model:graph 
                            {--format=mermaid : Output format (mermaid, json)}
                            {--output= : Save output to file}
                            {--direction=TB : Graph direction (TB, BT, LR, RL)}';

    protected $description = 'Generate a graph of model relationships';

    public function handle(DevtoolboxManager $manager): int
    {
        $format = $this->option('format');
        $output = $this->option('output');
        $direction = $this->option('direction');

        $this->info('Generating model relationship graph...');

        $modelData = $manager->scan('models', [
            'include_relationships' => true,
        ]);

        $result = match ($format) {
            'mermaid' => $this->generateMermaidGraph($modelData, $direction),
            default => $modelData,
        };

        if ($output) {
            if ($format === 'mermaid') {
                file_put_contents($output, $result);
            } else {
                file_put_contents($output, json_encode($result, JSON_PRETTY_PRINT));
            }
            $this->info("Graph saved to: {$output}");
        } else {
            if ($format === 'mermaid') {
                $this->line($result);
            } else {
                $this->line(json_encode($result, JSON_PRETTY_PRINT));
            }
        }

        return self::SUCCESS;
    }

    private function generateMermaidGraph(array $modelData, string $direction): string
    {
        $graph = "graph {$direction}\n";
        
        // This is a simplified implementation
        if (isset($modelData['data']['data']) && is_array($modelData['data']['data'])) {
            foreach ($modelData['data']['data'] as $model) {
                $modelName = $model['name'] ?? 'Unknown';
                $graph .= "    {$modelName}[{$modelName}]\n";
                
                if (isset($model['relationships']) && is_array($model['relationships'])) {
                    foreach ($model['relationships'] as $relationship) {
                        $relName = $relationship['name'] ?? 'unknown';
                        $graph .= "    {$modelName} --> {$relName}\n";
                    }
                }
            }
        }
        
        return $graph;
    }
}
