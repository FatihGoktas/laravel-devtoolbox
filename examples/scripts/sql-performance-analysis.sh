#!/bin/bash

# Laravel Devtoolbox - SQL Performance Analysis
# Analyze SQL query performance and N+1 problems across critical routes

echo "⚡ Laravel Devtoolbox - SQL Performance Analysis"
echo "==============================================="
date
echo

# Create output directory
mkdir -p storage/devtoolbox/sql-analysis

# Configuration - Add your critical routes here
CRITICAL_ROUTES=(
    "dashboard"
    "users.index"
    "users.show"
    "api.orders.index"
    "reports.dashboard"
)

# Configuration - Add critical URLs for API endpoints
CRITICAL_URLS=(
    "/api/users"
    "/api/orders"
    "/api/dashboard/stats"
)

echo "🔍 Analyzing SQL performance for critical routes..."
echo

# 1. SQL Duplicates Analysis for Routes
for route in "${CRITICAL_ROUTES[@]}"; do
    echo "🔍 Analyzing route: $route"
    
    # Basic N+1 detection
    php artisan dev:sql:duplicates --route="$route" --threshold=2 --format=json --output="storage/devtoolbox/sql-analysis/${route}-duplicates.json" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        # Count duplicates found
        DUPLICATES=$(jq '[.duplicates // []] | length' "storage/devtoolbox/sql-analysis/${route}-duplicates.json" 2>/dev/null || echo "0")
        TOTAL_QUERIES=$(jq '[.queries // []] | length' "storage/devtoolbox/sql-analysis/${route}-duplicates.json" 2>/dev/null || echo "0")
        
        if [ "$DUPLICATES" -gt 0 ]; then
            echo "⚠️  Found $DUPLICATES duplicate query patterns in $TOTAL_QUERIES total queries"
        else
            echo "✅ No duplicate queries found ($TOTAL_QUERIES total queries)"
        fi
    else
        echo "❌ Failed to analyze route: $route (route may not exist)"
    fi
    echo
done

# 2. SQL Duplicates Analysis for URLs
echo "🌐 Analyzing SQL performance for critical URLs..."
echo

for url in "${CRITICAL_URLS[@]}"; do
    echo "🔍 Analyzing URL: $url"
    
    # Analyze GET requests
    php artisan dev:sql:duplicates --url="$url" --method=GET --threshold=2 --format=json --output="storage/devtoolbox/sql-analysis/$(basename $url)-get-duplicates.json" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        DUPLICATES=$(jq '[.duplicates // []] | length' "storage/devtoolbox/sql-analysis/$(basename $url)-get-duplicates.json" 2>/dev/null || echo "0")
        TOTAL_QUERIES=$(jq '[.queries // []] | length' "storage/devtoolbox/sql-analysis/$(basename $url)-get-duplicates.json" 2>/dev/null || echo "0")
        
        if [ "$DUPLICATES" -gt 0 ]; then
            echo "⚠️  Found $DUPLICATES duplicate query patterns in $TOTAL_QUERIES total queries"
        else
            echo "✅ No duplicate queries found ($TOTAL_QUERIES total queries)"
        fi
    else
        echo "❌ Failed to analyze URL: $url"
    fi
    echo
done

# 3. Detailed SQL Tracing for problematic routes
echo "🔬 Performing detailed SQL tracing..."
echo

for route in "${CRITICAL_ROUTES[@]}"; do
    echo "📊 Tracing SQL for route: $route"
    
    php artisan dev:sql:trace --route="$route" --format=json --output="storage/devtoolbox/sql-analysis/${route}-trace.json" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        QUERY_COUNT=$(jq '[.queries // []] | length' "storage/devtoolbox/sql-analysis/${route}-trace.json" 2>/dev/null || echo "0")
        SLOW_QUERIES=$(jq '[.queries[] | select(.time > 100)] | length' "storage/devtoolbox/sql-analysis/${route}-trace.json" 2>/dev/null || echo "0")
        TOTAL_TIME=$(jq '[.queries[] | .time] | add' "storage/devtoolbox/sql-analysis/${route}-trace.json" 2>/dev/null || echo "0")
        
        echo "   📈 $QUERY_COUNT queries, $SLOW_QUERIES slow (>100ms), ${TOTAL_TIME}ms total"
        
        if [ "$SLOW_QUERIES" -gt 0 ]; then
            echo "   ⚠️  Performance concern: $SLOW_QUERIES slow queries detected"
        fi
    else
        echo "   ❌ Failed to trace route: $route"
    fi
    echo
done

# 4. Generate comprehensive SQL performance report
echo "📋 Generating SQL performance report..."

# Count all issues found
TOTAL_DUPLICATES=0
TOTAL_SLOW_QUERIES=0
TOTAL_ROUTES_ANALYZED=0

