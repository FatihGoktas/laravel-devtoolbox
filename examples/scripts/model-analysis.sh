#!/bin/bash

# Laravel Devtoolbox - Model Analysis Script
# Comprehensive analysis of Eloquent models and their relationships

echo "📊 Laravel Devtoolbox - Model Analysis"
echo "======================================"
date
echo

# Create output directory
mkdir -p storage/devtoolbox/models
OUTPUT_DIR="storage/devtoolbox/models"

# 1. Basic model scan
echo "🔍 Scanning all models..."
php artisan dev:models --format=json --output="$OUTPUT_DIR/all-models.json"
MODEL_COUNT=$(jq '.data | length' "$OUTPUT_DIR/all-models.json")
echo "📄 Found $MODEL_COUNT models"
echo

# 2. Generate relationship graph
echo "🕸️ Generating relationship diagram..."
php artisan dev:model:graph --format=mermaid --output="$OUTPUT_DIR/relationships.mmd"
echo "✅ Relationship diagram saved to $OUTPUT_DIR/relationships.mmd"
echo

# 3. Analyze specific high-usage models
echo "🔎 Analyzing key models usage..."
KEY_MODELS=("User" "Post" "Order" "Product" "Category")

for model in "${KEY_MODELS[@]}"; do
    if jq -e ".data[] | select(.class | endswith(\"\\$model\"))" "$OUTPUT_DIR/all-models.json" > /dev/null; then
        echo "   📋 Analyzing $model model usage..."
        php artisan dev:model:where-used "App\\Models\\$model" --format=json --output="$OUTPUT_DIR/$model-usage.json" 2>/dev/null || {
            echo "   ⚠️  Could not analyze $model (model might not exist)"
        }
    else
        echo "   ⏭️  Skipping $model (not found)"
    fi
done
echo

# 4. Find models without relationships
echo "🔍 Finding isolated models..."
jq '[.data[] | select(.relationships | length == 0) | .class] | length' "$OUTPUT_DIR/all-models.json" > "$OUTPUT_DIR/isolated-count.txt"
ISOLATED_COUNT=$(cat "$OUTPUT_DIR/isolated-count.txt")

if [ "$ISOLATED_COUNT" -gt 0 ]; then
    echo "⚠️  Found $ISOLATED_COUNT models without relationships:"
    jq -r '.data[] | select(.relationships | length == 0) | "   - " + .class' "$OUTPUT_DIR/all-models.json"
    jq '[.data[] | select(.relationships | length == 0)]' "$OUTPUT_DIR/all-models.json" > "$OUTPUT_DIR/isolated-models.json"
else
    echo "✅ All models have relationships"
fi
echo

# 5. Find models with many relationships (potential complexity)
echo "🕸️ Finding highly connected models..."
jq '[.data[] | select(.relationships | length > 5) | {class: .class, relationship_count: (.relationships | length)}] | sort_by(.relationship_count) | reverse' "$OUTPUT_DIR/all-models.json" > "$OUTPUT_DIR/complex-models.json"

COMPLEX_COUNT=$(jq 'length' "$OUTPUT_DIR/complex-models.json")
if [ "$COMPLEX_COUNT" -gt 0 ]; then
    echo "🔗 Found $COMPLEX_COUNT highly connected models:"
    jq -r '.[] | "   - " + .class + " (" + (.relationship_count | tostring) + " relationships)"' "$OUTPUT_DIR/complex-models.json"
else
    echo "✅ No overly complex models found"
fi
echo

# 6. Generate model statistics
echo "📊 Generating model statistics..."
cat > "$OUTPUT_DIR/statistics.json" << EOF
{
    "total_models": $(jq '.data | length' "$OUTPUT_DIR/all-models.json"),
    "isolated_models": $ISOLATED_COUNT,
    "complex_models": $COMPLEX_COUNT,
    "average_relationships": $(jq '[.data[].relationships | length] | add / length | floor' "$OUTPUT_DIR/all-models.json"),
    "max_relationships": $(jq '[.data[].relationships | length] | max' "$OUTPUT_DIR/all-models.json"),
    "scan_date": "$(date -u +%Y-%m-%dT%H:%M:%SZ)"
}
EOF

