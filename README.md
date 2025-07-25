# NEWREPOCITY - Laravel Streaming Platform

Uma plataforma moderna de streaming de filmes e séries desenvolvida com Laravel, otimizada para performance máxima e SEO.

## 📋 Índice

- [Características](#características)
- [Requisitos do Sistema](#requisitos-do-sistema)
- [Instalação](#instalação)
- [Configuração Nginx](#configuração-nginx)
- [Otimizações de Performance](#otimizações-de-performance)
- [SEO e Marketing](#seo-e-marketing)
- [Segurança](#segurança)
- [Manutenção](#manutenção)
- [Troubleshooting](#troubleshooting)

## 🚀 Características

- **Framework**: Laravel 10.x com Livewire 3.x
- **Frontend**: TailwindCSS, Alpine.js, Vite
- **Performance**: Otimizado para PageSpeed Insights 90+
- **SEO**: Meta tags avançadas, Schema.org, sitemaps
- **Segurança**: Headers de segurança, proteção CSRF
- **Integração**: TMDB, OneSignal, Stripe, PayPal
- **Mobile**: Responsivo e PWA ready

## 🖥️ Requisitos do Sistema

### Mínimo
- **PHP**: >= 8.1
- **MySQL/MariaDB**: >= 8.0 / >= 10.4
- **Node.js**: >= 18.x
- **Nginx**: >= 1.18
- **RAM**: 2GB
- **Storage**: 10GB

### Recomendado
- **PHP**: 8.2+ com OPcache
- **MySQL**: 8.0+ ou MariaDB 10.6+
- **Node.js**: 20.x LTS
- **Redis**: Para cache e sessões
- **RAM**: 4GB+
- **Storage**: SSD 20GB+

## 📦 Instalação

### 1. Clone o Repositório
```bash
git clone https://github.com/sebolinho/NEWREPOCITY.git
cd NEWREPOCITY
```

### 2. Instale Dependências PHP
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Instale Dependências JavaScript
```bash
npm ci
npm run build
```

### 4. Configuração do Ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure o Banco de Dados
Edite o arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=newrepocity
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 6. Execute as Migrations
```bash
php artisan migrate
php artisan db:seed
```

### 7. Configure Permissões
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Execute Script de Otimização
```bash
./optimize.sh
```

## 🌐 Configuração Nginx

### Configuração Básica

Crie o arquivo `/etc/nginx/sites-available/newrepocity`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name seudominio.com www.seudominio.com;
    root /var/www/newrepocity/public;
    index index.php index.html index.htm;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/javascript
        application/xml+rss
        application/json;

    # Brotli compression (se disponível)
    brotli on;
    brotli_comp_level 4;
    brotli_types
        text/plain
        text/css
        application/json
        application/javascript
        text/xml
        application/xml
        application/xml+rss
        text/javascript;

    # Cache estático
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|webp|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary Accept-Encoding;
        access_log off;
    }

    # Cache para build assets
    location /build/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary Accept-Encoding;
        access_log off;
    }

    # PHP handling
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { 
        access_log off; 
        log_not_found off; 
    }
    
    location = /robots.txt  { 
        access_log off; 
        log_not_found off; 
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        
        # Performance optimizations
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
        fastcgi_read_timeout 240;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~ /\.git {
        deny all;
    }

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;

    location ~ ^/(api|ajax) {
        limit_req zone=api burst=120 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ ^/(login|register) {
        limit_req zone=login burst=10 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

### Configuração SSL (Let's Encrypt)

```bash
# Instale certbot
sudo apt install certbot python3-certbot-nginx

# Obtenha certificado SSL
sudo certbot --nginx -d seudominio.com -d www.seudominio.com

# Configure renovação automática
sudo crontab -e
# Adicione: 0 12 * * * /usr/bin/certbot renew --quiet
```

### Ative o Site
```bash
sudo ln -s /etc/nginx/sites-available/newrepocity /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## ⚡ Otimizações de Performance

### 1. PHP Optimizations

#### OPcache (`/etc/php/8.2/fpm/conf.d/10-opcache.ini`)
```ini
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=12
opcache.max_accelerated_files=60000
opcache.max_wasted_percentage=10
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

#### PHP-FPM (`/etc/php/8.2/fpm/pool.d/www.conf`)
```ini
pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.process_idle_timeout = 10s
pm.max_requests = 1000
```

### 2. Redis Configuration

Instale e configure Redis:
```bash
sudo apt install redis-server
```

Configure no `.env`:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Database Optimization

#### MySQL/MariaDB (`/etc/mysql/mysql.conf.d/mysqld.cnf`)
```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
query_cache_type = 1
query_cache_size = 256M
tmp_table_size = 64M
max_heap_table_size = 64M
```

### 4. Cron Jobs

Adicione ao crontab (`crontab -e`):
```bash
# Laravel Scheduler
* * * * * cd /var/www/newrepocity && php artisan schedule:run >> /dev/null 2>&1

# Cache warm-up
0 2 * * * cd /var/www/newrepocity && php artisan optimize

# Sitemap generation
0 3 * * * cd /var/www/newrepocity && php artisan sitemap:generate

# Log cleanup
0 4 * * 0 cd /var/www/newrepocity && find storage/logs -name "*.log" -mtime +30 -delete
```

## 🔍 SEO e Marketing

### 1. Configurações Básicas

Configure no painel admin:
- **Site Title**: Título otimizado para SEO
- **Meta Description**: Descrição atrativa (150-160 caracteres)
- **Keywords**: Palavras-chave relevantes
- **Google Analytics**: ID de tracking
- **Google Search Console**: Verificação

### 2. Schema.org Markup

O sistema inclui automaticamente:
- Organization markup
- WebSite markup
- Movie/TV Show markup
- BreadcrumbList markup

### 3. Sitemap XML

```bash
# Gerar sitemap
php artisan sitemap:generate

# URLs incluídas:
# - /sitemap.xml (índice principal)
# - /sitemap_main.xml (páginas principais)
# - /sitemap_post_{page}.xml (filmes/séries)
# - /sitemap_episode_{page}.xml (episódios)
# - /sitemap_people_{page}.xml (pessoas)
# - /sitemap_genre_{page}.xml (gêneros)
```

### 4. Open Graph e Twitter Cards

Configuração automática para:
- Facebook sharing
- Twitter cards
- WhatsApp previews
- Discord embeds

## 🔒 Segurança

### 1. Headers de Segurança (já incluídos)
- X-Frame-Options
- X-Content-Type-Options
- X-XSS-Protection
- Content-Security-Policy
- Referrer-Policy

### 2. Rate Limiting
- API: 60 requests/minute
- Login: 5 attempts/minute
- General: configurável

### 3. Firewall (UFW)
```bash
sudo ufw enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

### 4. Fail2Ban
```bash
sudo apt install fail2ban
```

Configuração (`/etc/fail2ban/jail.local`):
```ini
[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log
maxretry = 10
findtime = 600
bantime = 7200
```

## 🛠️ Manutenção

### Script de Backup Diário
```bash
#!/bin/bash
# /usr/local/bin/backup-newrepocity.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/newrepocity"
DB_NAME="newrepocity"

# Criar diretório de backup
mkdir -p $BACKUP_DIR

# Backup do banco de dados
mysqldump -u root -p$DB_PASSWORD $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup dos arquivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/newrepocity \
  --exclude=/var/www/newrepocity/storage/logs \
  --exclude=/var/www/newrepocity/node_modules

# Limpar backups antigos (manter 7 dias)
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

### Monitoramento
```bash
# Verificar status dos serviços
sudo systemctl status nginx php8.2-fpm mysql redis

# Verificar logs
tail -f /var/log/nginx/error.log
tail -f /var/www/newrepocity/storage/logs/laravel.log

# Verificar performance
htop
iotop
mysql -e "SHOW PROCESSLIST;"
```

## 🐛 Troubleshooting

### Problemas Comuns

#### 1. Erro 500 - Internal Server Error
```bash
# Verificar logs
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# Verificar permissões
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Limpar cache
php artisan optimize:clear
```

#### 2. Problemas de Build
```bash
# Limpar cache npm
rm -rf node_modules package-lock.json
npm install
npm run build

# Verificar versões
node --version
npm --version
```

#### 3. Problemas de Database
```bash
# Verificar conexão
php artisan tinker
DB::connection()->getPdo();

# Executar migrations
php artisan migrate:status
php artisan migrate
```

#### 4. Performance Issues
```bash
# Verificar queries lentas
mysql -e "SHOW VARIABLES LIKE 'slow_query_log%';"

# Verificar cache Redis
redis-cli ping
redis-cli info memory

# Verificar PHP processes
ps aux | grep php-fpm
```

### Logs Importantes
- **Laravel**: `storage/logs/laravel.log`
- **Nginx Access**: `/var/log/nginx/access.log`
- **Nginx Error**: `/var/log/nginx/error.log`
- **PHP-FPM**: `/var/log/php8.2-fpm.log`
- **MySQL**: `/var/log/mysql/error.log`

## 📊 Métricas de Performance

### Objetivos PageSpeed Insights
- **Mobile**: 85+ pontos
- **Desktop**: 90+ pontos
- **First Contentful Paint**: < 1.8s
- **Largest Contentful Paint**: < 2.5s
- **Cumulative Layout Shift**: < 0.1

### Ferramentas de Teste
- [Google PageSpeed Insights](https://pagespeed.web.dev/)
- [GTmetrix](https://gtmetrix.com/)
- [WebPageTest](https://www.webpagetest.org/)
- [Lighthouse CI](https://github.com/GoogleChrome/lighthouse-ci)

## 📞 Suporte

Para suporte técnico:
1. Verifique a documentação
2. Consulte os logs de erro
3. Execute o script de otimização
4. Crie uma issue no GitHub

---

**Versão**: 1.0.3  
**Última atualização**: Dezembro 2024  
**Autor**: NEWREPOCITY Team