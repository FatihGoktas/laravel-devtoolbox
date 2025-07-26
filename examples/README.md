# Laravel Devtoolbox Examples

This directory contains practical examples demonstrating how to use Laravel Devtoolbox in various scenarios.

## 📁 Directory Structure

- [`scripts/`](scripts/) - Automation scripts and workflows
- [`ci-cd/`](ci-cd/) - Continuous integration and deployment examples
- [`output-samples/`](output-samples/) - Sample outputs in different formats

## 🚀 Quick Examples

### Basic Usage

```bash
# Enhanced application overview (NEW!)
php artisan dev:about+ --extended --performance

# Quick health check
php artisan dev:scan --all

# Find model usage
php artisan dev:model:where-used App\Models\User

# Find routes by controller (NEW!)
php artisan dev:routes:where UserController

# Check for unused routes
php artisan dev:routes:unused

# Generate relationship diagram
php artisan dev:model:graph --format=mermaid --output=models.mmd

# Analyze database column usage
php artisan dev:db:column-usage --unused-only

# Security scan for unprotected routes
php artisan dev:security:unprotected-routes --critical-only

# Monitor logs in real-time (NEW!)
php artisan dev:log:tail --follow --level=error

# Analyze SQL N+1 problems (NEW!)
php artisan dev:sql:duplicates --route=users.index --threshold=2

# Container binding analysis (NEW!)
php artisan dev:container:bindings --show-resolved

# Service provider performance (NEW!)
php artisan dev:providers:timeline --slow-threshold=100
```

### Export Data

```bash
# Export all data for documentation
php artisan dev:scan --all --format=json --output=app-structure.json

# Export specific analyses
php artisan dev:models --format=json --output=models.json
php artisan dev:routes --format=json --output=routes.json
php artisan dev:container:bindings --format=json --output=bindings.json
```

### Performance Analysis (ENHANCED!)

```bash
# SQL performance analysis
php artisan dev:sql:duplicates --route=dashboard --auto-explain
php artisan dev:sql:trace --route=api.users --method=GET

# Service provider performance
php artisan dev:providers:timeline --slow-threshold=50 --show-dependencies

# Container analysis
php artisan dev:container:bindings --show-resolved --show-parameters

# Log monitoring
php artisan dev:log:tail --follow --pattern="slow|error|exception"
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

# Check for N+1 problems (NEW!)
php artisan dev:sql:duplicates --route=api.users --threshold=1
if [ $? -ne 0 ]; then
    echo "❌ N+1 problems detected in critical routes"
    exit 1
fi

# Check provider performance (NEW!)
SLOW_PROVIDERS=$(php artisan dev:providers:timeline --slow-threshold=100 --format=count | jq '.slow_count // 0')
if [ $SLOW_PROVIDERS -gt 3 ]; then
    echo "❌ Too many slow service providers: $SLOW_PROVIDERS"
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

# Enhanced application overview (NEW!)
php artisan dev:about+ --extended --performance --format=json --output=docs/generated/$DATE/about.json

# Full application scan
php artisan dev:scan --all --format=json --output=docs/generated/$DATE/full-scan.json

# Model relationships
php artisan dev:model:graph --format=mermaid --output=docs/generated/$DATE/models.mmd

# Container analysis (NEW!)
php artisan dev:container:bindings --show-resolved --format=json --output=docs/generated/$DATE/bindings.json
```

### 3. Performance Monitoring (ENHANCED!)

**SQL Performance Tracking**
```bash
#!/bin/bash
# Monitor critical endpoints for SQL performance
ROUTES=("dashboard" "api.users.index" "api.orders.index")

for route in "${ROUTES[@]}"; do
    echo "🔍 Analyzing SQL performance for $route..."
    
    # N+1 detection
    php artisan dev:sql:duplicates --route=$route --threshold=2 --output=logs/n+1-$route-$(date +%Y%m%d).json
    
    # Detailed trace
    php artisan dev:sql:trace --route=$route --output=logs/sql-$route-$(date +%Y%m%d).json
done
```

