# Laravel Devtoolbox - Ideas Implementation

## Priority Implementation Plan

### Phase 1: Core Extensions [core]
1. **dev:about+** - Extend existing about command
   - New AboutEnhancedCommand extends existing about
   - Add providers timeline, env diff, performance metrics
   - Reuse existing scanners (services, routes, etc.)

2. **dev:routes:where** - Reverse route lookup
   - New RouteWhereLookupScanner
   - Parse controllers and find matching routes
   - Integrate with existing RouteScanner

3. **dev:log:tail** - Intelligent log tailing
   - New LogTailCommand with real-time filtering
   - Use Symfony Process for tail functionality
   - Add pattern matching and highlighting

### Phase 2: Enhanced Analysis [nice]
4. **dev:container:bindings** - Extended services analysis
   - Enhance existing ServiceScanner
   - Add binding type detection (singleton, scoped, etc.)
   - Detect unused bindings

5. **dev:middlewares:where-used** - Middleware usage mapping
   - New MiddlewareUsageScanner
   - Parse routes, controllers, and global middleware
   - Show usage statistics and coverage

### Phase 3: Performance & Debugging [wow]
6. **dev:sql:duplicates** - N+1 and duplicate query detection
   - New SqlAnalysisScanner
   - Hook into DB query log during request
   - Analyze patterns and suggest optimizations

7. **dev:providers:timeline** - Service provider boot analysis
   - New ProviderTimelineScanner
   - Measure boot times and dependencies
   - Generate timeline visualization

## Implementation Strategy

### Leverage Existing Architecture
- Use current AbstractScanner pattern
- Extend DevtoolboxManager for new scanners
- Reuse JSON format infrastructure

### New Scanner Ideas
```php
// Example: RouteWhereLookupScanner
class RouteWhereLookupScanner extends AbstractScanner
{
    public function scan(array $options = []): array
    {
        $target = $options['target']; // UserController or UserController@show
        return [
            'target' => $target,
            'matching_routes' => $this->findRoutesForTarget($target),
            'methods' => $this->getControllerMethods($target),
        ];
    }
}
```

### Integration Points
- All new commands follow existing JSON format pattern
- Reuse existing progress message suppression logic
- Leverage current test architecture

## Command Signatures
```bash
# Core
dev:about --enhanced --format=json
dev:routes:where {target} --format=json
dev:log:tail --pattern= --level= --follow --format=json

# Nice  
dev:container:bindings --type= --unused --format=json
dev:middlewares:where-used {middleware?} --routes --groups --format=json

# Wow
dev:sql:duplicates --route= --threshold= --auto-explain --format=json  
dev:providers:timeline --slow-threshold= --graph --format=json
```

## Technical Considerations

### Dependencies
- Symfony Process (for log tail)
- Laravel Telescope integration (optional, for SQL analysis)
- Memory profiling extensions

### Performance
- Lazy loading for heavy scanners
- Caching for static analysis
- Async processing for real-time commands

### Testing Strategy
- Unit tests for each new scanner
- Feature tests for command integration
- Performance benchmarks for heavy operations
