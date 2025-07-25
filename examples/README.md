# Laravel Devtoolbox Examples

This directory contains practical examples demonstrating how to use Laravel Devtoolbox in various scenarios.

## 📁 Directory Structure

- [`scripts/`](scripts/) - Automation scripts and workflows
- [`ci-cd/`](ci-cd/) - Continuous integration and deployment examples
- [`output-samples/`](output-samples/) - Sample outputs in different formats

## 🚀 Quick Examples

### Basic Usage

```bash
# Quick health check
php artisan dev:scan --all

# Find model usage
php artisan dev:model:where-used App\Models\User

# Check for unused routes
php artisan dev:routes:unused

# Generate relationship diagram
php artisan dev:model:graph --format=mermaid --output=models.mmd
```

### Export Data

```bash
# Export all data for documentation
php artisan dev:scan --all --format=json --output=app-structure.json

# Export specific analyses
php artisan dev:models --format=json --output=models.json
php artisan dev:routes --format=json --output=routes.json
```

### Performance Analysis

```bash
# Trace SQL for critical routes
php artisan dev:sql:trace --route=dashboard
php artisan dev:sql:trace --url=/api/orders --method=POST

# Analyze service container
php artisan dev:services --format=json --output=services.json
```

## 📋 Example Categories

### 1. Development Workflows

**Daily Development Checks**
```bash
#!/bin/bash
echo "🔍 Running daily development checks..."

# Check for unused routes
echo "📍 Checking for unused routes..."
php artisan dev:routes:unused --format=count

# Quick model overview
echo "📊 Model overview..."
php artisan dev:models --format=count

# Environment consistency
echo "⚙️ Environment check..."
php artisan dev:env:diff --against=.env.example
```

**Code Review Preparation**
```bash
#!/bin/bash
echo "📝 Generating code review documentation..."

# Model relationships
php artisan dev:model:graph --format=mermaid --output=docs/models.mmd

# Route analysis
php artisan dev:routes --format=json --output=docs/routes.json

# Service bindings
php artisan dev:services --format=json --output=docs/services.json
```

### 2. CI/CD Integration

**Quality Gates**
```bash
#!/bin/bash
# Check for code quality issues

UNUSED_ROUTES=$(php artisan dev:routes:unused --format=count | jq '.count')
if [ $UNUSED_ROUTES -gt 10 ]; then
    echo "❌ Too many unused routes: $UNUSED_ROUTES"
    exit 1
fi

echo "✅ Quality checks passed"
```

**Documentation Generation**
```bash
#!/bin/bash
# Auto-generate documentation
DATE=$(date +%Y%m%d)
mkdir -p docs/generated/$DATE

php artisan dev:scan --all --format=json --output=docs/generated/$DATE/full-scan.json
php artisan dev:model:graph --format=mermaid --output=docs/generated/$DATE/models.mmd
```

### 3. Performance Monitoring

**SQL Performance Tracking**
```bash
#!/bin/bash
# Monitor critical endpoints
ROUTES=("dashboard" "api.users.index" "api.orders.index")

for route in "${ROUTES[@]}"; do
    echo "🔍 Tracing $route..."
    php artisan dev:sql:trace --route=$route --output=logs/sql-$route-$(date +%Y%m%d).json
done
```

**Application Health Monitoring**
```bash
#!/bin/bash
# Weekly health check
php artisan dev:scan --all --format=json --output=reports/health-$(date +%Y%m%d).json

# Check for issues
UNUSED_COUNT=$(php artisan dev:routes:unused --format=count | jq '.count')
echo "Unused routes: $UNUSED_COUNT" >> reports/summary-$(date +%Y%m%d).txt
```

## 🛠 Automation Scripts

### Model Analysis Script

```bash
#!/bin/bash
# Comprehensive model analysis

echo "🔍 Analyzing models..."

# Basic model info
php artisan dev:models --format=json --output=analysis/models.json

# Model relationships graph
php artisan dev:model:graph --format=mermaid --output=analysis/relationships.mmd

# Check specific model usage
MODELS=("User" "Post" "Order")
for model in "${MODELS[@]}"; do
    echo "📊 Analyzing $model usage..."
    php artisan dev:model:where-used "App\Models\$model" --format=json --output="analysis/$model-usage.json"
done

echo "✅ Model analysis complete"
```

### Route Health Check

```bash
#!/bin/bash
# Route health and performance check

echo "🛣️ Checking route health..."

# List all routes
php artisan dev:routes --format=json --output=analysis/all-routes.json

# Find unused routes
php artisan dev:routes:unused --format=json --output=analysis/unused-routes.json

# Trace critical routes
CRITICAL_ROUTES=("home" "dashboard" "api.status")
for route in "${CRITICAL_ROUTES[@]}"; do
    echo "⚡ Tracing $route..."
    php artisan dev:sql:trace --route=$route --output="analysis/sql-$route.json"
done

echo "✅ Route analysis complete"
```

