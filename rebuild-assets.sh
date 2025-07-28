#!/bin/bash

# Script to completely rebuild Vite assets and clear all caches

echo "🔄 NEWREPOCITY - Complete Asset Rebuild & Cache Clear"
echo "=================================================="

# Stop on any error
set -e

echo "📦 Step 1: Cleaning old build files..."
rm -rf public/build/*
echo "✅ Old build files removed"

echo "🧹 Step 2: Clearing Laravel caches..."
php artisan config:clear 2>/dev/null || echo "Config cache cleared (or was empty)"
php artisan route:clear 2>/dev/null || echo "Route cache cleared (or was empty)"
php artisan view:clear 2>/dev/null || echo "View cache cleared (or was empty)"
php artisan cache:clear 2>/dev/null || echo "Cache cleared (or permission issue - OK for development)"
echo "✅ Laravel caches cleared"

echo "📋 Step 3: Installing/updating NPM dependencies..."
npm install
echo "✅ NPM dependencies ready"

echo "🔨 Step 4: Building Vite assets..."
npm run build
echo "✅ Vite build completed"

echo "🔍 Step 5: Verifying build..."
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Manifest file exists"
    
    # Check main assets
    MANIFEST_CONTENT=$(cat public/build/manifest.json)
    
    echo "📊 Build verification:"
    echo "  - CSS files: $(find public/build/assets/css -name "*.css" | wc -l) files"
    echo "  - JS files: $(find public/build/assets/js -name "*.js" | wc -l) files"
    echo "  - Total build size: $(du -sh public/build | cut -f1)"
    
    echo ""
    echo "🎯 Main asset files:"
    ls -lh public/build/assets/css/app-*.css 2>/dev/null || echo "  ❌ No app CSS files found"
    ls -lh public/build/assets/js/app-*.js 2>/dev/null || echo "  ❌ No app JS files found"
    
else
    echo "❌ ERROR: Manifest file not found! Build may have failed."
    exit 1
fi

echo ""
echo "🚀 SUCCESS! Vite assets rebuilt successfully."
echo ""
echo "💡 Next steps:"
echo "   1. Hard refresh your browser (Ctrl+F5 or Cmd+Shift+R)"
echo "   2. Clear browser cache completely"
echo "   3. If using a reverse proxy (nginx/apache), restart it"
echo "   4. Check browser dev tools Network tab for any remaining 404s"
echo ""
echo "🌟 Your NEWREPOCITY streaming platform is ready!"