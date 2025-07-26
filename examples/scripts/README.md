# Scripts Directory

This directory contains ready-to-use automation scripts for Laravel Devtoolbox.

## Available Scripts

### `daily-health-check.sh` (ENHANCED!)
**Purpose:** Comprehensive daily application health check with new features  
**Usage:** `./daily-health-check.sh`  
**Output:** `storage/devtoolbox/daily/`

**Features:**
- Enhanced application overview (NEW!)
- Checks for unused routes
- Analyzes model relationships
- Validates environment consistency
- Container binding analysis (NEW!)
- Service provider performance (NEW!)
- Generates summary report
- Provides recommendations

### `sql-performance-analysis.sh` (NEW!)
**Purpose:** Comprehensive SQL performance analysis and N+1 problem detection  
**Usage:** `./sql-performance-analysis.sh`  
**Output:** `storage/devtoolbox/sql-analysis/`

**Features:**
- N+1 problem detection for critical routes
- SQL duplicate query analysis
- Performance threshold monitoring
- Auto-EXPLAIN for problematic queries
- Route and URL-based analysis
- Comprehensive performance reporting
- Optimization recommendations

### `model-analysis.sh`  
**Purpose:** In-depth model and relationship analysis  
**Usage:** `./model-analysis.sh`  
**Output:** `storage/devtoolbox/models/`

**Features:**
- Comprehensive model scanning
- Relationship diagram generation
- Usage analysis for key models
- Complexity metrics
- Markdown report generation

### `security-audit.sh`
**Purpose:** Comprehensive security analysis of Laravel application  
**Usage:** `./security-audit.sh`  
**Output:** `security-audit/YYYYMMDD/`

**Features:**
- Scans for unprotected routes
- Identifies critical security issues
- Analyzes middleware configuration
- Generates security summary report
- Provides remediation recommendations
- Exit codes for CI/CD integration

### `database-analysis.sh`
**Purpose:** Database column usage analysis and optimization insights  
**Usage:** `./database-analysis.sh`  
**Output:** `database-analysis/YYYYMMDD/`

**Features:**
- Analyzes database column usage across codebase
- Identifies unused columns
- Critical table analysis
- Database health scoring
- Cleanup recommendations
- Optimization opportunities

### `performance-monitor.sh`
**Purpose:** SQL performance monitoring for critical routes  
**Usage:** `./performance-monitor.sh`  
**Output:** `storage/devtoolbox/performance/`

**Features:**
- Tests critical application routes
- SQL query analysis and timing
- Performance threshold checking
- Trend analysis
- Alert generation

## Usage Tips

1. **Make executable:** `chmod +x *.sh`
2. **Run from Laravel root:** These scripts expect to be run from your Laravel application root
3. **Configure routes:** Edit the scripts to specify your critical routes
4. **Schedule regular runs:** Add to cron for automated monitoring
5. **Review outputs:** Check generated reports regularly

## Customization

Each script can be customized by editing the configuration variables at the top:

```bash
# Example customization in performance-monitor.sh
CRITICAL_ROUTES=(
    "home"
    "dashboard" 
    "api.users.index"
    "your.custom.route"
)

SLOW_QUERY_THRESHOLD=100  # milliseconds
MAX_QUERIES_THRESHOLD=20
```

```bash
# Example customization in sql-performance-analysis.sh (NEW!)
CRITICAL_ROUTES=(
    "dashboard"
    "users.index"
    "api.orders.index"
    "your.critical.route"
)

CRITICAL_URLS=(
    "/api/users"
    "/api/orders"
    "/your/api/endpoint"
)

N_PLUS_ONE_THRESHOLD=2  # duplicates threshold
```

```bash
# Example customization in daily-health-check.sh (ENHANCED!)
# Now includes container and provider analysis
UNUSED_ROUTES_THRESHOLD=5
SLOW_PROVIDER_THRESHOLD=100  # milliseconds
ORPHAN_MODELS_THRESHOLD=0
```

## Integration

These scripts are designed to integrate with:
- **Cron jobs** for scheduled execution
- **CI/CD pipelines** for automated quality checks  
- **Monitoring systems** for alerting
- **Documentation workflows** for keeping docs up-to-date