<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Scanners;

use Illuminate\Support\Facades\Route;

final class RouteScanner extends AbstractScanner
{
    public function getName(): string
    {
        return 'routes';
    }

    public function getDescription(): string
    {
        return 'Scan Laravel routes and analyze their usage';
    }

    public function getAvailableOptions(): array
    {
        return [
            'group_by_middleware' => 'Group routes by their middleware',
            'include_parameters' => 'Include route parameters information',
            'detect_unused' => 'Attempt to detect unused routes',
            'filter_methods' => 'Filter by HTTP methods (array)',
        ];
    }

    public function scan(array $options = []): array
    {
        $options = $this->mergeOptions($options);

        $routes = collect(Route::getRoutes())->map(function ($route) use ($options) {
            return $this->analyzeRoute($route, $options);
        })->toArray();

        $result = [
            'routes' => $routes,
            'count' => count($routes),
        ];

        if ($options['group_by_middleware'] ?? false) {
            $result['grouped_by_middleware'] = $this->groupByMiddleware($routes);
        }

        if ($options['detect_unused'] ?? false) {
            $result['unused_routes'] = $this->detectUnusedRoutes($routes);
        }

        return $this->addMetadata($result, $options);
    }

    protected function analyzeRoute($route, array $options): array
    {
        $routeData = [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'methods' => $route->methods(),
            'action' => $route->getActionName(),
            'middleware' => $route->middleware(),
        ];

        if ($options['include_parameters'] ?? false) {
            $routeData['parameters'] = $route->parameterNames();
            $routeData['where_conditions'] = $route->wheres;
        }

        return $routeData;
    }

    protected function groupByMiddleware(array $routes): array
    {
        $grouped = [];

        foreach ($routes as $route) {
            $middleware = $route['middleware'] ?? [];

            if (empty($middleware)) {
                $grouped['no_middleware'][] = $route;
            } else {
                foreach ($middleware as $mid) {
                    $grouped[$mid][] = $route;
                }
            }
        }

        return $grouped;
    }

    protected function detectUnusedRoutes(array $routes): array
    {
        // This is a simplified implementation
        // In reality, you'd scan controllers, views, etc. for route usage
        $unused = [];

        foreach ($routes as $route) {
            // Simple heuristic: routes without names might be unused
            if (empty($route['name']) && ! str_contains($route['uri'], 'api/')) {
                $unused[] = $route;
            }
        }

        return $unused;
    }
}
