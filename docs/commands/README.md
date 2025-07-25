# Commands Reference

Laravel Devtoolbox provides a comprehensive set of artisan commands for analyzing and inspecting your Laravel application.

## General Commands

### `dev:scan`

The main scanning command that can analyze multiple aspects of your application.

```bash
php artisan dev:scan [type] [--all] [--format=FORMAT] [--output=FILE]
```

**Arguments:**
- `type` - Specific scanner type (models, routes, commands, services, middleware, views)

**Options:**
- `--all` - Scan all available types
- `--format=FORMAT` - Output format (array, json, count) 
- `--output=FILE` - Save output to file

**Examples:**
```bash
# List available scanner types
php artisan dev:scan

# Scan all types
php artisan dev:scan --all

# Scan specific type
php artisan dev:scan models

# Save scan results
php artisan dev:scan routes --format=json --output=routes.json
```

**Available Scanner Types:**
- `models` - Eloquent models and relationships
- `routes` - Application routes
- `commands` - Artisan commands
- `services` - Service container bindings
- `middleware` - Middleware classes and usage
- `views` - Blade templates and views
- `model-usage` - Model usage analysis
- `sql-trace` - SQL query tracing

---

## Model Commands

### `dev:models`

Scan and list all Eloquent models in your application.

```bash
php artisan dev:models [--format=FORMAT] [--output=FILE]
```

**Options:**
- `--format=FORMAT` - Output format (table, json)
- `--output=FILE` - Save output to file

**Examples:**
```bash
# List all models
php artisan dev:models

# Export models to JSON
php artisan dev:models --format=json --output=models.json
```

### `dev:model:where-used`

Find where a specific model is used throughout your application.

```bash
php artisan dev:model:where-used MODEL [--format=FORMAT] [--output=FILE]
```

**Arguments:**
- `MODEL` - The model class name or path

**Options:**
- `--format=FORMAT` - Output format (array, json, count)
- `--output=FILE` - Save output to file

**Examples:**
```bash
# Find where User model is used
php artisan dev:model:where-used App\Models\User

# Find usage by path
php artisan dev:model:where-used app/Models/Post.php

# Export results
php artisan dev:model:where-used User --format=json --output=user-usage.json
```

### `dev:model:graph`

Generate a graph showing model relationships.

```bash
php artisan dev:model:graph [--format=FORMAT] [--output=FILE] [--direction=DIR]
```

**Options:**
- `--format=FORMAT` - Output format (mermaid, json)
- `--output=FILE` - Save output to file
- `--direction=DIR` - Graph direction (TB, BT, LR, RL)

**Examples:**
```bash
# Generate Mermaid diagram
php artisan dev:model:graph --format=mermaid

# Save diagram to file
php artisan dev:model:graph --format=mermaid --output=models.mmd

# Left-to-right layout
php artisan dev:model:graph --format=mermaid --direction=LR
```

---

## Route Commands

### `dev:routes`

Scan and analyze application routes.

```bash
php artisan dev:routes [--format=FORMAT] [--output=FILE]
```

**Options:**
- `--format=FORMAT` - Output format (array, json, count)
- `--output=FILE` - Save output to file

**Examples:**
```bash
# List all routes
php artisan dev:routes

# Export routes data
php artisan dev:routes --format=json --output=routes.json
```

### `dev:routes:unused`

Detect potentially unused routes in your application.

```bash
php artisan dev:routes:unused [--format=FORMAT] [--output=FILE]
```

**Options:**
- `--format=FORMAT` - Output format (array, json, count)
- `--output=FILE` - Save output to file

**Examples:**
```bash
# Find unused routes
php artisan dev:routes:unused

# Count unused routes
php artisan dev:routes:unused --format=count

# Save analysis
php artisan dev:routes:unused --format=json --output=unused-routes.json
```

---

## Service Commands

### `dev:services`

Examine service container bindings and registrations.

```bash
php artisan dev:services [--format=FORMAT] [--output=FILE]
```

**Options:**
- `--format=FORMAT` - Output format (array, json, count)
- `--output=FILE` - Save output to file

**Examples:**
```bash
# List all services
php artisan dev:services

# Export service bindings
php artisan dev:services --format=json --output=services.json
```

### `dev:commands`

List and analyze artisan commands.

```bash
php artisan dev:commands [--format=FORMAT] [--output=FILE]
```

**Options:**
- `--format=FORMAT` - Output format (array, json, count)
- `--output=FILE` - Save output to file

