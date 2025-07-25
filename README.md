# Laravel Devtoolbox

<div align="center">
  <img src="new_logo.png" alt="Laravel Devtoolbox" width="100">
  <p><strong>Swiss-army artisan CLI for Laravel — Scan, inspect, debug, and explore every aspect of your Laravel application from the command line.</strong></p>

  [![Latest Version](https://img.shields.io/packagist/v/grazulex/laravel-devtoolbox)](https://packagist.org/packages/grazulex/laravel-devtoolbox)
  [![Total Downloads](https://img.shields.io/packagist/dt/grazulex/laravel-devtoolbox)](https://packagist.org/packages/grazulex/laravel-devtoolbox)
  [![License](https://img.shields.io/github/license/grazulex/laravel-devtoolbox)](https://github.com/grazulex/laravel-devtoolbox/blob/main/LICENSE)
</div>

---

## ✨ Features

Laravel Devtoolbox provides comprehensive analysis tools for Laravel applications:

- **🔎 Deep Application Scanning** - Complete analysis of models, routes, services, and more
- **🧠 Model Introspection** - Analyze Eloquent models, relationships, and usage patterns
- **🛣️ Route Analysis** - Inspect routes, detect unused ones, and analyze middleware
- **📦 Service Container Analysis** - Examine bindings, singletons, and providers
- **⚙️ Environment Auditing** - Compare configuration files and detect inconsistencies
- **🔄 SQL Query Tracing** - Monitor and analyze database queries for specific routes
- **📊 Multiple Export Formats** - JSON, Markdown, Mermaid diagrams, and more
- **🛠 Developer Experience** - Rich console output with actionable insights

## 📦 Installation

Install via Composer as a development dependency:

```bash
composer require --dev grazulex/laravel-devtoolbox
```

**Requirements:**
- PHP 8.3+
- Laravel 11.0+

## 🚀 Quick Start

```bash
# See all available commands
php artisan list dev:

# Quick health check of your application
php artisan dev:scan --all

# Find where a model is used
php artisan dev:model:where-used App\Models\User

# Detect unused routes
php artisan dev:routes:unused

# Generate model relationship diagram
php artisan dev:model:graph --format=mermaid --output=models.mmd

# Trace SQL queries for a route
php artisan dev:sql:trace --route=dashboard

# Compare environment files
php artisan dev:env:diff --against=.env.example
```

## 🔍 Available Commands

### General Scanning
- `dev:scan` - Comprehensive application analysis with multiple scanner types

### Model Analysis
- `dev:models` - List and analyze all Eloquent models
- `dev:model:where-used` - Find where specific models are used
- `dev:model:graph` - Generate model relationship diagrams

### Route Analysis  
- `dev:routes` - Inspect application routes
- `dev:routes:unused` - Detect potentially unused routes

### Service & Container Analysis
- `dev:services` - Examine service container bindings
- `dev:commands` - List and analyze artisan commands
- `dev:middleware` - Analyze middleware classes and usage

### View Analysis
- `dev:views` - Scan Blade templates and views

### Performance & Debugging
- `dev:sql:trace` - Trace SQL queries for specific routes
- `dev:env:diff` - Compare environment configuration files

## 📊 Export Formats

All commands support multiple output formats:

| Format | Usage | Best For |
|--------|-------|----------|
| **Array/Table** | `--format=array` (default) | Interactive development |
| **JSON** | `--format=json` | Automation, CI/CD |
| **Count** | `--format=count` | Quick metrics |
| **Mermaid** | `--format=mermaid` | Documentation, diagrams |

### Save to Files

```bash
# Export to JSON
php artisan dev:models --format=json --output=models.json

# Generate Mermaid diagram
php artisan dev:model:graph --format=mermaid --output=relationships.mmd

# Save comprehensive scan
php artisan dev:scan --all --format=json --output=app-analysis.json
```

## 🛠 Configuration

Publish the configuration file to customize behavior:

```bash
php artisan vendor:publish --tag=devtoolbox-config
```

This creates `config/devtoolbox.php` where you can customize:
- Default output formats
- Scanner-specific options
- Performance settings
- Export configurations

## 📚 Documentation

- **[Complete Documentation](docs/)** - Comprehensive guides and references
- **[Getting Started](docs/getting-started.md)** - Quick start guide
- **[Commands Reference](docs/commands/)** - Detailed command documentation
- **[Configuration](docs/configuration.md)** - Configuration options
- **[Examples](examples/)** - Practical usage examples

## 🔧 Examples & Automation

### Daily Development Workflow

```bash
# Check application health
php artisan dev:scan --all --format=count

# Find cleanup opportunities
php artisan dev:routes:unused
php artisan dev:env:diff
```

### CI/CD Integration

```bash
# Quality gates in CI
UNUSED_ROUTES=$(php artisan dev:routes:unused --format=count | jq '.count')
if [ $UNUSED_ROUTES -gt 10 ]; then
  echo "Too many unused routes: $UNUSED_ROUTES"
  exit 1
fi
```

### Documentation Generation

```bash
# Generate project documentation
php artisan dev:models --format=json --output=docs/models.json
php artisan dev:model:graph --format=mermaid --output=docs/relationships.mmd
php artisan dev:routes --format=json --output=docs/routes.json
```

See the [examples directory](examples/) for complete automation scripts and CI/CD configurations.

## 🔍 Use Cases

- **🔍 Code Reviews** - Generate comprehensive application overviews
- **📊 Performance Analysis** - Identify slow queries and bottlenecks  
- **🧹 Technical Debt** - Find unused routes, orphaned models, and inconsistencies
- **📖 Documentation** - Auto-generate up-to-date application structure docs
- **⚡ CI/CD Quality Gates** - Automated quality checks and thresholds
- **🎯 Onboarding** - Help new team members understand application structure

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## 📄 License

Laravel Devtoolbox is open-sourced software licensed under the [MIT license](LICENSE.md).

---

<div align="center">
  <p>Made with ❤️ for the Laravel community</p>
  <p>
    <a href="https://github.com/grazulex/laravel-devtoolbox/issues">Report Issues</a> •
    <a href="https://github.com/grazulex/laravel-devtoolbox/discussions">Discussions</a> •
    <a href="https://github.com/grazulex/laravel-devtoolbox/wiki">Wiki</a>
  </p>
</div>
