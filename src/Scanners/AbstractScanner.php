<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Scanners;

use Grazulex\LaravelDevtoolbox\Contracts\ScannerInterface;
use Illuminate\Contracts\Foundation\Application;

abstract class AbstractScanner implements ScannerInterface
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get default options for all scanners
     */
    protected function getDefaultOptions(): array
    {
        return [
            'paths' => [],
            'exclude' => [],
            'format' => 'array',
            'include_metadata' => true,
        ];
    }

    /**
     * Merge options with defaults
     */
    protected function mergeOptions(array $options): array
    {
        return array_merge($this->getDefaultOptions(), $options);
    }

    /**
     * Format the output based on options
     */
    protected function formatOutput(array $data, array $options): array
    {
        $format = $options['format'] ?? 'array';

        return match ($format) {
            'json' => ['json' => json_encode($data, JSON_PRETTY_PRINT)],
            'count' => ['count' => count($data)],
            default => $data,
        };
    }

    /**
     * Add metadata to results if requested
     */
    protected function addMetadata(array $data, array $options): array
    {
        if (! ($options['include_metadata'] ?? true)) {
            return $data;
        }

        return [
            'metadata' => [
                'scanner' => $this->getName(),
                'description' => $this->getDescription(),
                'scanned_at' => now()->toISOString(),
                'count' => count($data),
            ],
            'data' => $data,
        ];
    }
}
