# Scripts Directory

This directory contains ready-to-use automation scripts for Laravel Devtoolbox.

## Available Scripts

### `daily-health-check.sh`
**Purpose:** Comprehensive daily application health check  
**Usage:** `./daily-health-check.sh`  
**Output:** `storage/devtoolbox/daily/`

**Features:**
- Checks for unused routes
- Analyzes model relationships
- Validates environment consistency
- Generates summary report
- Provides recommendations

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

## Integration

These scripts are designed to integrate with:
- **Cron jobs** for scheduled execution
- **CI/CD pipelines** for automated quality checks  
- **Monitoring systems** for alerting
- **Documentation workflows** for keeping docs up-to-date