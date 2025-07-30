#!/bin/bash

# Fix Permissions Script for NEWREPOCITY
# Resolves permission issues commonly found on production servers

echo "🔧 Fixing permissions for NEWREPOCITY build system..."

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "package.json" ]; then
    echo -e "${RED}❌ Error: package.json not found. Please run this script from the project root directory.${NC}"
    exit 1
fi

echo -e "${BLUE}📂 Setting correct permissions for node_modules...${NC}"

# Fix node_modules permissions
if [ -d "node_modules" ]; then
    # Set directory permissions
    find node_modules -type d -exec chmod 755 {} \;
    
    # Set file permissions
    find node_modules -type f -exec chmod 644 {} \;
    
    # Set executable permissions for binary files
    find node_modules/.bin -type f -exec chmod +x {} \; 2>/dev/null || true
    find node_modules -name "*.bin" -exec chmod +x {} \; 2>/dev/null || true
    find node_modules -path "*/bin/*" -type f -exec chmod +x {} \; 2>/dev/null || true
    
    echo -e "${GREEN}✅ Node modules permissions fixed${NC}"
else
    echo -e "${YELLOW}⚠️  node_modules directory not found. Running npm install...${NC}"
    npm install
fi

# Fix specific esbuild permission issue
if [ -f "node_modules/@esbuild/linux-x64/bin/esbuild" ]; then
    chmod +x node_modules/@esbuild/linux-x64/bin/esbuild
    echo -e "${GREEN}✅ ESBuild permissions fixed${NC}"
fi

# Fix vite binary permissions
if [ -f "node_modules/.bin/vite" ]; then
    chmod +x node_modules/.bin/vite
    echo -e "${GREEN}✅ Vite binary permissions fixed${NC}"
fi

# Check if Vite config exists
if [ ! -f "vite.config.js" ]; then
    echo -e "${RED}❌ Error: vite.config.js not found${NC}"
    exit 1
fi

# Set project file permissions
echo -e "${BLUE}📁 Setting project file permissions...${NC}"
chmod +x rebuild-assets.sh 2>/dev/null || true
chmod +x optimize.sh 2>/dev/null || true
chmod +x analyze.sh 2>/dev/null || true

# Fix storage and cache permissions for Laravel
echo -e "${BLUE}🗂️  Setting Laravel permissions...${NC}"
chmod -R 775 storage/ 2>/dev/null || true
chmod -R 775 bootstrap/cache/ 2>/dev/null || true

# Clean any existing build artifacts
echo -e "${BLUE}🧹 Cleaning previous build artifacts...${NC}"
rm -rf public/build/* 2>/dev/null || true

# Test npm and node versions
echo -e "${BLUE}🔍 Checking system requirements...${NC}"
echo "Node version: $(node --version)"
echo "NPM version: $(npm --version)"

# Test if we can run vite now
echo -e "${BLUE}🧪 Testing Vite binary...${NC}"
if ./node_modules/.bin/vite --version &>/dev/null; then
    echo -e "${GREEN}✅ Vite binary is working${NC}"
else
    echo -e "${RED}❌ Vite binary still has issues. Trying alternative fix...${NC}"
    
    # Alternative fix: reinstall node_modules with correct permissions
    echo -e "${YELLOW}🔄 Reinstalling node_modules with correct permissions...${NC}"
    rm -rf node_modules package-lock.json
    npm install
    
    # Reapply permissions
    find node_modules/.bin -type f -exec chmod +x {} \; 2>/dev/null || true
    find node_modules -name "esbuild" -exec chmod +x {} \; 2>/dev/null || true
fi

echo -e "${GREEN}🎉 Permission fixes complete!${NC}"
echo -e "${BLUE}📋 Next steps:${NC}"
echo "1. Run: npm run build"
echo "2. If build fails, check the troubleshooting section in README.md"
echo "3. For persistent issues, contact support with error details"

echo -e "\n${YELLOW}💡 Tip: If you're still having issues, try running:${NC}"
echo "sudo chown -R \$(whoami) ."
echo "npm install"
echo "./fix-permissions.sh"
echo "npm run build"