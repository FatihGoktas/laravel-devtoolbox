# Output Samples Directory

This directory contains sample outputs from Laravel Devtoolbox commands in various formats to help you understand the data structure and plan your automation.

## Available Samples

### `models.json`
**Command:** `php artisan dev:models --format=json`  
**Description:** Complete model analysis with relationships, attributes, and metadata

**Key Features:**
- Model class information and file paths
- Detailed relationship definitions (hasMany, belongsTo, etc.)
- Fillable, hidden, and cast attributes
- Query scopes and traits
- Comprehensive metadata

**Use Cases:**
- Generate model documentation
- Analyze relationship complexity
- Validate model structure
- Create architecture diagrams

### `model-relationships.mmd`
**Command:** `php artisan dev:model:graph --format=mermaid`  
**Description:** Mermaid diagram showing model relationships

**Key Features:**
- Visual representation of model connections
- Graph syntax compatible with Mermaid
- Can be rendered in documentation tools
- Shows relationship directions

**Use Cases:**
- Documentation generation
- Architecture visualization
- Onboarding new developers
- Code review preparation

### `db-column-usage.json`
**Command:** `php artisan dev:db:column-usage --format=json`  
**Description:** Database column usage analysis across the Laravel application codebase

**Key Features:**
- Column-by-column usage analysis
- Usage location tracking (files and line numbers)
- Unused column identification  
- Usage statistics and counts
- Cleanup recommendations

**Use Cases:**
- Database optimization
- Identify unused columns for cleanup
- Track column usage patterns
- Database migration planning

### `security-unprotected-routes.json`
**Command:** `php artisan dev:security:unprotected-routes --format=json`  
**Description:** Security analysis of routes lacking proper authentication middleware

**Key Features:**
- Security level classification (critical, high, medium, low)
- Detailed security issue identification
- Remediation recommendations
- Overall security scoring
- Critical route highlighting

**Use Cases:**
- Security auditing
- Compliance checking
- Vulnerability assessment
- Security monitoring

### `sql-trace.json`
**Command:** `php artisan dev:sql:trace --route=users.index`  
**Description:** Complete SQL performance analysis for a route

**Key Features:**
- Individual query details with timing
- Performance statistics and analysis
- N+1 query detection
- Slow query identification
- Memory usage tracking
- Cache hit/miss ratios

**Use Cases:**
- Performance optimization
- Query analysis
- Bottleneck identification
- Performance monitoring

## Sample Data Structure

### Models Output Structure
```json
{
  "type": "models",
  "timestamp": "2024-01-15T10:30:00.000000Z",
  "data": [
    {
      "class": "App\\Models\\User",
      "file": "app/Models/User.php",
      "relationships": {
        "posts": {
          "type": "hasMany",
          "related": "App\\Models\\Post"
        }
      },
      "attributes": ["id", "name", "email"],
      "scopes": ["active", "verified"]
    }
  ],
  "metadata": {
    "models_found": 5,
    "total_relationships": 12
  }
}
```

### SQL Trace Output Structure
```json
{
  "type": "sql-trace",
  "data": {
    "traced_target": "users.index",
    "total_queries": 8,
    "total_time": 145.67,
    "queries": [
      {
        "sql": "SELECT * FROM users WHERE active = ?",
        "bindings": [1],
        "time": 23.45,
        "type": "SELECT"
      }
    ],
    "statistics": {
      "average_time": 18.21,
      "max_time": 45.67,
      "query_types": {"SELECT": 7, "UPDATE": 1}
    },
    "performance_insights": {
      "n_plus_one_detected": true,
      "recommendations": ["Consider using eager loading"]
    }
  }
}
```

### Database Column Usage Structure
```json
{
  "command": "dev:db:column-usage",
  "data": [
    {
      "table_name": "users",
      "total_columns": 8,
      "columns": [
        {
          "column_name": "name",
          "type": "varchar(255)",
          "used": true,
          "usage_locations": ["app/Models/User.php"],
          "usage_count": 8
        }
      ],
      "unused_columns_count": 1
    }
  ],
  "summary": {
    "total_unused_columns": 3,
    "usage_percentage": 80.0
  }
}
```

### Security Analysis Structure
```json
{
  "command": "dev:security:unprotected-routes",
  "data": [
    {
      "route_name": "admin.dashboard",
      "uri": "admin/dashboard",
      "security_level": "critical",
      "protected": false,
      "issues": ["Admin endpoint without authentication"],
      "recommendations": ["Add auth middleware immediately"]
    }
  ],
  "security_summary": {
    "critical_issues": 2,
    "overall_security_score": 65,
    "security_grade": "C"
  }
}
```

