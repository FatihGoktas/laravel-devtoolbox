# Laravel Devtoolbox Documentation

Welcome to Laravel Devtoolbox! This comprehensive developer toolkit provides powerful artisan commands to scan, inspect, debug, and explore every aspect of your Laravel application from the command line.

## 📚 Documentation Index

- [Installation](installation.md) - How to install and configure Laravel Devtoolbox
- [Getting Started](getting-started.md) - Quick start guide and basic usage
- [Commands Reference](commands/) - Complete reference for all available commands
- [Configuration](configuration.md) - Configuration options and customization
- [Output Formats](output-formats.md) - Available export formats and options
- [Examples](../examples/) - Practical usage examples

## 🚀 Quick Start

```bash
# Install the package
composer require --dev grazulex/laravel-devtoolbox

# See all available commands
php artisan list dev:

# Quick scan of your entire application
php artisan dev:scan --all

# Find where a model is used
php artisan dev:model:where-used App\Models\User

# Detect unused routes
php artisan dev:routes:unused

# Generate model relationship graph
php artisan dev:model:graph --format=mermaid --output=models.mmd
```

## 🔍 Available Scanners

Laravel Devtoolbox includes these powerful scanners:

| Scanner | Description | Command |
|---------|-------------|---------|
| **Models** | Analyze Eloquent models, relationships, and usage | `dev:models`, `dev:model:where-used` |
| **Routes** | Inspect routes, detect unused ones | `dev:routes`, `dev:routes:unused` |
| **Commands** | List and analyze artisan commands | `dev:commands` |
| **Services** | Examine service container bindings | `dev:services` |
| **Middleware** | Analyze middleware usage and configuration | `dev:middleware` |
| **Views** | Scan views and templates | `dev:views` |
| **SQL Tracing** | Trace SQL queries for specific routes | `dev:sql:trace` |
| **Environment** | Compare environment configurations | `dev:env:diff` |

## 📊 Export Formats

All scanners support multiple output formats:

- **Array/Table** - Human-readable console output
- **JSON** - Machine-readable structured data
- **Markdown** - Documentation-friendly format
- **Mermaid** - Graph diagrams (for relationship visualization)

## 🛠 Core Features

- **🔎 Deep Scanning** - Comprehensive analysis of Laravel applications
- **🧠 Introspection** - Detailed insights into routes, controllers, and models
- **🔍 Dead Code Detection** - Find unused routes, views, and code
- **📦 Container Analysis** - Service provider and dependency injection analysis
- **⚙️ Configuration Auditing** - Environment consistency checks
- **🔄 SQL Tracing** - Monitor and analyze database queries
- **📊 Multiple Formats** - Export to JSON, Markdown, Mermaid diagrams
- **🛠 Developer Experience** - Easy-to-use CLI with rich output

## 🤝 Contributing

Please see our [Contributing Guide](../CONTRIBUTING.md) for details on how to contribute to this project.

## 📄 License

Laravel Devtoolbox is open-sourced software licensed under the [MIT license](../LICENSE.md).