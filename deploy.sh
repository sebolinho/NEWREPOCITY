#!/bin/bash

# Production Deployment Script for NEWREPOCITY
# Handles build with automatic permission fixes

echo "🚀 NEWREPOCITY Production Deployment Script"
echo "==========================================="

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Check if we're in the right directory
if [ ! -f "package.json" ]; then
    echo -e "${RED}❌ Error: package.json not found. Please run this script from the project root directory.${NC}"
    exit 1
fi

# Step 1: Fix permissions first
echo -e "${BLUE}🔧 Step 1: Fixing permissions...${NC}"
if [ -f "fix-permissions.sh" ]; then
    chmod +x fix-permissions.sh
    ./fix-permissions.sh
else
    echo -e "${YELLOW}⚠️  fix-permissions.sh not found, applying basic fixes...${NC}"
    chmod -R 755 node_modules/.bin/ 2>/dev/null || true
    find node_modules -name "esbuild" -exec chmod +x {} \; 2>/dev/null || true
fi

# Step 2: Clean install
echo -e "${BLUE}📦 Step 2: Installing dependencies...${NC}"
npm ci --production=false || npm install

# Step 3: Re-fix permissions after install
echo -e "${BLUE}🔄 Step 3: Re-fixing permissions after install...${NC}"
find node_modules/.bin -type f -exec chmod +x {} \; 2>/dev/null || true
find node_modules -name "esbuild" -exec chmod +x {} \; 2>/dev/null || true

# Step 4: Clear old assets
echo -e "${BLUE}🧹 Step 4: Clearing old assets...${NC}"
rm -rf public/build/* 2>/dev/null || true

# Step 5: Build production assets
echo -e "${BLUE}⚡ Step 5: Building production assets...${NC}"
npm run build

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Build completed successfully!${NC}"
else
    echo -e "${RED}❌ Build failed! Trying alternative approach...${NC}"
    
    # Alternative: reinstall everything
    echo -e "${YELLOW}🔄 Trying complete reinstall...${NC}"
    rm -rf node_modules package-lock.json
    npm install
    
    # Fix permissions again
    find node_modules/.bin -type f -exec chmod +x {} \; 2>/dev/null || true
    find node_modules -name "esbuild" -exec chmod +x {} \; 2>/dev/null || true
    
    # Try build again
    npm run build
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Build completed successfully after reinstall!${NC}"
    else
        echo -e "${RED}❌ Build still failing. Check error messages above.${NC}"
        exit 1
    fi
fi

# Step 6: Laravel optimizations
echo -e "${BLUE}🚀 Step 6: Laravel optimizations...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 7: Set proper Laravel permissions
echo -e "${BLUE}🗂️  Step 7: Setting Laravel permissions...${NC}"
chmod -R 775 storage/ bootstrap/cache/ 2>/dev/null || true
chown -R www-data:www-data storage/ bootstrap/cache/ public/build/ 2>/dev/null || true

# Step 8: Verify build
echo -e "${BLUE}🔍 Step 8: Verifying build...${NC}"
if [ -f "public/build/manifest.json" ]; then
    echo -e "${GREEN}✅ Build manifest found${NC}"
    ASSETS_COUNT=$(ls public/build/assets/ 2>/dev/null | wc -l)
    echo -e "${GREEN}✅ Assets generated: ${ASSETS_COUNT} files${NC}"
else
    echo -e "${RED}❌ Build manifest missing!${NC}"
    exit 1
fi

echo -e "\n${GREEN}🎉 Deployment completed successfully!${NC}"
echo -e "${BLUE}📊 Summary:${NC}"
echo "- Dependencies installed"
echo "- Permissions fixed"
echo "- Production assets built"
echo "- Laravel optimized"
echo "- Ready for production"

echo -e "\n${YELLOW}💡 Next steps:${NC}"
echo "1. Test your site in a browser"
echo "2. Check /install/system-status for any issues"
echo "3. Clear browser cache if assets seem outdated"

# Optional: Show asset sizes
echo -e "\n${BLUE}📁 Generated assets:${NC}"
du -sh public/build/assets/css/* 2>/dev/null || echo "No CSS files found"
du -sh public/build/assets/js/* 2>/dev/null || echo "No JS files found"