**Real-time Performance Monitoring**
```bash
#!/bin/bash
# Monitor application performance in real-time

# Service provider performance
echo "📊 Checking service provider performance..."
php artisan dev:providers:timeline --slow-threshold=50

# Container binding analysis
echo "🔍 Analyzing container bindings..."
php artisan dev:container:bindings --show-resolved

# Log monitoring for performance issues
echo "📋 Monitoring logs for performance issues..."
php artisan dev:log:tail --follow --pattern="slow|timeout|memory" --level=warning
```

**Application Health Monitoring**
```bash
#!/bin/bash
# Comprehensive weekly health check

# Enhanced application overview
php artisan dev:about+ --extended --performance --security --format=json --output=reports/health-$(date +%Y%m%d).json

# Provider performance analysis
php artisan dev:providers:timeline --include-deferred --show-dependencies --format=json --output=reports/providers-$(date +%Y%m%d).json

# Container health
php artisan dev:container:bindings --show-parameters --format=json --output=reports/container-$(date +%Y%m%d).json

# Check for issues
UNUSED_COUNT=$(php artisan dev:routes:unused --format=count | jq '.count')
SLOW_PROVIDERS=$(php artisan dev:providers:timeline --slow-threshold=100 --format=count | jq '.slow_count // 0')

echo "Health Summary - $(date)" >> reports/summary-$(date +%Y%m%d).txt
echo "Unused routes: $UNUSED_COUNT" >> reports/summary-$(date +%Y%m%d).txt
echo "Slow providers: $SLOW_PROVIDERS" >> reports/summary-$(date +%Y%m%d).txt
```

## 🛠 Automation Scripts

### Enhanced Analysis Script (NEW!)

```bash
#!/bin/bash
# Comprehensive application analysis with new features

echo "🔍 Enhanced Laravel Application Analysis..."

# Enhanced overview
php artisan dev:about+ --extended --performance --security --format=json --output=analysis/overview.json

# Container and service analysis
php artisan dev:container:bindings --show-resolved --show-parameters --format=json --output=analysis/container.json
php artisan dev:providers:timeline --include-deferred --show-dependencies --format=json --output=analysis/providers.json

# Route analysis with reverse lookups
php artisan dev:routes --format=json --output=analysis/all-routes.json
php artisan dev:routes:unused --format=json --output=analysis/unused-routes.json

# Find routes for common controllers
CONTROLLERS=("UserController" "AuthController" "ApiController")
for controller in "${CONTROLLERS[@]}"; do
    echo "📊 Finding routes for $controller..."
    php artisan dev:routes:where "$controller" --format=json --output="analysis/$controller-routes.json"
done

echo "✅ Enhanced analysis complete"
```

### Log Monitoring Script (NEW!)

```bash
#!/bin/bash
# Advanced log monitoring and analysis

echo "📋 Starting advanced log monitoring..."

# Monitor errors in real-time
echo "🚨 Monitoring error logs (press Ctrl+C to stop)..."
php artisan dev:log:tail --follow --level=error --pattern="exception|error|fatal" &
ERROR_PID=$!

# Monitor SQL slow queries
echo "🐌 Monitoring slow query logs..."
php artisan dev:log:tail --follow --pattern="slow.*query|query.*slow" &
SLOW_PID=$!

# Monitor specific patterns
echo "🔍 Monitoring application-specific patterns..."
php artisan dev:log:tail --follow --pattern="user.*login|authentication|authorization" &
AUTH_PID=$!

echo "Log monitoring started. Process IDs: $ERROR_PID, $SLOW_PID, $AUTH_PID"
echo "Use 'kill $ERROR_PID $SLOW_PID $AUTH_PID' to stop all monitors"

# Wait for user interrupt
wait
```

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

### JSON Data Processing with jq (ENHANCED!)

