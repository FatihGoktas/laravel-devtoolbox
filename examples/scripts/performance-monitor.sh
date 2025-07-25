#!/bin/bash

# Laravel Devtoolbox - Performance Monitoring Script
# Monitor SQL performance for critical application routes

echo "⚡ Laravel Devtoolbox - Performance Monitor"
echo "=========================================="
date
echo

# Configuration
OUTPUT_DIR="storage/devtoolbox/performance"
LOG_DIR="$OUTPUT_DIR/logs"
REPORTS_DIR="$OUTPUT_DIR/reports"

# Create directories
mkdir -p "$OUTPUT_DIR" "$LOG_DIR" "$REPORTS_DIR"

# Define critical routes to monitor
CRITICAL_ROUTES=(
    "home"
    "dashboard" 
    "api.users.index"
    "api.users.show"
    "api.orders.index"
    "api.orders.store"
    "reports.monthly"
    "admin.dashboard"
)

# Define critical URLs (for routes without names)
CRITICAL_URLS=(
    "/api/status"
    "/api/health"
    "/webhooks/payment"
)

# Performance thresholds
SLOW_QUERY_THRESHOLD=100  # milliseconds
MAX_QUERIES_THRESHOLD=20
TOTAL_TIME_THRESHOLD=500  # milliseconds

echo "🔍 Monitoring $(( ${#CRITICAL_ROUTES[@]} + ${#CRITICAL_URLS[@]} )) critical endpoints..."
echo

