<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Scanners;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

final class RouteWhereLookupScanner extends AbstractScanner
{
    public function getName(): string
    {
        return 'route-where-lookup';
    }

    public function getDescription(): string
    {
        return 'Find routes that use a specific controller or method';
    }

    public function getAvailableOptions(): array
    {
        return [
            'target' => 'Controller class or method to search for (required)',
            'show_methods' => 'Show available methods in the target controller',
            'include_parameters' => 'Include route parameters in results',
        ];
    }

    public function scan(array $options = []): array
    {
        $options = $this->mergeOptions($options);
        $target = $options['target'] ?? '';

        if (empty($target)) {
            return [
                'error' => 'Target controller or method is required',
                'usage' => 'Specify a controller like "UserController" or "UserController@show"',
            ];
        }

        return [
            'target' => $target,
            'matching_routes' => $this->findRoutesForTarget($target, $options),
            'controller_info' => $this->getControllerInfo($target, $options),
            'statistics' => $this->getSearchStatistics($target),
        ];
    }

    private function findRoutesForTarget(string $target, array $options): array
    {
        $routes = [];
        $allRoutes = RouteFacade::getRoutes();

        foreach ($allRoutes as $route) {
            if ($this->routeMatchesTarget($route, $target)) {
                $routes[] = $this->analyzeRoute($route, $options);
            }
        }

        return $routes;
    }

    private function routeMatchesTarget(Route $route, string $target): bool
    {
        $action = $route->getAction();
        
        if (!isset($action['controller'])) {
            return false;
        }

        $controller = $action['controller'];
        
        // Handle full controller@method notation
        if (str_contains($target, '@')) {
            return str_contains($controller, $target);
        }
        
        // Handle just controller name
        return str_contains($controller, $target);
    }

    private function analyzeRoute(Route $route, array $options): array
    {
        $action = $route->getAction();
        
        $routeData = [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'name' => $route->getName(),
            'controller' => $action['controller'] ?? 'Closure',
            'middleware' => $this->getRouteMiddleware($route),
        ];

        if ($options['include_parameters'] ?? false) {
            $routeData['parameters'] = $route->parameterNames();
            $routeData['where_conditions'] = $route->wheres;
        }

        return $routeData;
    }

    private function getRouteMiddleware(Route $route): array
    {
        $middleware = $route->middleware();
        
        // Clean up middleware names
        return array_map(function ($middleware) {
            if (is_string($middleware)) {
                return $middleware;
            }
            
            return is_object($middleware) ? get_class($middleware) : 'Unknown';
        }, $middleware);
    }

    private function getControllerInfo(string $target, array $options): array
    {
        // Extract controller class name
        $controllerClass = $this->extractControllerClass($target);
        
        if (!$controllerClass || !class_exists($controllerClass)) {
            return [
                'exists' => false,
                'class' => $controllerClass,
                'error' => 'Controller class not found',
            ];
        }

        $info = [
            'exists' => true,
            'class' => $controllerClass,
            'file' => $this->getControllerFile($controllerClass),
        ];

        if ($options['show_methods'] ?? false) {
            $info['methods'] = $this->getControllerMethods($controllerClass);
        }

        return $info;
    }

    private function extractControllerClass(string $target): ?string
    {
        // Remove @method if present
        $controller = str_contains($target, '@') ? 
            explode('@', $target)[0] : 
            $target;

        // If it's already a full class name, return it
        if (str_contains($controller, '\\')) {
            return $controller;
        }

        // Try to find in common controller namespaces
        $possibleNamespaces = [
            'App\\Http\\Controllers\\',
            'App\\Http\\Controllers\\Api\\',
            'App\\Http\\Controllers\\Admin\\',
        ];

        foreach ($possibleNamespaces as $namespace) {
            $fullClass = $namespace . $controller;
            if (class_exists($fullClass)) {
                return $fullClass;
            }
        }

        return $controller;
    }

    private function getControllerFile(string $controllerClass): ?string
    {
        try {
            $reflection = new ReflectionClass($controllerClass);
            return $reflection->getFileName() ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getControllerMethods(string $controllerClass): array
    {
        try {
            $reflection = new ReflectionClass($controllerClass);
            $methods = [];

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                // Skip magic methods and inherited methods from base classes
                if (str_starts_with($method->getName(), '__') || 
                    $method->getDeclaringClass()->getName() !== $controllerClass) {
                    continue;
                }

                $methods[] = [
                    'name' => $method->getName(),
                    'parameters' => array_map(function ($param) {
                        return [
                            'name' => $param->getName(),
                            'type' => $param->getType() ? $param->getType()->getName() : 'mixed',
                            'optional' => $param->isOptional(),
                        ];
                    }, $method->getParameters()),
                ];
            }

            return $methods;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getSearchStatistics(string $target): array
    {
        $allRoutes = RouteFacade::getRoutes();
        $totalRoutes = count($allRoutes);
        $matchingRoutes = 0;

        foreach ($allRoutes as $route) {
            if ($this->routeMatchesTarget($route, $target)) {
                $matchingRoutes++;
            }
        }

        return [
            'total_routes' => $totalRoutes,
            'matching_routes' => $matchingRoutes,
            'match_percentage' => $totalRoutes > 0 ? round(($matchingRoutes / $totalRoutes) * 100, 2) : 0,
        ];
    }
}
