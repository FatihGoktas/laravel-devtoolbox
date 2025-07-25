<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Scanners;

final class MiddlewareScanner extends AbstractScanner
{
    public function getName(): string
    {
        return 'middleware';
    }

    public function getDescription(): string
    {
        return 'Scan Laravel middleware and their usage';
    }

    public function getAvailableOptions(): array
    {
        return [
            'include_usage' => 'Include middleware usage in routes',
            'group_by_type' => 'Group by global, route, and group middleware',
        ];
    }

    public function scan(array $options = []): array
    {
        $options = $this->mergeOptions($options);

        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

        $middleware = [
            'global' => $this->getGlobalMiddleware($kernel),
            'route' => $this->getRouteMiddleware($kernel),
            'group' => $this->getMiddlewareGroups($kernel),
        ];

        $result = [
            'middleware' => $middleware,
            'count' => array_sum(array_map('count', $middleware)),
        ];

        if ($options['include_usage'] ?? false) {
            $result['usage'] = $this->getMiddlewareUsage();
        }

        return $this->addMetadata($result, $options);
    }

    protected function getGlobalMiddleware($kernel): array
    {
        return method_exists($kernel, 'getGlobalMiddleware') ?
            $kernel->getGlobalMiddleware() : [];
    }

    protected function getRouteMiddleware($kernel): array
    {
        return method_exists($kernel, 'getRouteMiddleware') ?
            $kernel->getRouteMiddleware() : [];
    }

    protected function getMiddlewareGroups($kernel): array
    {
        return method_exists($kernel, 'getMiddlewareGroups') ?
            $kernel->getMiddlewareGroups() : [];
    }

    protected function getMiddlewareUsage(): array
    {
        // This would scan routes and count middleware usage
        return [
            'total_routes_with_middleware' => 0,
            'most_used_middleware' => [],
        ];
    }
}