**Examples:**
```bash
# List all commands
php artisan dev:commands

# Export commands data
php artisan dev:commands --format=json
```

---

## Middleware Commands

### `dev:middleware`

Analyze middleware classes and their usage.

```bash
php artisan dev:middleware [--format=FORMAT] [--output=FILE]
```

**Options:**
- `--format=FORMAT` - Output format (array, json, count)
- `--output=FILE` - Save output to file

**Examples:**
```bash
# List middleware
php artisan dev:middleware

# Export middleware analysis
php artisan dev:middleware --format=json --output=middleware.json
```

---

## View Commands

### `dev:views`

Scan and analyze Blade templates and views.

```bash
php artisan dev:views [--format=FORMAT] [--output=FILE]
```

**Options:**
- `--format=FORMAT` - Output format (array, json, count)
- `--output=FILE` - Save output to file

**Examples:**
```bash
# List all views
php artisan dev:views

# Export views data
php artisan dev:views --format=json --output=views.json
```

---

## SQL Analysis Commands

### `dev:sql:trace`

Trace SQL queries executed during route execution.

```bash
php artisan dev:sql:trace [--route=ROUTE] [--url=URL] [--method=METHOD] [--parameters=JSON] [--headers=JSON] [--output=FILE]
```

**Options:**
- `--route=ROUTE` - Named route to trace
- `--url=URL` - URL path to trace
- `--method=METHOD` - HTTP method (default: GET)
- `--parameters=JSON` - Route/query parameters as JSON
- `--headers=JSON` - Request headers as JSON
- `--output=FILE` - Save output to file

**Examples:**
```bash
# Trace a named route
php artisan dev:sql:trace --route=users.index

# Trace a URL with POST method
php artisan dev:sql:trace --url=/api/users --method=POST

# Trace with parameters
php artisan dev:sql:trace --route=users.show --parameters='{"user": 1}'

# Trace with custom headers
php artisan dev:sql:trace --url=/api/users --headers='{"Authorization": "Bearer token"}'

# Save detailed trace
php artisan dev:sql:trace --route=dashboard --output=sql-trace.json
```

---

## Environment Commands

### `dev:env:diff`

Compare environment configuration files.

```bash
php artisan dev:env:diff [--against=FILE] [--format=FORMAT] [--output=FILE]
```

**Options:**
- `--against=FILE` - File to compare against (default: .env.example)
- `--format=FORMAT` - Output format (array, json)
- `--output=FILE` - Save output to file

**Examples:**
```bash
# Compare .env with .env.example
php artisan dev:env:diff

# Compare with custom file
php artisan dev:env:diff --against=.env.staging

# Export differences
php artisan dev:env:diff --format=json --output=env-diff.json
```

---

## Common Options

### Format Options

All commands support these format options:

- `array` / `table` - Human-readable console output (default)
- `json` - Structured JSON output
- `count` - Count-only output
- `mermaid` - Diagram syntax (specific commands only)

### Output Options

Save results to files:

```bash
# JSON output
--output=filename.json

# Mermaid diagrams
--output=diagram.mmd

# Custom paths
--output=/path/to/file.json
```

## Advanced Usage

### Combining Commands

```bash
# Full application analysis
php artisan dev:scan --all --format=json --output=full-analysis.json

# Model-focused analysis
php artisan dev:models --format=json --output=models.json
php artisan dev:model:graph --format=mermaid --output=relationships.mmd
```

### CI/CD Integration

```bash
# Check thresholds
UNUSED_ROUTES=$(php artisan dev:routes:unused --format=count | jq '.count')
if [ $UNUSED_ROUTES -gt 5 ]; then
    echo "Too many unused routes: $UNUSED_ROUTES"
    exit 1
fi
```

### Automated Reporting

```bash
#!/bin/bash
DATE=$(date +%Y%m%d)
mkdir -p reports/$DATE

php artisan dev:scan models --format=json --output=reports/$DATE/models.json
php artisan dev:scan routes --format=json --output=reports/$DATE/routes.json
php artisan dev:routes:unused --format=json --output=reports/$DATE/unused-routes.json
```

## Error Handling

Commands return appropriate exit codes:
- `0` - Success
- `1` - General error
- `2` - Invalid arguments

Example error handling in scripts:
```bash
if ! php artisan dev:model:where-used InvalidModel; then
    echo "Model analysis failed"
    exit 1
fi
```