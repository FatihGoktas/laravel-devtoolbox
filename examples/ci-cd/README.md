# CI/CD Directory

This directory contains configuration templates for integrating Laravel Devtoolbox into your CI/CD pipelines.

## Available Configurations

### `github-actions-quality.yml`
**Platform:** GitHub Actions  
**Purpose:** Comprehensive quality gates and performance checks

**Features:**
- Quality gate enforcement (unused routes, model health, environment consistency)
- Security audit with critical issue detection
- Database health monitoring and column usage analysis
- Performance baseline testing
- Automated report generation
- Artifact collection
- Multi-stage pipeline (build → quality → security → performance → documentation)

**Usage:**
1. Copy to `.github/workflows/devtoolbox-quality.yml`
2. Customize route names and thresholds
3. Commit and push to trigger workflow

### `gitlab-ci-integration.yml`
**Platform:** GitLab CI  
**Purpose:** Multi-stage quality and documentation pipeline

**Features:**
- Build stage with dependency caching
- Quality gates with configurable thresholds
- Performance monitoring for critical routes
- Automated documentation generation
- Artifact management with retention policies

**Usage:**
1. Add contents to your `.gitlab-ci.yml` file
2. Configure variables and route names
3. Push to trigger pipeline

## Configuration Guide

### Quality Thresholds

Both configurations include configurable quality thresholds:

```bash
# Unused routes threshold
if [ "$UNUSED_ROUTES" -gt 10 ]; then
    echo "Too many unused routes: $UNUSED_ROUTES"
    exit 1
fi

# Model health checks
ORPHAN_MODELS=$(jq '[.data[] | select(.relationships | length == 0)] | length' models.json)

# Environment consistency
MISSING_VARS=$(jq '[.missing_in_env // []] | length' env-diff.json)

# Security thresholds
CRITICAL_SECURITY_ISSUES=$(jq '.security_summary.critical_issues // 0' security-audit.json)
SECURITY_SCORE=$(jq '.security_summary.overall_security_score // 0' security-audit.json)

# Database health
UNUSED_COLUMNS=$(jq '.summary.total_unused_columns // 0' db-analysis.json)
DB_USAGE_PERCENTAGE=$(jq '.summary.usage_percentage // 100' db-analysis.json)
```

### Customization Points

1. **Route Names:** Update `CRITICAL_ROUTES` arrays with your application's routes
2. **Thresholds:** Adjust quality gate limits based on your application size
3. **Retention:** Configure artifact retention periods
4. **Notifications:** Add Slack/email notifications for failures
5. **Environments:** Adapt for staging/production deployments

### Environment Variables

Set these in your CI/CD platform:

```env
# Optional: Custom thresholds
DEVTOOLBOX_UNUSED_ROUTES_THRESHOLD=10
DEVTOOLBOX_ORPHAN_MODELS_THRESHOLD=5

# Optional: Performance limits  
DEVTOOLBOX_SLOW_QUERY_THRESHOLD=100
DEVTOOLBOX_MAX_QUERIES_THRESHOLD=20

# Optional: Security thresholds
DEVTOOLBOX_MIN_SECURITY_SCORE=70
DEVTOOLBOX_MAX_CRITICAL_ISSUES=0

# Optional: Database health limits
DEVTOOLBOX_MAX_UNUSED_COLUMNS=20
DEVTOOLBOX_MIN_DB_USAGE_PERCENTAGE=80
```

## Integration Examples

### Quality Gates
```bash
# Fail build if too many issues
UNUSED_COUNT=$(php artisan dev:routes:unused --format=count | jq '.count')
if [ $UNUSED_COUNT -gt 10 ]; then
    exit 1
fi
```

### Security Monitoring
```bash
# Security audit gates
CRITICAL_ISSUES=$(php artisan dev:security:unprotected-routes --critical-only --format=json | jq '.security_summary.critical_issues // 0')
if [ $CRITICAL_ISSUES -gt 0 ]; then
    echo "❌ Critical security issues found"
    exit 1
fi
```

### Database Health Gates
```bash
# Database cleanup threshold
UNUSED_COLUMNS=$(php artisan dev:db:column-usage --unused-only --format=json | jq '.summary.total_unused_columns // 0')
if [ $UNUSED_COLUMNS -gt 20 ]; then
    echo "⚠️ Consider database cleanup"
fi
```

### Performance Monitoring
```bash
# Test critical routes
for route in "${CRITICAL_ROUTES[@]}"; do
    php artisan dev:sql:trace --route="$route" --output="performance/$route.json"
done
```

### Documentation Generation
```bash
# Auto-generate documentation
php artisan dev:scan --all --format=json --output=docs/structure.json
php artisan dev:model:graph --format=mermaid --output=docs/models.mmd
```

## Best Practices

1. **Start Small:** Begin with basic quality gates and expand
2. **Set Realistic Thresholds:** Base limits on your current application state
3. **Monitor Trends:** Track metrics over time, not just absolute values
4. **Fast Feedback:** Keep essential checks in early pipeline stages
5. **Artifact Management:** Use appropriate retention policies for reports

## Troubleshooting

### Common Issues

1. **Command Not Found:** Ensure Laravel Devtoolbox is installed in CI environment
2. **Permission Errors:** Check write permissions for output directories
3. **Memory Limits:** Increase PHP memory for large applications
4. **Database Issues:** Ensure test database is properly configured

### Debug Tips

```bash
# Check package installation
composer show grazulex/laravel-devtoolbox

# Verify commands are available
php artisan list dev:

# Test with verbose output
php artisan dev:scan models --format=json
```