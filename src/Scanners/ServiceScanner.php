<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Scanners;

final class ServiceScanner extends AbstractScanner
{
    public function getName(): string
    {
        return 'services';
    }

    public function getDescription(): string
    {
        return 'Scan Laravel service container bindings';
    }

    public function getAvailableOptions(): array
    {
        return [
            'include_singletons' => 'Include singleton services separately',
            'include_aliases' => 'Include service aliases',
            'filter_custom' => 'Show only custom (non-Laravel) services',
        ];
    }

    public function scan(array $options = []): array
    {
        $options = $this->mergeOptions($options);

        // Use reflection to access protected bindings property
        $bindings = $this->getAppBindings();
        $services = [];

        foreach ($bindings as $abstract => $binding) {
            $serviceData = $this->analyzeService($abstract, $binding, $options);

            if ($options['filter_custom'] ?? false) {
                if ($this->isCustomService($abstract)) {
                    $services[] = $serviceData;
                }
            } else {
                $services[] = $serviceData;
            }
        }

        $result = [
            'services' => $services,
            'count' => count($services),
        ];

        if ($options['include_singletons'] ?? false) {
            $result['singletons'] = $this->getSingletons();
        }

        if ($options['include_aliases'] ?? false) {
            $result['aliases'] = $this->getAliases();
        }

        return $this->addMetadata($result, $options);
    }

    protected function analyzeService(string $abstract, array $binding, array $options): array
    {
        return [
            'abstract' => $abstract,
            'concrete' => $binding['concrete'] ?? null,
            'shared' => $binding['shared'] ?? false,
        ];
    }

    protected function isCustomService(string $abstract): bool
    {
        $laravelServices = [
            'Illuminate\\', 'Laravel\\', 'Symfony\\', 'Psr\\',
            'app', 'auth', 'cache', 'config', 'db', 'events',
            'files', 'log', 'queue', 'redis', 'request', 'response',
            'route', 'session', 'validator', 'view',
        ];

        foreach ($laravelServices as $service) {
            if (str_starts_with($abstract, $service)) {
                return false;
            }
        }

        return true;
    }

    protected function getSingletons(): array
    {
        $singletons = [];
        $bindings = $this->getAppBindings();

        foreach ($bindings as $abstract => $binding) {
            if ($binding['shared'] ?? false) {
                $singletons[] = [
                    'abstract' => $abstract,
                    'concrete' => $binding['concrete'] ?? null,
                ];
            }
        }

        return $singletons;
    }

    protected function getAliases(): array
    {
        return $this->getAppAliases();
    }

    /**
     * Get application bindings using reflection
     */
    private function getAppBindings(): array
    {
        try {
            $reflection = new \ReflectionClass($this->app);
            $property = $reflection->getProperty('bindings');
            $property->setAccessible(true);
            return $property->getValue($this->app) ?? [];
        } catch (\ReflectionException $e) {
            return [];
        }
    }

    /**
     * Get application aliases using reflection
     */
    private function getAppAliases(): array
    {
        try {
            $reflection = new \ReflectionClass($this->app);
            $property = $reflection->getProperty('aliases');
            $property->setAccessible(true);
            return $property->getValue($this->app) ?? [];
        } catch (\ReflectionException $e) {
            return [];
        }
    }
}
