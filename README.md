# 🎬 Laravel Streaming Platform

A modern, high-performance streaming platform built with Laravel, Livewire, and Vite. This platform provides a Netflix-like experience for movies and TV shows with advanced SEO optimization, security features, and performance enhancements.

## ✨ Features

- 🎥 **Movie & TV Show Streaming** - Complete movie and series management
- 👥 **User Management** - Registration, authentication, and profiles
- 💬 **Comments System** - Interactive commenting with real-time updates
- 🔍 **Advanced Search** - Powerful search and filtering capabilities
- 📊 **Admin Panel** - Comprehensive administrative dashboard
- 🌐 **SEO Optimized** - Advanced SEO with Schema.org markup
- 🔒 **Security Enhanced** - Multiple security layers and protection
- ⚡ **Performance Optimized** - Optimized for PageSpeed scores
- 📱 **Responsive Design** - Mobile-first responsive design
- 🌙 **Dark Mode** - Beautiful dark theme interface

## 🚀 Quick Start

### Prerequisites

- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.3+
- Node.js 16+ & npm
- Composer
- Web server (Apache/Nginx)

### 🎯 Recommended Installation (Web Installer)

The **easiest and fastest way** to install your streaming platform:

1. **Download and extract** the platform files to your web server
2. **Set basic permissions**:
```bash
chmod -R 755 storage bootstrap/cache
chmod 644 .env
```
3. **Visit the web installer**: Navigate to `https://your-domain.com/install/index`
4. **Follow the wizard**: The installer will guide you through:
   - ✅ Requirements check (PHP extensions, permissions)
   - ⚙️ Database configuration
   - 👤 Admin account setup
   - 📧 Email configuration (optional)
   - 🚀 Automatic optimization and asset building

**That's it!** The web installer handles everything automatically:
- Database connection and migrations
- Admin user creation
- Storage setup and linking
- Asset compilation
- System optimization
- Security configuration

### 📋 What the Web Installer Does For You

The comprehensive web installer automatically:

- **🔍 Checks all requirements** - PHP extensions, directory permissions, server setup
- **🗄️ Configures database** - Tests connection, runs migrations, seeds data
- **👤 Creates admin account** - Sets up your administrator credentials
- **📧 Sets up email** - Configures SMTP settings for notifications
- **🔗 Links storage** - Creates symbolic links for file uploads
- **⚡ Builds assets** - Compiles and optimizes frontend resources
- **🚀 Runs optimization** - Caches routes, views, and configurations
- **🔒 Applies security** - Sets production environment and security headers
- **📊 Generates sitemap** - Creates SEO-optimized sitemaps

### 🎛️ Manual Installation (Advanced Users)

If you prefer manual installation or need custom configurations:

1. **Clone the repository**
```bash
git clone https://github.com/sebolinho/NEWREPOCITY.git
cd NEWREPOCITY
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure your database** in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Build assets**
```bash
npm run build
```

8. **Optimize for production**
```bash
chmod +x optimize.sh
./optimize.sh
```

## 🎯 Web Installer Features

### One-Click Setup
The web installer provides a complete setup experience:

- **Smart Requirements Detection** - Automatically detects and validates all server requirements
- **Database Auto-Configuration** - Tests connections and sets up the database seamlessly  
- **User-Friendly Interface** - Beautiful, responsive installer with progress tracking
- **Comprehensive Configuration** - Handles app settings, admin account, email, and more
- **Asset Building Integration** - Automatically builds and optimizes frontend assets
- **Security Hardening** - Applies production security settings and optimizations
- **Error Handling** - Detailed error reporting and troubleshooting guidance

### Installation Steps

1. **Requirements Check** (`/install/index`)
   - PHP 8.1+ validation
   - Extension requirements (PDO, MySQL, GD, etc.)
   - Directory permission validation
   - Server software detection
   - Optional feature detection (Redis, FFmpeg, etc.)

2. **Configuration Setup** (`/install/config`)
   - Application settings (name, URL, license)
   - Database configuration with connection testing
   - Admin account creation
   - Email SMTP configuration (optional)
   - Installation options (asset building, optimization, sample data)

3. **Automatic Installation** (Backend Process)
   - Database migration and seeding
   - Storage directory setup and linking
   - Frontend asset compilation
   - Cache and route optimization
   - Security environment configuration
   - Performance optimization scripts

4. **Completion** (`/install/complete`)
   - Installation summary and verification
   - Admin credentials display
   - Next steps guidance
   - Performance optimization tips

### 🛡️ Security Features

The installer automatically configures:
- Production environment settings
- Secure session configuration
- Security headers middleware
- File permission optimization
- Cache and optimization strategies

### 🔧 Troubleshooting

If you encounter issues with the web installer:

1. **Check Requirements**: Ensure all PHP extensions and permissions are properly set
2. **Database Access**: Verify database credentials and server accessibility
3. **File Permissions**: Ensure web server can write to storage and cache directories
4. **Error Logs**: Check `storage/logs/laravel.log` for detailed error information
5. **Manual Installation**: Fall back to manual installation if needed

## 🌐 Nginx Configuration

### Basic Nginx Configuration

Create a new site configuration in `/etc/nginx/sites-available/streaming-platform`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/streaming-platform/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/javascript
        application/xml+rss
        application/json;

    # Browser Caching
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary "Accept-Encoding";
    }

    # Main location block
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
    location /api/ {
        limit_req zone=api burst=5 nodelay;
    }
}

# HTTPS Redirect
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/streaming-platform/public;

    # SSL Configuration
    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Include the same configuration as above
    # ... (same as HTTP configuration)
}
```

### Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/streaming-platform /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## ⚡ Performance Optimization

### Automatic Optimization Script

Run the included optimization script for production:

```bash
./optimize.sh
```

This script will:
- ✅ Update and secure all dependencies
- ✅ Optimize Laravel caches and configurations
- ✅ Build and minify frontend assets
- ✅ Set proper file permissions
- ✅ Generate sitemaps
- ✅ Optimize database
- ✅ Configure security headers

### Manual Optimizations

#### Laravel Optimizations
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

#### Frontend Optimizations
```bash
# Build optimized assets
npm run build

# Analyze bundle size
npm run analyze
```

## 🔧 Development

### Development Server
```bash
# Start Laravel development server
php artisan serve

# Start Vite development server (in another terminal)
npm run dev
```

### Available Scripts

- `npm run dev` - Start development server with hot reload
- `npm run build` - Build for production
- `npm run preview` - Preview production build
- `npm run analyze` - Analyze bundle size
- `npm run optimize` - Run optimization script
- `npm run audit-fix` - Fix security vulnerabilities
- `npm run clean` - Clean install dependencies
- `npm run production` - Build and optimize for production

## 🔒 Security Features

- ✅ **Security Headers** - CSP, HSTS, X-Frame-Options, etc.
- ✅ **CSRF Protection** - Laravel's built-in CSRF protection
- ✅ **XSS Protection** - Input sanitization and output escaping
- ✅ **SQL Injection Protection** - Eloquent ORM protection
- ✅ **Rate Limiting** - API and form submission limits
- ✅ **Secure Authentication** - Laravel Sanctum
- ✅ **Input Validation** - Comprehensive validation rules

## 📈 SEO Features

- ✅ **Meta Tags** - Comprehensive meta tag management
- ✅ **Open Graph** - Facebook and social media optimization
- ✅ **Twitter Cards** - Twitter-specific meta tags
- ✅ **Schema.org** - Structured data markup
- ✅ **Sitemap** - Automatic sitemap generation
- ✅ **Canonical URLs** - Proper canonical URL handling
- ✅ **Page Speed** - Optimized for 90+ PageSpeed scores

## 🛠️ Configuration

### Environment Variables

Key environment variables to configure:

```env
# Application
APP_NAME="Your Streaming Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password

# Analytics (Optional)
GOOGLE_ANALYTICS_ID=GA-XXXXXXXX
```

### Application Settings

Configure in your admin panel:
- Site name and description
- SEO settings
- Comment moderation
- User registration settings
- Streaming server configurations

## 📊 Monitoring & Analytics

### Performance Monitoring

1. **Google PageSpeed Insights** - Monitor page speed scores
2. **GTmetrix** - Detailed performance analysis
3. **Laravel Telescope** (development) - Application debugging
4. **Server monitoring** - Monitor server resources

### Analytics Integration

- Google Analytics 4 support
- Custom event tracking
- User engagement metrics
- Content performance analytics

## 🔧 Troubleshooting

### Common Issues

#### 500 Error - Comment System
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan cache:clear
php artisan config:clear
```

#### Asset Loading Issues
```bash
# Rebuild assets
npm run build

# Check public directory permissions
chmod -R 755 public/
```

#### Database Connection Issues
```bash
# Test database connection
php artisan tinker
> DB::connection()->getPdo();
```

### Performance Issues

#### Slow Page Loading
1. Enable Redis caching
2. Optimize database queries
3. Use CDN for static assets
4. Enable compression
5. Optimize images

#### High Server Load
1. Implement queue workers
2. Enable proper caching
3. Optimize database indexes
4. Use load balancing

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests and optimization
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Laravel Framework
- Livewire
- TailwindCSS
- Alpine.js
- Vite

## 📞 Support

For support and questions:
- Create an issue on GitHub
- Check the documentation
- Review the troubleshooting section

---

**Made with ❤️ for the streaming community**