for route in "${CRITICAL_ROUTES[@]}"; do
    if [ -f "storage/devtoolbox/sql-analysis/${route}-duplicates.json" ]; then
        ROUTE_DUPLICATES=$(jq '[.duplicates // []] | length' "storage/devtoolbox/sql-analysis/${route}-duplicates.json" 2>/dev/null || echo "0")
        TOTAL_DUPLICATES=$((TOTAL_DUPLICATES + ROUTE_DUPLICATES))
        TOTAL_ROUTES_ANALYZED=$((TOTAL_ROUTES_ANALYZED + 1))
    fi
    
    if [ -f "storage/devtoolbox/sql-analysis/${route}-trace.json" ]; then
        ROUTE_SLOW=$(jq '[.queries[] | select(.time > 100)] | length' "storage/devtoolbox/sql-analysis/${route}-trace.json" 2>/dev/null || echo "0")
        TOTAL_SLOW_QUERIES=$((TOTAL_SLOW_QUERIES + ROUTE_SLOW))
    fi
done

# Create detailed report
cat > storage/devtoolbox/sql-analysis/performance-report.md << EOF
# SQL Performance Analysis Report

**Date:** $(date)
**Analysis Type:** N+1 Detection & Query Performance

## Summary

- **Routes Analyzed:** $TOTAL_ROUTES_ANALYZED
- **Total Duplicate Patterns Found:** $TOTAL_DUPLICATES
- **Total Slow Queries (>100ms):** $TOTAL_SLOW_QUERIES

## Critical Routes Analysis

EOF

# Add details for each route
for route in "${CRITICAL_ROUTES[@]}"; do
    if [ -f "storage/devtoolbox/sql-analysis/${route}-duplicates.json" ]; then
        DUPLICATES=$(jq '[.duplicates // []] | length' "storage/devtoolbox/sql-analysis/${route}-duplicates.json" 2>/dev/null || echo "0")
        QUERIES=$(jq '[.queries // []] | length' "storage/devtoolbox/sql-analysis/${route}-duplicates.json" 2>/dev/null || echo "0")
        
        echo "### Route: \`$route\`" >> storage/devtoolbox/sql-analysis/performance-report.md
        echo "- **Total Queries:** $QUERIES" >> storage/devtoolbox/sql-analysis/performance-report.md
        echo "- **Duplicate Patterns:** $DUPLICATES" >> storage/devtoolbox/sql-analysis/performance-report.md
        
        if [ "$DUPLICATES" -gt 0 ]; then
            echo "- **Status:** ⚠️ N+1 Problem Detected" >> storage/devtoolbox/sql-analysis/performance-report.md
        else
            echo "- **Status:** ✅ No Issues Found" >> storage/devtoolbox/sql-analysis/performance-report.md
        fi
        echo "" >> storage/devtoolbox/sql-analysis/performance-report.md
    fi
done

# Add recommendations
cat >> storage/devtoolbox/sql-analysis/performance-report.md << EOF

## Recommendations

EOF

if [ "$TOTAL_DUPLICATES" -gt 0 ]; then
    echo "- 🔧 **N+1 Problems Found:** Review routes with duplicate queries and implement eager loading" >> storage/devtoolbox/sql-analysis/performance-report.md
fi

if [ "$TOTAL_SLOW_QUERIES" -gt 0 ]; then
    echo "- ⚡ **Slow Queries Detected:** Optimize queries taking >100ms or add database indexes" >> storage/devtoolbox/sql-analysis/performance-report.md
fi

if [ "$TOTAL_DUPLICATES" -eq 0 ] && [ "$TOTAL_SLOW_QUERIES" -eq 0 ]; then
    echo "- ✅ **All Clear:** No significant performance issues detected" >> storage/devtoolbox/sql-analysis/performance-report.md
fi

cat >> storage/devtoolbox/sql-analysis/performance-report.md << EOF

## Next Steps

1. **For N+1 Problems:**
   \`\`\`bash
   # Get detailed analysis with EXPLAIN
   php artisan dev:sql:duplicates --route=ROUTE_NAME --auto-explain
   \`\`\`

2. **For Performance Issues:**
   \`\`\`bash
   # Monitor real-time queries
   php artisan dev:log:tail --follow --pattern="database"
   \`\`\`

3. **Regular Monitoring:**
   \`\`\`bash
   # Add this to your CI/CD pipeline
   php artisan dev:sql:duplicates --route=critical-route --threshold=1
   \`\`\`

## Files Generated

EOF

# List all generated files
for file in storage/devtoolbox/sql-analysis/*.json; do
    if [ -f "$file" ]; then
        echo "- \`$(basename "$file")\`" >> storage/devtoolbox/sql-analysis/performance-report.md
    fi
done

echo "✅ SQL performance analysis complete!"
echo
echo "📊 **Results:**"
echo "   - Routes analyzed: $TOTAL_ROUTES_ANALYZED"
echo "   - Duplicate patterns found: $TOTAL_DUPLICATES"
echo "   - Slow queries found: $TOTAL_SLOW_QUERIES"
echo
echo "📁 **Detailed reports available in:**"
echo "   - storage/devtoolbox/sql-analysis/performance-report.md"
echo "   - storage/devtoolbox/sql-analysis/*-duplicates.json"
echo "   - storage/devtoolbox/sql-analysis/*-trace.json"
echo
if [ "$TOTAL_DUPLICATES" -gt 0 ] || [ "$TOTAL_SLOW_QUERIES" -gt 0 ]; then
    echo "⚠️  **Action Required:** Performance issues detected. Review the report for details."
else
    echo "✅ **All Clear:** No significant performance issues found."
fi