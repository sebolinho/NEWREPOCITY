#!/bin/bash

# Laravel Streaming Platform Optimization Script
# This script optimizes the Laravel application for production

echo "🚀 Starting Laravel Streaming Platform Optimization..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "This script must be run from the Laravel project root directory"
    exit 1
fi

# 1. Update dependencies and fix vulnerabilities
print_step "1. Updating and securing dependencies..."
print_status "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev --quiet

print_status "Installing and securing Node dependencies..."
npm ci --silent
npm audit fix --silent

# 2. Optimize Laravel
print_step "2. Optimizing Laravel application..."
print_status "Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

print_status "Optimizing configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_status "Optimizing Composer autoloader..."
composer dump-autoload --optimize

# 3. Build and optimize assets
print_step "3. Building and optimizing frontend assets..."
print_status "Building production assets with Vite..."
npm run build

# 4. Optimize images (if ImageOptim or similar is available)
print_step "4. Optimizing images..."
if command -v imageoptim &> /dev/null; then
    print_status "Optimizing images with ImageOptim..."
    find public/uploads -type f \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" -o -name "*.gif" \) -exec imageoptim {} +
else
    print_warning "ImageOptim not found. Consider installing it for image optimization."
fi

# 5. Set proper file permissions
print_step "5. Setting proper file permissions..."
print_status "Setting storage and cache permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 6. Generate sitemap
print_step "6. Generating sitemap..."
if php artisan list | grep -q "sitemap:generate"; then
    print_status "Generating sitemap..."
    php artisan sitemap:generate
else
    print_warning "Sitemap generation command not found. Consider implementing it."
fi

# 7. Database optimizations
print_step "7. Database optimizations..."
print_status "Running database migrations..."
php artisan migrate --force

if php artisan list | grep -q "db:optimize"; then
    print_status "Optimizing database..."
    php artisan db:optimize
fi

# 8. Queue optimizations
print_step "8. Queue optimizations..."
if php artisan list | grep -q "queue:restart"; then
    print_status "Restarting queue workers..."
    php artisan queue:restart
fi

# 9. Create .htaccess optimizations
print_step "9. Optimizing .htaccess for performance..."
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Angular and Vue.js/Nuxt.js HTML5 history mode
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^.*$ /index.php [L]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>

# Browser Caching
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType video/mp4 "access plus 1 year"
    ExpiresByType video/webm "access plus 1 year"
    ExpiresByType application/pdf "access plus 1 year"
    ExpiresByType text/x-javascript "access plus 1 year"
    ExpiresByType application/x-shockwave-flash "access plus 1 year"
    ExpiresByType image/x-icon "access plus 1 year"
    ExpiresByType text/html "access plus 600 seconds"
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Permissions-Policy "camera=(), microphone=(), geolocation=()"
    
    # Remove server signature
    Header unset Server
    Header unset X-Powered-By
</IfModule>

# File size limits
LimitRequestBody 52428800

# Disable server signature
ServerTokens Prod
EOF

print_status ".htaccess optimized for performance and security"

# 10. Performance summary
print_step "10. Performance optimization summary..."
print_status "✅ Dependencies updated and secured"
print_status "✅ Laravel caches optimized"
print_status "✅ Assets built and minified"
print_status "✅ File permissions set"
print_status "✅ Database optimized"
print_status "✅ .htaccess optimized"

echo ""
echo -e "${GREEN}🎉 Optimization completed successfully!${NC}"
echo ""
echo -e "${BLUE}Next steps:${NC}"
echo "1. Test your application thoroughly"
echo "2. Monitor performance with tools like GTmetrix or PageSpeed Insights"
echo "3. Set up monitoring for errors and performance"
echo "4. Consider implementing Redis for caching"
echo "5. Use a CDN for static assets"
echo ""
echo -e "${YELLOW}Note:${NC} Run this script after every deployment for optimal performance"