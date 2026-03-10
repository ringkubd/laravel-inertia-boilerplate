#!/bin/bash
##############################################
# Placement Report - NPM Installation Setup
##############################################
#
# This script installs the required Chart.js
# dependency for the placement report system.
#
# Usage: bash install-placement-report-deps.sh
#

echo "=========================================="
echo "Placement Report Dependencies Installation"
echo "=========================================="
echo ""

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ Error: npm not found. Please install Node.js and npm first."
    exit 1
fi

echo "✓ npm found"
echo ""

# Install Chart.js
echo "📦 Installing Chart.js..."
npm install chart.js

if [ $? -eq 0 ]; then
    echo "✓ Chart.js installed successfully"
else
    echo "❌ Failed to install Chart.js"
    exit 1
fi

echo ""
echo "📦 Installing dependencies (npm install)..."
npm install

if [ $? -eq 0 ]; then
    echo "✓ All dependencies installed"
else
    echo "❌ Failed to install dependencies"
    exit 1
fi

echo ""
echo "🔨 Building assets..."
npm run dev

if [ $? -eq 0 ]; then
    echo "✓ Assets built successfully"
else
    echo "❌ Failed to build assets"
    exit 1
fi

echo ""
echo "=========================================="
echo "✅ Installation Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Navigate to /placement"
echo "2. Click 'View Report' button"
echo "3. Use filters and explore the report"
echo ""
echo "Documentation:"
echo "- PLACEMENT_REPORT_GUIDE.md"
echo "- PLACEMENT_REPORT_SETUP.md"
echo "- PLACEMENT_REPORT_CHECKLIST.md"
echo ""