```bash
# Find models with many relationships
php artisan dev:models --format=json | jq '.data[] | select(.relationships | length > 5) | .class'

# Count routes by middleware
php artisan dev:routes --format=json | jq '.data | group_by(.middleware[]) | map({middleware: .[0].middleware[0], count: length})'

# Find slow SQL queries
php artisan dev:sql:trace --route=dashboard --format=json | jq '.data.queries[] | select(.time > 100)'

# Analyze container bindings by type (NEW!)
php artisan dev:container:bindings --format=json | jq '.data | group_by(.type) | map({type: .[0].type, count: length})'

# Find slow service providers (NEW!)
php artisan dev:providers:timeline --format=json | jq '.data[] | select(.boot_time > 100) | {name: .name, time: .boot_time}'

# Find N+1 problems (NEW!)
php artisan dev:sql:duplicates --route=users.index --format=json | jq '.duplicates[] | {query: .query, count: .count}'

# Find controllers with most routes (NEW!)
php artisan dev:routes --format=json | jq '.data | group_by(.controller) | map({controller: .[0].controller, route_count: length}) | sort_by(.route_count) | reverse'

# Analyze middleware usage patterns (NEW!)
php artisan dev:middlewares:where-used auth --format=json | jq '.usage | {routes: (.routes | length), groups: (.groups | length)}'
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

### Git Hooks (ENHANCED!)

**Pre-commit Hook**
```bash
#!/bin/bash
# .git/hooks/pre-commit - Enhanced checks

echo "🔍 Running enhanced pre-commit checks..."

# Check for unused routes
UNUSED=$(php artisan dev:routes:unused --format=count | jq '.count')
if [ $UNUSED -gt 20 ]; then
    echo "❌ Too many unused routes: $UNUSED"
    echo "Please clean up unused routes before committing"
    exit 1
fi

# Check for N+1 problems in critical routes (NEW!)
CRITICAL_ROUTES=("dashboard" "users.index")
for route in "${CRITICAL_ROUTES[@]}"; do
    echo "🔍 Checking N+1 problems in $route..."
    if ! php artisan dev:sql:duplicates --route=$route --threshold=1 >/dev/null 2>&1; then
        echo "⚠️ Potential N+1 problems in $route - review before committing"
    fi
done

# Check service provider performance (NEW!)
SLOW_PROVIDERS=$(php artisan dev:providers:timeline --slow-threshold=200 --format=count | jq '.slow_count // 0')
if [ $SLOW_PROVIDERS -gt 5 ]; then
    echo "❌ Too many slow service providers: $SLOW_PROVIDERS"
    echo "Please optimize provider boot times before committing"
    exit 1
fi

echo "✅ Enhanced pre-commit checks passed"
```

**Post-merge Hook**
```bash
#!/bin/bash
# .git/hooks/post-merge - Enhanced documentation update

echo "📊 Updating documentation after merge..."

# Enhanced application overview (NEW!)
php artisan dev:about+ --extended --performance --format=json --output=docs/about.json

# Update model diagrams
php artisan dev:model:graph --format=mermaid --output=docs/models.mmd

# Update route documentation
php artisan dev:routes --format=json --output=docs/routes.json

# Container bindings documentation (NEW!)
php artisan dev:container:bindings --show-resolved --format=json --output=docs/container.json

# Provider performance baseline (NEW!)
php artisan dev:providers:timeline --include-deferred --format=json --output=docs/providers.json

git add docs/
git commit -m "docs: update auto-generated documentation with enhanced data" || true
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

### Scenario 1: New Team Member Onboarding (ENHANCED!)

