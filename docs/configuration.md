# Configuration

Laravel Devtoolbox provides extensive configuration options to customize scanning behavior and output formats.

## Publishing Configuration

To publish the configuration file:

```bash
php artisan vendor:publish --tag=devtoolbox-config
```

This creates `config/devtoolbox.php` with all available options.

## Configuration Structure

### Default Options

```php
'defaults' => [
    'format' => 'array',           // Default output format
    'include_metadata' => true,    // Include scanning metadata
    'exclude_paths' => [           // Paths to exclude from scanning
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
    ],
],
```

### Scanner-Specific Configuration

#### Models Scanner

```php
'models' => [
    'paths' => [
        'app/Models',              // Directories to scan for models
    ],
    'include_relationships' => true,  // Analyze model relationships
    'include_attributes' => true,     // Include model attributes
    'include_scopes' => true,         // Include query scopes
],
```

#### Routes Scanner

```php
'routes' => [
    'group_by_middleware' => false,   // Group routes by middleware
    'include_parameters' => true,     // Include route parameters
    'detect_unused' => false,         // Detect unused routes
],
```

#### Commands Scanner

```php
'commands' => [
    'custom_only' => false,           // Only scan custom commands
    'include_signatures' => true,     // Include command signatures
    'group_by_namespace' => false,    // Group by namespace
],
```

#### Services Scanner

```php
'services' => [
    'include_singletons' => true,     // Include singleton bindings
    'include_aliases' => true,        // Include service aliases
    'filter_custom' => false,         // Filter to custom services only
],
```

#### Views Scanner

```php
'views' => [
    'view_paths' => [
        // Will default to resource_path('views') if empty
    ],
    'detect_unused' => false,         // Detect unused views
    'include_components' => true,     // Include Blade components
],
```

#### Middleware Scanner

```php
'middleware' => [
    'include_usage' => true,          // Include middleware usage info
    'group_by_type' => true,          // Group by middleware type
],
```

### Output Configuration

#### Format Options

```php
'output' => [
    'formats' => [
        'json' => [
            'pretty_print' => true,       // Pretty print JSON
            'escape_unicode' => false,    // Escape Unicode characters
        ],
        'markdown' => [
            'include_toc' => true,        // Include table of contents
            'include_timestamps' => true, // Include scan timestamps
        ],
        'mermaid' => [
            'direction' => 'TB',          // Graph direction (TB, BT, RL, LR)
            'theme' => 'default',         // Mermaid theme
        ],
    ],
],
```

#### Export Settings

```php
'export' => [
    'default_path' => storage_path('devtoolbox'),    // Default export directory
    'filename_format' => 'devtoolbox-{type}-{date}', // Filename template
    'auto_timestamp' => true,                        // Auto-add timestamps
],
```

### Performance Configuration

```php
'performance' => [
    'memory_limit' => '512M',         // Memory limit for scanning
    'time_limit' => 300,              // Time limit in seconds
    'chunk_size' => 100,              // Chunk size for large datasets
],
```

## Environment-Specific Configuration

You can use different configurations per environment:

```php
// config/devtoolbox.php
return [
    'defaults' => [
        'format' => env('DEVTOOLBOX_DEFAULT_FORMAT', 'array'),
        'exclude_paths' => array_merge([
            'vendor',
            'node_modules',
            'storage',
        ], explode(',', env('DEVTOOLBOX_EXCLUDE_PATHS', ''))),
    ],
    
    // Production-specific settings
    'performance' => [
        'memory_limit' => env('DEVTOOLBOX_MEMORY_LIMIT', '512M'),
        'time_limit' => env('DEVTOOLBOX_TIME_LIMIT', 300),
    ],
];
```

Then in your `.env` file:

```env
DEVTOOLBOX_DEFAULT_FORMAT=json
DEVTOOLBOX_MEMORY_LIMIT=1G
DEVTOOLBOX_TIME_LIMIT=600
DEVTOOLBOX_EXCLUDE_PATHS=tests,docs
```

## Command-Line Overrides

Most configuration options can be overridden via command-line options:

```bash
# Override format
php artisan dev:scan models --format=json

# Override output location
php artisan dev:models --output=custom-location.json

# Multiple overrides
php artisan dev:model:graph --format=mermaid --direction=LR --output=graph.mmd
```

## Configuration Examples

### Development Environment

```php
// Optimized for development
'defaults' => [
    'format' => 'table',
    'include_metadata' => true,
],
'models' => [
    'include_relationships' => true,
    'include_attributes' => true,
    'include_scopes' => true,
],
'performance' => [
    'memory_limit' => '1G',
    'time_limit' => 600,
],
```

### CI/CD Environment

```php
// Optimized for automated processing
'defaults' => [
    'format' => 'json',
    'include_metadata' => false,
],
'performance' => [
    'memory_limit' => '2G',
    'time_limit' => 1800,
],
'export' => [
    'default_path' => '/tmp/devtoolbox',
    'auto_timestamp' => true,
],
```

### Production Analysis

```php
// Minimal impact configuration
'defaults' => [
    'format' => 'count',
    'exclude_paths' => [
        'vendor', 'node_modules', 'storage', 'tests', 'docs'
    ],
],
'performance' => [
    'memory_limit' => '256M',
    'time_limit' => 120,
    'chunk_size' => 50,
],
```

## Advanced Configuration

### Custom Scanner Paths

```php
'models' => [
    'paths' => [
        'app/Models',
        'app/Domain/*/Models',
        'packages/*/src/Models',
    ],
],
```

### Custom Exclusions

```php
'defaults' => [
    'exclude_paths' => [
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
        // Custom exclusions
        'app/Legacy',
        'resources/js/vendor',
        'tests/Fixtures',
    ],
],
```

### Output Customization

```php
'output' => [
    'formats' => [
        'json' => [
            'pretty_print' => env('APP_DEBUG', false),
            'escape_unicode' => false,
        ],
        'mermaid' => [
            'direction' => 'TB',
            'theme' => env('DEVTOOLBOX_MERMAID_THEME', 'default'),
        ],
    ],
],
```

## Validation

The configuration is validated at runtime. Invalid configurations will result in helpful error messages.

## Best Practices

1. **Use environment variables** for settings that change between environments
2. **Keep exclusions minimal** to ensure comprehensive scanning
3. **Adjust performance settings** based on your application size
4. **Use descriptive filename formats** for exported files
5. **Test configuration changes** with small scans first

## Troubleshooting

### Memory Issues
Increase `memory_limit` in performance configuration or use PHP's `-d` flag:
```bash
php -d memory_limit=2G artisan dev:scan --all
```

### Timeout Issues
Increase `time_limit` or use smaller chunk sizes for large applications.

### Path Issues
Ensure all configured paths exist and are readable by the PHP process.