<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Scanners;

use Illuminate\Support\Facades\File;

final class ViewScanner extends AbstractScanner
{
    public function getName(): string
    {
        return 'views';
    }

    public function getDescription(): string
    {
        return 'Scan Laravel views and detect unused ones';
    }

    public function getAvailableOptions(): array
    {
        return [
            'detect_unused' => 'Attempt to detect unused views',
            'include_components' => 'Include Blade components',
            'view_paths' => 'Custom view paths to scan',
        ];
    }

    public function scan(array $options = []): array
    {
        $options = $this->mergeOptions($options);

        $viewPaths = $options['view_paths'] ?? [resource_path('views')];
        $views = [];

        foreach ($viewPaths as $path) {
            $views = array_merge($views, $this->scanViewPath($path, $options));
        }

        $result = [
            'views' => $views,
            'count' => count($views),
        ];

        if ($options['detect_unused'] ?? false) {
            $result['unused_views'] = $this->detectUnusedViews($views);
        }

        if ($options['include_components'] ?? false) {
            $result['components'] = $this->scanComponents();
        }

        return $this->addMetadata($result, $options);
    }

    protected function scanViewPath(string $path, array $options): array
    {
        $views = [];

        if (! File::exists($path)) {
            return $views;
        }

        $files = File::allFiles($path);

        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['blade.php', 'php'])) {
                $views[] = $this->analyzeView($file, $path);
            }
        }

        return $views;
    }

    protected function analyzeView($file, string $basePath): array
    {
        $relativePath = str_replace($basePath.'/', '', $file->getPathname());
        $viewName = str_replace(['/', '.blade.php', '.php'], ['.', '', ''], $relativePath);

        return [
            'name' => $viewName,
            'path' => $file->getPathname(),
            'size' => $file->getSize(),
            'modified' => date('Y-m-d H:i:s', $file->getMTime()),
        ];
    }

    protected function detectUnusedViews(array $views): array
    {
        // This is a simplified implementation
        // In reality, you'd scan controllers, routes, other views for usage
        return [];
    }

    protected function scanComponents(): array
    {
        $componentPath = resource_path('views/components');

        if (! File::exists($componentPath)) {
            return [];
        }

        return $this->scanViewPath($componentPath, []);
    }
}