# 1. Test critical named routes
echo "📍 Testing named routes..."
for route in "${CRITICAL_ROUTES[@]}"; do
    echo "   🔎 Testing route: $route"
    
    # Skip if route doesn't exist
    if ! php artisan route:list --name="$route" &>/dev/null; then
        echo "      ⚠️  Route '$route' not found - skipping"
        continue
    fi
    
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    OUTPUT_FILE="$LOG_DIR/${route}_${TIMESTAMP}.json"
    
    # Trace SQL for the route
    if php artisan dev:sql:trace --route="$route" --output="$OUTPUT_FILE" 2>/dev/null; then
        # Analyze results
        TOTAL_QUERIES=$(jq -r '.data.total_queries // 0' "$OUTPUT_FILE")
        TOTAL_TIME=$(jq -r '.data.total_time // 0' "$OUTPUT_FILE")
        SLOW_QUERIES=$(jq -r '[.data.queries[]? | select(.time > '"$SLOW_QUERY_THRESHOLD"')] | length' "$OUTPUT_FILE")
        
        # Check thresholds
        ISSUES=()
        if (( $(echo "$TOTAL_TIME > $TOTAL_TIME_THRESHOLD" | bc -l) )); then
            ISSUES+=("slow_total_time")
        fi
        if [ "$TOTAL_QUERIES" -gt "$MAX_QUERIES_THRESHOLD" ]; then
            ISSUES+=("too_many_queries")
        fi
        if [ "$SLOW_QUERIES" -gt 0 ]; then
            ISSUES+=("slow_queries")
        fi
        
        # Report results
        if [ ${#ISSUES[@]} -gt 0 ]; then
            echo "      ❌ Issues found: ${ISSUES[*]}"
            echo "         - Total time: ${TOTAL_TIME}ms"
            echo "         - Query count: $TOTAL_QUERIES"
            echo "         - Slow queries: $SLOW_QUERIES"
        else
            echo "      ✅ Performance OK (${TOTAL_TIME}ms, $TOTAL_QUERIES queries)"
        fi
    else
        echo "      ❌ Failed to trace route"
    fi
done
echo

# 2. Test critical URLs
echo "🌐 Testing URLs..."
for url in "${CRITICAL_URLS[@]}"; do
    echo "   🔎 Testing URL: $url"
    
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    OUTPUT_FILE="$LOG_DIR/url_$(echo "$url" | tr '/' '_')_${TIMESTAMP}.json"
    
    # Trace SQL for the URL
    if php artisan dev:sql:trace --url="$url" --output="$OUTPUT_FILE" 2>/dev/null; then
        TOTAL_QUERIES=$(jq -r '.data.total_queries // 0' "$OUTPUT_FILE")
        TOTAL_TIME=$(jq -r '.data.total_time // 0' "$OUTPUT_FILE")
        
        echo "      ✅ Performance: ${TOTAL_TIME}ms, $TOTAL_QUERIES queries"
    else
        echo "      ❌ Failed to trace URL"
    fi
done
echo

# 3. Generate performance summary
echo "📊 Generating performance summary..."
SUMMARY_FILE="$REPORTS_DIR/performance_summary_$(date +%Y%m%d_%H%M%S).json"

# Collect all recent performance data
find "$LOG_DIR" -name "*.json" -mtime -1 | head -20 | while read -r file; do
    jq '{
        source: .data.traced_target // "unknown",
        total_time: .data.total_time // 0,
        total_queries: .data.total_queries // 0,
        slow_queries: [.data.queries[]? | select(.time > '"$SLOW_QUERY_THRESHOLD"')] | length,
        timestamp: .timestamp
    }' "$file"
done | jq -s '.' > "$SUMMARY_FILE"

# Calculate aggregated metrics
TOTAL_ENDPOINTS=$(jq 'length' "$SUMMARY_FILE")
SLOW_ENDPOINTS=$(jq 'map(select(.total_time > '"$TOTAL_TIME_THRESHOLD"')) | length' "$SUMMARY_FILE")
AVG_QUERIES=$(jq 'map(.total_queries) | add / length | floor' "$SUMMARY_FILE")
AVG_TIME=$(jq 'map(.total_time) | add / length | floor' "$SUMMARY_FILE")

# 4. Generate markdown report
echo "📝 Generating performance report..."
REPORT_FILE="$REPORTS_DIR/performance_report_$(date +%Y%m%d_%H%M%S).md"

cat > "$REPORT_FILE" << EOF
# Performance Monitoring Report

**Generated:** $(date)

## Summary

- **Endpoints Tested:** $TOTAL_ENDPOINTS
- **Slow Endpoints:** $SLOW_ENDPOINTS (>${TOTAL_TIME_THRESHOLD}ms)
- **Average Queries:** $AVG_QUERIES per endpoint
- **Average Response Time:** ${AVG_TIME}ms

## Performance Metrics

| Endpoint | Response Time | Query Count | Slow Queries | Status |
|----------|---------------|-------------|--------------|--------|
EOF

# Add performance data to table
jq -r '.[] | "| " + .source + " | " + (.total_time | tostring) + "ms | " + (.total_queries | tostring) + " | " + (.slow_queries | tostring) + " | " + (if .total_time > '"$TOTAL_TIME_THRESHOLD"' then "❌ Slow" elif .total_queries > '"$MAX_QUERIES_THRESHOLD"' then "⚠️ Many Queries" else "✅ OK" end) + " |"' "$SUMMARY_FILE" >> "$REPORT_FILE"

cat >> "$REPORT_FILE" << EOF

## Thresholds

- **Slow Query:** > ${SLOW_QUERY_THRESHOLD}ms
- **Too Many Queries:** > $MAX_QUERIES_THRESHOLD
- **Slow Total Time:** > ${TOTAL_TIME_THRESHOLD}ms

## Recommendations

EOF

# Add recommendations based on findings
if [ "$SLOW_ENDPOINTS" -gt 0 ]; then
    echo "- 🐌 Optimize slow endpoints (${SLOW_ENDPOINTS} found)" >> "$REPORT_FILE"
fi

if [ "$AVG_QUERIES" -gt 15 ]; then
    echo "- 🔄 Consider reducing query count (average: $AVG_QUERIES)" >> "$REPORT_FILE"
fi

# 5. Cleanup old logs (keep last 7 days)
echo "🧹 Cleaning up old logs..."
find "$LOG_DIR" -name "*.json" -mtime +7 -delete
find "$REPORTS_DIR" -name "*.json" -mtime +30 -delete
find "$REPORTS_DIR" -name "*.md" -mtime +30 -delete

echo
echo "✅ Performance monitoring complete!"
echo
echo "📁 Generated files:"
echo "   - $SUMMARY_FILE (performance summary)"
echo "   - $REPORT_FILE (detailed report)"
echo "   - $LOG_DIR/ (individual trace files)"
echo
echo "📊 Key metrics:"
echo "   - Endpoints tested: $TOTAL_ENDPOINTS"
echo "   - Slow endpoints: $SLOW_ENDPOINTS"
echo "   - Average response time: ${AVG_TIME}ms"
echo "   - Average queries: $AVG_QUERIES"

# 6. Alert if performance issues found
if [ "$SLOW_ENDPOINTS" -gt 0 ]; then
    echo
    echo "⚠️  WARNING: $SLOW_ENDPOINTS slow endpoints detected!"
    echo "   Review the performance report for details."
fi