# 7. Generate markdown report
echo "📝 Generating analysis report..."
cat > "$OUTPUT_DIR/analysis-report.md" << EOF
# Model Analysis Report

**Generated:** $(date)

## Overview

- **Total Models:** $(jq -r '.total_models' "$OUTPUT_DIR/statistics.json")
- **Isolated Models:** $(jq -r '.isolated_models' "$OUTPUT_DIR/statistics.json") (no relationships)
- **Complex Models:** $(jq -r '.complex_models' "$OUTPUT_DIR/statistics.json") (5+ relationships)
- **Average Relationships:** $(jq -r '.average_relationships' "$OUTPUT_DIR/statistics.json") per model
- **Max Relationships:** $(jq -r '.max_relationships' "$OUTPUT_DIR/statistics.json") in a single model

## Model Relationship Diagram

\`\`\`mermaid
$(cat "$OUTPUT_DIR/relationships.mmd")
\`\`\`

## Highly Connected Models

These models have many relationships and might benefit from review:

$(if [ "$COMPLEX_COUNT" -gt 0 ]; then jq -r '.[] | "- **" + .class + "**: " + (.relationship_count | tostring) + " relationships"' "$OUTPUT_DIR/complex-models.json"; else echo "None found."; fi)

## Isolated Models

These models have no relationships and might need attention:

$(if [ "$ISOLATED_COUNT" -gt 0 ]; then jq -r '.[] | "- " + .class' "$OUTPUT_DIR/isolated-models.json"; else echo "None found."; fi)

## Recommendations

EOF

# Add recommendations based on analysis
if [ "$ISOLATED_COUNT" -gt 0 ]; then
    echo "- 🔗 Review isolated models - they might be incomplete or unused" >> "$OUTPUT_DIR/analysis-report.md"
fi

if [ "$COMPLEX_COUNT" -gt 0 ]; then
    echo "- 🧩 Consider refactoring highly connected models to reduce complexity" >> "$OUTPUT_DIR/analysis-report.md"
fi

TOTAL_MODELS=$(jq -r '.total_models' "$OUTPUT_DIR/statistics.json")
if [ "$TOTAL_MODELS" -gt 50 ]; then
    echo "- 📂 Large number of models - consider organizing into subdirectories" >> "$OUTPUT_DIR/analysis-report.md"
fi

# 8. Create interactive HTML report (if possible)
if command -v pandoc &> /dev/null; then
    echo "📄 Generating HTML report..."
    pandoc "$OUTPUT_DIR/analysis-report.md" -o "$OUTPUT_DIR/analysis-report.html" --standalone --css=https://cdn.jsdelivr.net/npm/github-markdown-css@5/github-markdown.min.css
    echo "✅ HTML report generated: $OUTPUT_DIR/analysis-report.html"
fi

echo
echo "✅ Model analysis complete!"
echo
echo "📁 Generated files:"
echo "   - $OUTPUT_DIR/all-models.json (complete model data)"
echo "   - $OUTPUT_DIR/relationships.mmd (Mermaid diagram)"
echo "   - $OUTPUT_DIR/statistics.json (summary statistics)"
echo "   - $OUTPUT_DIR/analysis-report.md (comprehensive report)"
if [ -f "$OUTPUT_DIR/analysis-report.html" ]; then
    echo "   - $OUTPUT_DIR/analysis-report.html (interactive report)"
fi
echo "   - $OUTPUT_DIR/*-usage.json (individual model usage)"
echo
echo "🔗 To view the relationship diagram:"
echo "   Copy the contents of $OUTPUT_DIR/relationships.mmd"
echo "   Paste into: https://mermaid.live/"