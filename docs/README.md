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

# Enhanced application overview (NEW!)
php artisan dev:about+ --extended --performance

# Quick scan of your entire application
php artisan dev:scan --all

# Find where a model is used
php artisan dev:model:where-used App\Models\User

# Find routes by controller (NEW!)
php artisan dev:routes:where UserController

# Detect unused routes
php artisan dev:routes:unused

# Generate model relationship graph
php artisan dev:model:graph --format=mermaid --output=models.mmd

# Analyze SQL N+1 problems (NEW!)
php artisan dev:sql:duplicates --route=users.index --threshold=2

# Monitor logs in real-time (NEW!)
php artisan dev:log:tail --follow --level=error

# Analyze database column usage
php artisan dev:db:column-usage --unused-only

# Container binding analysis (NEW!)
php artisan dev:container:bindings --show-resolved

# Service provider performance (NEW!)
php artisan dev:providers:timeline --slow-threshold=100

# Security scan for unprotected routes
php artisan dev:security:unprotected-routes --critical-only
```

## 🔍 Available Scanners

Laravel Devtoolbox includes these powerful scanners:

| Scanner | Description | Commands |
|---------|-------------|----------|
| **General** | Enhanced application overview and comprehensive scanning | `dev:about+`, `dev:scan` |
| **Models** | Analyze Eloquent models, relationships, and usage | `dev:models`, `dev:model:where-used`, `dev:model:graph` |
| **Routes** | Inspect routes, detect unused ones, reverse lookups | `dev:routes`, `dev:routes:unused`, `dev:routes:where` |
| **Commands** | List and analyze artisan commands | `dev:commands` |
| **Services** | Examine service container bindings and providers | `dev:services`, `dev:container:bindings`, `dev:providers:timeline` |
| **Middleware** | Analyze middleware usage and configuration | `dev:middleware`, `dev:middlewares:where-used` |
| **Views** | Scan views and templates | `dev:views` |
| **Database** | Analyze database column usage and SQL performance | `dev:db:column-usage`, `dev:sql:trace`, `dev:sql:duplicates` |
| **Security** | Scan for unprotected routes and security issues | `dev:security:unprotected-routes` |
| **Environment** | Compare environment configurations | `dev:env:diff` |
| **Logging** | Real-time log monitoring and analysis | `dev:log:tail` |

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
- **⚡ Performance Analysis** - N+1 detection and provider timeline analysis
- **🛡️ Security Scanning** - Unprotected route detection
- **📋 Log Monitoring** - Real-time log analysis and filtering
- **🔗 Reverse Lookups** - Find routes by controller and middleware usage
- **📊 Multiple Formats** - Export to JSON, Markdown, Mermaid diagrams
- **🛠 Developer Experience** - Easy-to-use CLI with rich output

## 🤝 Contributing

Please see our [Contributing Guide](../CONTRIBUTING.md) for details on how to contribute to this project.

## 📄 License

Laravel Devtoolbox is open-sourced software licensed under the [MIT license](../LICENSE.md).