```bash
#!/bin/bash
# Generate comprehensive application overview for new developers

echo "📋 Generating enhanced application overview for onboarding..."

# Enhanced application overview with performance and security info (NEW!)
php artisan dev:about+ --extended --performance --security --format=json --output=onboarding/overview.json

# Application structure
php artisan dev:scan --all --format=json --output=onboarding/app-structure.json

# Visual model relationships
php artisan dev:model:graph --format=mermaid --output=onboarding/models.mmd

# Route documentation with controller mapping
php artisan dev:routes --format=json --output=onboarding/routes.json

# Service and container overview (NEW!)
php artisan dev:services --format=json --output=onboarding/services.json
php artisan dev:container:bindings --show-resolved --format=json --output=onboarding/container.json

# Provider performance baseline (NEW!)
php artisan dev:providers:timeline --include-deferred --show-dependencies --format=json --output=onboarding/providers.json

# Critical controller route mappings (NEW!)
CONTROLLERS=("UserController" "AuthController" "ApiController" "DashboardController")
mkdir -p onboarding/controllers
for controller in "${CONTROLLERS[@]}"; do
    php artisan dev:routes:where "$controller" --format=json --output="onboarding/controllers/$controller.json" 2>/dev/null || echo "Controller $controller not found"
done

echo "✅ Enhanced onboarding documentation generated in onboarding/"
echo "📁 Includes: overview, structure, models, routes, services, container, providers, and controller mappings"
```

### Scenario 2: Performance Audit (COMPREHENSIVE!)

```bash
#!/bin/bash
# Comprehensive performance audit with enhanced analysis

echo "⚡ Starting comprehensive performance audit..."
mkdir -p audit/performance

# Service provider performance analysis (NEW!)
echo "📊 Analyzing service provider performance..."
php artisan dev:providers:timeline --slow-threshold=50 --include-deferred --show-dependencies --format=json --output=audit/performance/providers.json

# Container binding performance (NEW!)
echo "🔍 Analyzing container binding performance..."
php artisan dev:container:bindings --show-resolved --show-parameters --format=json --output=audit/performance/container.json

# Critical routes SQL performance analysis (ENHANCED!)
ROUTES=("dashboard" "api.users.index" "reports.monthly" "users.index" "orders.index")
mkdir -p audit/performance/sql

for route in "${ROUTES[@]}"; do
    echo "🔍 Auditing SQL performance for $route..."
    
    # N+1 detection with auto-explain
    php artisan dev:sql:duplicates --route=$route --threshold=1 --auto-explain --format=json --output="audit/performance/sql/$route-n+1.json" 2>/dev/null
    
    # Detailed SQL trace
    php artisan dev:sql:trace --route=$route --format=json --output="audit/performance/sql/$route-trace.json" 2>/dev/null
done

# Log analysis for performance issues (NEW!)
echo "📋 Analyzing logs for performance patterns..."
php artisan dev:log:tail --lines=1000 --pattern="slow|timeout|memory|performance" --format=json > audit/performance/performance-logs.json 2>/dev/null || echo "No performance logs found"

# Generate performance summary
echo "📋 Generating performance audit summary..."
cat > audit/performance/summary.md << 'EOF'
# Performance Audit Summary

**Date:** $(date)

## Service Provider Performance
$(php artisan dev:providers:timeline --slow-threshold=100 --format=count | jq -r '"Slow providers (>100ms): " + (.slow_count // 0 | tostring)')

## Container Performance
$(php artisan dev:container:bindings --format=count | jq -r '"Total bindings: " + (.count // 0 | tostring)')

## SQL Performance Issues
- N+1 problems detected in routes (see sql/*-n+1.json files)
- Slow queries identified (see sql/*-trace.json files)

## Recommendations
- Review slow service providers for optimization opportunities
- Implement eager loading for N+1 problems
- Add database indexes for slow queries
- Consider caching for frequently accessed data

EOF

echo "✅ Comprehensive performance audit complete"
echo "📁 Results saved in audit/performance/"
```

### Scenario 3: Legacy Code Analysis (ENHANCED!)

