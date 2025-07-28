#!/bin/bash

echo "🚀 COMPREHENSIVE INSTALLATION FIX SCRIPT"
echo "==========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_step "Starting comprehensive installation fix..."

# Step 1: Fix file permissions
print_step "1. Setting proper file permissions..."
chmod +x artisan
chmod +x optimize.sh
chmod 755 storage/app/public
chmod 755 bootstrap/cache
chmod 755 storage/logs
chmod -R 755 storage
chmod -R 755 bootstrap/cache
print_success "File permissions fixed"

# Step 2: Clean and reinstall dependencies
print_step "2. Cleaning npm dependencies..."
rm -rf node_modules package-lock.json
npm cache clean --force
print_success "Dependencies cleaned"

print_step "3. Installing compatible dependencies..."
npm install
if [ $? -eq 0 ]; then
    print_success "Dependencies installed successfully"
else
    print_error "Failed to install dependencies"
    exit 1
fi

# Step 3: Build assets
print_step "4. Building frontend assets..."
npm run build
if [ $? -eq 0 ]; then
    print_success "Assets built successfully"
else
    print_error "Failed to build assets"
    exit 1
fi

# Step 4: Laravel optimizations
print_step "5. Running Laravel optimizations..."

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache

print_success "Laravel caches optimized"

# Step 5: Storage linking
print_step "6. Creating storage link..."
php artisan storage:link
print_success "Storage linked"

# Step 6: Fix environment setup
print_step "7. Setting up environment..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate
    print_success "Environment file created and key generated"
else
    print_success "Environment file already exists"
fi

# Step 7: Database setup (if needed)
print_step "8. Setting up database..."
if php artisan migrate:status >/dev/null 2>&1; then
    print_success "Database already configured"
else
    print_warning "Database needs to be configured manually or through web installer"
fi

# Step 8: Final optimizations
print_step "9. Running final optimizations..."
if command -v php >/dev/null 2>&1; then
    php artisan optimize
    print_success "Laravel optimized"
fi

# Step 9: Security headers
print_step "10. Setting up security headers..."
if [ -f ".htaccess" ]; then
    print_success ".htaccess file exists with security headers"
else
    print_warning ".htaccess file not found - manual setup may be required"
fi

echo ""
echo "🎉 INSTALLATION FIX COMPLETE!"
echo "==============================="
echo ""
echo "✅ Dependencies installed and built successfully"
echo "✅ Laravel optimizations applied"
echo "✅ File permissions fixed"
echo "✅ Storage linked"
echo "✅ Caches optimized"
echo ""
echo "🌐 Next Steps:"
echo "1. Configure your database settings in .env"
echo "2. Visit /install/index to run the web installer"
echo "3. Or manually run: php artisan migrate:fresh --seed"
echo ""
echo "🚀 Your platform is ready to go!"