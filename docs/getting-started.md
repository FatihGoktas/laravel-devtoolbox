# Getting Started

This guide will help you get up and running with Laravel Devtoolbox quickly.

## First Steps

After installation, familiarize yourself with the available commands:

```bash
# List all Laravel Devtoolbox commands
php artisan list dev:

# Get help for any specific command
php artisan dev:scan --help
```

## Basic Usage Patterns

### 1. General Scanning

The `dev:scan` command is your Swiss Army knife for application analysis:

```bash
# Scan all available types
php artisan dev:scan --all

# Scan a specific type
php artisan dev:scan models
php artisan dev:scan routes
php artisan dev:scan services

# List available scanner types
php artisan dev:scan
```

### 2. Model Analysis

Analyze your Eloquent models and their usage:

```bash
# List all models
php artisan dev:models

# Find where a specific model is used
php artisan dev:model:where-used App\Models\User

# Generate a relationship graph
php artisan dev:model:graph --format=mermaid
```

### 3. Route Inspection

Examine your application routes:

```bash
# List all routes
php artisan dev:routes

# Find unused routes
php artisan dev:routes:unused
```

### 4. SQL Query Analysis

Monitor database queries for specific routes:

```bash
# Trace SQL for a named route
php artisan dev:sql:trace --route=users.index

# Trace SQL for a URL
php artisan dev:sql:trace --url=/api/users --method=POST
```

## Output Formats

Most commands support multiple output formats:

```bash
# Human-readable table format (default)
php artisan dev:models --format=table

# JSON format for programmatic use
php artisan dev:models --format=json

# Save output to file
php artisan dev:models --format=json --output=models.json
```

## Common Workflows

### 1. Application Health Check

```bash
# Comprehensive application scan
php artisan dev:scan --all --output=app-scan.json

# Check for unused routes
php artisan dev:routes:unused

# Compare environment files
php artisan dev:env:diff --against=.env.example
```

### 2. Model Documentation

```bash
# Generate model documentation
php artisan dev:models --format=json --output=docs/models.json

# Create relationship diagram
php artisan dev:model:graph --format=mermaid --output=docs/models.mmd
```

### 3. Performance Analysis

```bash
# Trace SQL for slow endpoints
php artisan dev:sql:trace --route=dashboard --output=sql-analysis.json

# Analyze service container
php artisan dev:services --output=services.json
```

## Tips and Best Practices

### 1. Regular Scanning

Include scanning in your development workflow:

```bash
# Add to your development script
php artisan dev:scan --all > storage/logs/dev-scan.json
```

### 2. CI/CD Integration

Use in your continuous integration:

```bash
# Check for unused routes in CI
php artisan dev:routes:unused --format=count
```

### 3. Documentation Generation

Generate up-to-date documentation:

```bash
# Create comprehensive docs
php artisan dev:scan --all --format=json --output=docs/application-structure.json
```

### 4. Performance Monitoring

Monitor SQL performance during development:

```bash
# Check critical routes
php artisan dev:sql:trace --route=api.orders.index --output=logs/sql-$(date +%Y%m%d).json
```

## Configuration

For advanced usage, publish and customize the configuration:

```bash
php artisan vendor:publish --tag=devtoolbox-config
```

Edit `config/devtoolbox.php` to customize:
- Default output formats
- Scanner-specific options
- Performance settings
- Export configurations

## Next Steps

- Explore specific [Commands](commands/) in detail
- Check out practical [Examples](../examples/)
- Learn about [Output Formats](output-formats.md)
- Review [Configuration](configuration.md) options

## Common Issues

### Permission Errors
Make sure your storage directory is writable when using `--output` options.

### Memory Limits
For large applications, you may need to increase PHP memory limit:
```bash
php -d memory_limit=1G artisan dev:scan --all
```

### Missing Models
Ensure your models are properly autoloaded and follow PSR-4 conventions.