### Mermaid Diagram Structure
```mermaid
graph TB
    User[User] --> Post[Post]
    Post[Post] --> Comment[Comment]
    User[User] --> Role[Role]
```

## Processing Examples

### Using jq for JSON Processing

```bash
# Extract model names
jq '.data[].class' models.json

# Find models with many relationships
jq '.data[] | select(.relationships | length > 5)' models.json

# Get slow queries
jq '.data.queries[] | select(.time > 100)' sql-trace.json

# Calculate average query time
jq '.data.statistics.average_time' sql-trace.json

# Find unused database columns
jq '.data[].columns[] | select(.used == false)' db-column-usage.json

# Get critical security issues
jq '.data[] | select(.security_level == "critical")' security-unprotected-routes.json

# Count tables with unused columns
jq '[.data[] | select(.unused_columns_count > 0)] | length' db-column-usage.json
```

### Converting to Other Formats

```bash
# Convert to CSV
jq -r '.data[] | [.class, .file, (.relationships | length)] | @csv' models.json

# Extract for Excel
jq '.data[] | {Model: .class, File: .file, Relationships: (.relationships | length)}' models.json

# Create summary report
jq '{total_models: (.data | length), avg_relationships: ([.data[].relationships | length] | add / length)}' models.json
```

### Integration with Monitoring Tools

```bash
# Prometheus metrics format
echo "laravel_models_total $(jq '.data | length' models.json)"
echo "laravel_unused_routes_total $(jq '.count' unused-routes.json)"

# InfluxDB format
echo "laravel,app=myapp models_count=$(jq '.data | length' models.json)i"

# Security metrics
echo "laravel_security_score $(jq '.security_summary.overall_security_score' security-unprotected-routes.json)"
echo "laravel_critical_security_issues $(jq '.security_summary.critical_issues' security-unprotected-routes.json)"

# Database health metrics  
echo "laravel_unused_columns $(jq '.summary.total_unused_columns' db-column-usage.json)"
echo "laravel_db_usage_percentage $(jq '.summary.usage_percentage' db-column-usage.json)"
```

## Viewing Mermaid Diagrams

To view the `.mmd` files as visual diagrams:

1. **Online:** Copy content to [Mermaid Live](https://mermaid.live/)
2. **VS Code:** Install the Mermaid Preview extension
3. **GitHub/GitLab:** Native rendering in markdown files
4. **Documentation:** Integrate with GitBook, Notion, or similar tools

## Automation Ideas

### Documentation Generation
```bash
# Auto-update architecture docs
php artisan dev:model:graph --format=mermaid --output=docs/models.mmd
php artisan dev:models --format=json | jq '.data[]' > docs/models-summary.json
```

### Performance Monitoring
```bash
# Daily performance check
php artisan dev:sql:trace --route=dashboard > performance/daily-$(date +%Y%m%d).json

# Alert on slow queries
SLOW_QUERIES=$(jq '.data.slow_queries | length' sql-trace.json)
if [ $SLOW_QUERIES -gt 0 ]; then
    echo "Alert: $SLOW_QUERIES slow queries detected"
fi
```

### Security Monitoring
```bash
# Daily security audit
php artisan dev:security:unprotected-routes --format=json > security/daily-$(date +%Y%m%d).json

# Alert on critical issues
CRITICAL_ISSUES=$(jq '.security_summary.critical_issues' security-unprotected-routes.json)
if [ $CRITICAL_ISSUES -gt 0 ]; then
    echo "ALERT: $CRITICAL_ISSUES critical security issues detected"
    # Send notification (Slack, email, etc.)
fi
```

### Database Health Monitoring
```bash
# Weekly database analysis
php artisan dev:db:column-usage --format=json > database/weekly-$(date +%Y%m%d).json

# Track database cleanliness
UNUSED_COLUMNS=$(jq '.summary.total_unused_columns' db-column-usage.json)
USAGE_PERCENTAGE=$(jq '.summary.usage_percentage' db-column-usage.json)
echo "Database health: $USAGE_PERCENTAGE% usage, $UNUSED_COLUMNS unused columns"
```

### Quality Metrics
```bash
# Track code quality over time
echo "$(date),$(jq '.data | length' models.json),$(jq '.count' unused-routes.json)" >> metrics.csv
```

These samples provide a foundation for building your own automation and monitoring solutions with Laravel Devtoolbox.