```bash
#!/bin/bash
# Analyze legacy code for refactoring with enhanced tools

echo "🔧 Analyzing legacy code with enhanced tools..."
mkdir -p legacy

# Enhanced application overview to understand current state (NEW!)
php artisan dev:about+ --extended --performance --format=json --output=legacy/current-state.json

# Find unused routes (potential cleanup candidates)
php artisan dev:routes:unused --format=json --output=legacy/unused-routes.json

# Analyze service provider performance for legacy providers (NEW!)
php artisan dev:providers:timeline --slow-threshold=50 --show-dependencies --format=json --output=legacy/provider-performance.json

# Container binding analysis to understand dependencies (NEW!)
php artisan dev:container:bindings --show-resolved --show-parameters --format=json --output=legacy/container-analysis.json

# Find controllers and their route mappings (NEW!)
LEGACY_CONTROLLERS=("LegacyUserController" "OldOrderController" "DeprecatedProductController")
mkdir -p legacy/controllers

for controller in "${LEGACY_CONTROLLERS[@]}"; do
    echo "📊 Analyzing routes for $controller..."
    php artisan dev:routes:where "$controller" --show-methods --format=json --output="legacy/controllers/$controller-routes.json" 2>/dev/null || echo "$controller not found"
done

# Model usage analysis (enhanced to find tightly coupled models)
MODELS=("LegacyUser" "OldOrder" "DeprecatedProduct")
mkdir -p legacy/models

for model in "${MODELS[@]}"; do
    if [ -f "app/Models/$model.php" ]; then
        echo "📊 Analyzing $model usage..."
        php artisan dev:model:where-used "App\Models\$model" --format=json --output="legacy/models/$model-usage.json"
    fi
done

# Middleware usage analysis for legacy middleware (NEW!)
LEGACY_MIDDLEWARE=("LegacyAuth" "OldSession" "DeprecatedCors")
mkdir -p legacy/middleware

for middleware in "${LEGACY_MIDDLEWARE[@]}"; do
    echo "🔍 Analyzing middleware: $middleware..."
    php artisan dev:middlewares:where-used "$middleware" --show-routes --show-groups --format=json --output="legacy/middleware/$middleware-usage.json" 2>/dev/null || echo "$middleware not found"
done

# SQL performance analysis for legacy routes (NEW!)
LEGACY_ROUTES=("legacy.dashboard" "old.reports" "deprecated.api")
mkdir -p legacy/sql

for route in "${LEGACY_ROUTES[@]}"; do
    echo "🔍 Analyzing SQL performance for legacy route: $route..."
    php artisan dev:sql:duplicates --route=$route --threshold=1 --format=json --output="legacy/sql/$route-n+1.json" 2>/dev/null || echo "Route $route not found"
done

# Generate refactoring recommendations
echo "📋 Generating refactoring recommendations..."
cat > legacy/refactoring-plan.md << 'EOF'
# Legacy Code Refactoring Plan

**Generated:** $(date)

## Cleanup Opportunities

### Unused Routes
$(jq -r '.data | length' legacy/unused-routes.json 2>/dev/null || echo "0") unused routes found - candidates for removal

### Performance Issues
- Service providers with >50ms boot time (check provider-performance.json)
- N+1 problems in legacy routes (check sql/ directory)
- Complex container bindings (check container-analysis.json)

## Migration Strategy

### Phase 1: Quick Wins
1. Remove unused routes
2. Fix N+1 problems with eager loading
3. Optimize slow service providers

### Phase 2: Architecture Improvements
1. Refactor tightly coupled models
2. Modernize legacy controllers
3. Update deprecated middleware

### Phase 3: Performance Optimization
1. Review container bindings
2. Implement caching strategies
3. Database query optimization

## Next Steps
1. Review generated JSON files for detailed analysis
2. Prioritize changes based on impact and effort
3. Create feature branches for each refactoring phase

EOF

echo "✅ Enhanced legacy analysis complete"
echo "📁 Analysis results saved in legacy/ directory"
echo "📋 Review legacy/refactoring-plan.md for recommendations"
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