## 📊 Data Processing Examples

### JSON Data Processing with jq

```bash
# Find models with many relationships
php artisan dev:models --format=json | jq '.data[] | select(.relationships | length > 5) | .class'

# Count routes by middleware
php artisan dev:routes --format=json | jq '.data | group_by(.middleware[]) | map({middleware: .[0].middleware[0], count: length})'

# Find slow SQL queries
php artisan dev:sql:trace --route=dashboard --format=json | jq '.data.queries[] | select(.time > 100)'
```

### Excel/CSV Export

```bash
#!/bin/bash
# Convert JSON to CSV for Excel analysis

# Models to CSV
php artisan dev:models --format=json | jq -r '.data[] | [.class, .file, (.relationships | length)] | @csv' > models.csv

# Routes to CSV  
php artisan dev:routes --format=json | jq -r '.data[] | [.name, .uri, .methods[0], .controller] | @csv' > routes.csv
```

## 🔧 Integration Examples

### Git Hooks

**Pre-commit Hook**
```bash
#!/bin/bash
# .git/hooks/pre-commit

echo "🔍 Running pre-commit checks..."

# Check for unused routes
UNUSED=$(php artisan dev:routes:unused --format=count | jq '.count')
if [ $UNUSED -gt 20 ]; then
    echo "❌ Too many unused routes: $UNUSED"
    echo "Please clean up unused routes before committing"
    exit 1
fi

echo "✅ Pre-commit checks passed"
```

**Post-merge Hook**
```bash
#!/bin/bash
# .git/hooks/post-merge

echo "📊 Updating documentation after merge..."

# Update model diagrams
php artisan dev:model:graph --format=mermaid --output=docs/models.mmd

# Update route documentation
php artisan dev:routes --format=json --output=docs/routes.json

git add docs/
git commit -m "docs: update auto-generated documentation" || true
```

### Laravel Telescope Integration

```php
// In a custom Telescope watcher
class DevtoolboxWatcher extends Watcher
{
    public function register($app)
    {
        $app['events']->listen('*', function ($event, $data) {
            if ($this->shouldRecordDevtoolboxMetrics()) {
                $this->recordDevtoolboxScan();
            }
        });
    }
    
    protected function recordDevtoolboxScan()
    {
        $result = app('devtoolbox')->scan('routes', ['format' => 'count']);
        
        Telescope::recordMetric([
            'type' => 'devtoolbox_routes',
            'value' => $result['count'] ?? 0,
        ]);
    }
}
```

## 🎯 Use Case Scenarios

### Scenario 1: New Team Member Onboarding

```bash
#!/bin/bash
# Generate comprehensive application overview for new developers

echo "📋 Generating application overview..."

# Application structure
php artisan dev:scan --all --format=json --output=onboarding/app-structure.json

# Visual model relationships
php artisan dev:model:graph --format=mermaid --output=onboarding/models.mmd

# Route documentation
php artisan dev:routes --format=json --output=onboarding/routes.json

# Service overview
php artisan dev:services --format=json --output=onboarding/services.json

echo "✅ Onboarding documentation generated in onboarding/"
```

### Scenario 2: Performance Audit

```bash
#!/bin/bash
# Comprehensive performance audit

echo "⚡ Starting performance audit..."

# Critical routes to monitor
ROUTES=("dashboard" "api.users.index" "reports.monthly")

for route in "${ROUTES[@]}"; do
    echo "🔍 Auditing $route..."
    php artisan dev:sql:trace --route=$route --output="audit/sql-$route.json"
done

# Service container analysis
php artisan dev:services --format=json --output=audit/services.json

echo "✅ Performance audit complete"
```

### Scenario 3: Legacy Code Analysis

```bash
#!/bin/bash
# Analyze legacy code for refactoring

echo "🔧 Analyzing legacy code..."

# Find unused routes (potential cleanup candidates)
php artisan dev:routes:unused --format=json --output=legacy/unused-routes.json

# Model usage analysis (find tightly coupled models)
MODELS=("LegacyUser" "OldOrder" "DeprecatedProduct")
for model in "${MODELS[@]}"; do
    if [ -f "app/Models/$model.php" ]; then
        echo "📊 Analyzing $model..."
        php artisan dev:model:where-used "App\Models\$model" --format=json --output="legacy/$model-usage.json"
    fi
done

echo "✅ Legacy analysis complete"
```

## 📚 Next Steps

1. Explore the [`scripts/`](scripts/) directory for ready-to-use automation scripts
2. Check [`ci-cd/`](ci-cd/) for continuous integration examples
3. Review [`output-samples/`](output-samples/) to understand data formats
4. Adapt examples to your specific workflow needs

## 🔗 Related Documentation

- [Commands Reference](../docs/commands/)
- [Configuration Guide](../docs/configuration.md)
- [Output Formats](../docs/output-formats.md)
- [Getting Started](../docs/getting-started.md)