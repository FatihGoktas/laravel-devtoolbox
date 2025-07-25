# Installation

Laravel Devtoolbox is a development package designed to enhance your Laravel development workflow with powerful inspection and analysis tools.

## Requirements

- PHP 8.3 or higher
- Laravel 11.0 or higher
- Composer

## Installation

Install Laravel Devtoolbox via Composer as a development dependency:

```bash
composer require --dev grazulex/laravel-devtoolbox
```

## Service Provider Registration

The package uses Laravel's auto-discovery feature, so the service provider will be automatically registered. No manual registration is required.

## Configuration

Publish the configuration file to customize the package settings:

```bash
php artisan vendor:publish --tag=devtoolbox-config
```

This will create a `config/devtoolbox.php` file where you can customize various aspects of the scanning tools.

## Optional: Publish Views

If you need to customize the package views:

```bash
php artisan vendor:publish --tag=devtoolbox-views
```

## Verification

Verify the installation by listing all available dev commands:

```bash
php artisan list dev:
```

You should see a list of available Laravel Devtoolbox commands.

## Optional Dependencies

For enhanced functionality, consider installing these optional packages:

### Database Schema Generation
```bash
composer require --dev doctrine/dbal
```
Required for the `dev:generate-migration` command to generate migrations from models.

## Quick Test

Run a quick scan to ensure everything is working:

```bash
php artisan dev:scan models
```

This should display information about the models in your application.

## Next Steps

- Read the [Getting Started](getting-started.md) guide
- Explore the [Commands Reference](commands/)
- Check out the [Examples](../examples/)
- Review the [Configuration](configuration.md) options