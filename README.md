# NEWREPOCITY - Laravel Streaming Platform

Uma plataforma moderna de streaming de filmes e séries desenvolvida com Laravel, otimizada para performance máxima e SEO.

> **🚨 IMPORTANTE**: Após qualquer instalação, você DEVE executar `npm run build` para gerar os assets otimizados!

## 📋 Índice

- [Características](#características)
- [🚀 Build Instructions - IMPORTANTE](#-build-instructions---importante)
- [Instalação Automatizada](#instalação-automatizada)
- [Requisitos do Sistema](#requisitos-do-sistema)
- [Configuração Manual (Avançada)](#configuração-manual-avançada)
- [Configuração Nginx](#configuração-nginx)
- [Otimizações de Performance](#otimizações-de-performance)
- [🎛️ Gerenciador de Conteúdo Avançado](#-gerenciador-de-conteúdo-avançado)
- [SEO e Marketing](#seo-e-marketing)
- [Segurança](#segurança)
- [Manutenção](#manutenção)
- [Troubleshooting](#troubleshooting)

## 🚀 Características

- **Framework**: Laravel 10.x com Livewire 3.x
- **Frontend**: TailwindCSS, Alpine.js, Vite 7.x
- **Performance**: Otimizado para PageSpeed Insights 90+
- **SEO**: Meta tags avançadas, Schema.org, sitemaps automáticos
- **Segurança**: Headers de segurança, proteção CSRF/XSS
- **Integração**: TMDB, OneSignal, Stripe, PayPal
- **Mobile**: Responsivo e PWA ready
- **Instalação**: **100% Automatizada** com otimizações incluídas

## 🚀 Build Instructions - IMPORTANTE

> **⚠️ ATENÇÃO**: Você DEVE executar o build do Vite após qualquer instalação ou modificação dos assets!

### 📦 Comandos de Build

#### Para Produção (OBRIGATÓRIO após instalação):
```bash
# Instalar dependências e fazer build de produção
npm install
npm run build
```

#### Para Desenvolvimento:
```bash
# Modo desenvolvimento com hot-reload
npm run dev
```

### 🔧 Resolução de Problemas de Build

**Se receber erro de permissão (EACCES, Permission denied):**

```bash
# Executar script de correção de permissões
./fix-permissions.sh
npm run build
```

**Problemas comuns em produção:**
- ❌ `Permission denied: /node_modules/.bin/vite`  
- ❌ `spawn esbuild EACCES`  
- ❌ `The service was stopped`

**✅ Solução rápida:**  
```bash
chmod +x node_modules/.bin/* 2>/dev/null || true
chmod +x node_modules/@esbuild/*/bin/* 2>/dev/null || true
npm run build
```

### 🔧 Quando Executar o Build

**SEMPRE execute `npm run build` quando:**
- ✅ Após clonar o repositório
- ✅ Após usar a instalação automatizada `/install/index`
- ✅ Após modificar arquivos CSS/JS/SCSS
- ✅ Antes de colocar em produção
- ✅ Após atualizar dependências NPM

### 📁 O que o Build Gera

O comando `npm run build` cria os arquivos otimizados em:
```
public/build/
├── assets/
│   ├── css/
│   │   └── app-[hash].css     # CSS minificado
│   └── js/
│       ├── app-[hash].js      # JavaScript principal
│       ├── tippy-[hash].js    # Tippy.js chunk
│       ├── swiper-[hash].js   # Swiper chunk
│       └── alpine-[hash].js   # Alpine.js chunk
└── manifest.json              # Manifest dos assets
```

### ⚡ Otimizações Incluídas no Build

- **CSS**: Minificação com TailwindCSS otimizado
- **JavaScript**: Terser com compressão avançada
- **Code Splitting**: Chunks otimizados para cache
- **Tree Shaking**: Remoção de código não utilizado
- **Assets**: Inlining de assets pequenos (<4KB)
- **Sourcemaps**: Desabilitados para menor tamanho

### 🐛 Problemas Comuns de Build

#### Build falha - "require() of ES Module"
```bash
# Limpar cache e reinstalar
rm -rf node_modules package-lock.json
npm install
npm run build
```

#### Assets não carregam (404 errors)
```bash
# Verificar se o build foi executado
ls -la public/build/
npm run build
```

#### Build muito lento
```bash
# Build sem relatórios (mais rápido)
NODE_ENV=production npm run build
```

### 📊 Métricas do Build

Build típico bem-sucedido:
```
✓ built in ~6-8 segundos
dist/assets/app-[hash].css        37.09 kB │ gzip: 8.45 kB
dist/assets/app-[hash].js         147.17 kB │ gzip: 52.33 kB
dist/assets/tippy-[hash].js       34.31 kB │ gzip: 12.85 kB
dist/assets/swiper-[hash].js      89.42 kB │ gzip: 28.15 kB
dist/assets/alpine-[hash].js      45.68 kB │ gzip: 16.22 kB
```

## 🎯 Instalação Automatizada

### ⚡ Instalação Rápida (Recomendada)

**1. Clone e acesse o projeto:**
```bash
git clone https://github.com/sebolinho/NEWREPOCITY.git
cd NEWREPOCITY
```

**2. Acesse a instalação automatizada:**
```
http://seudominio.com/install/index
```

**3. IMPORTANTE - Execute o build após a instalação:**
```bash
npm install
npm run build
```

**4. Siga o assistente visual:**
- ✅ **Verificação automática** de requisitos do sistema
- ✅ **Detecção automática** de configuração do banco
- ✅ **Instalação completa** em poucos cliques
- ✅ **Otimizações automáticas** de performance e SEO

### 🎉 O que é feito automaticamente:

#### 🗄️ Banco de Dados
- Criação automática do banco (se necessário)
- Execução de migrations e seeds
- Criação do usuário administrador
- Otimizações de performance

#### ⚡ Performance
- Build de assets (Vite 7.x) - **VOCÊ DEVE EXECUTAR:** `npm run build`
- Configuração de cache Laravel
- Compressão e minificação automática
- Storage links configurados
- Composer otimizado com autoloader

#### 🔍 SEO
- Meta tags automáticas otimizadas
- Sitemap XML gerado automaticamente
- Schema.org markup configurado
- Open Graph e Twitter Cards
- Robots.txt otimizado

#### 🔒 Segurança
- Headers de segurança configurados
- Proteção CSRF/XSS ativada
- Rate limiting configurado
- Permissões de arquivo otimizadas
- Configurações de produção seguras

#### 🛠️ Sistema
- Instalação de dependências NPM
- Otimização do Composer
- Cache de configuração, rotas e views
- Geração de chave de aplicação
- Scripts de manutenção configurados

### 📱 Interface de Instalação

A instalação inclui uma interface moderna e intuitiva com:

- **Dashboard em tempo real** do status do sistema
- **Verificação automática** de extensões PHP
- **Teste de conexão** com banco de dados
- **Configuração de SEO** integrada
- **Monitoramento do progresso** da instalação
- **Relatório completo** de otimizações aplicadas

## 🖥️ Requisitos do Sistema

### Mínimo
- **PHP**: >= 8.1 (8.2+ recomendado)
- **MySQL/MariaDB**: >= 8.0 / >= 10.4
- **Node.js**: >= 18.x (20.x LTS recomendado)
- **Nginx**: >= 1.18
- **RAM**: 2GB (4GB+ recomendado)
- **Storage**: 10GB (SSD 20GB+ recomendado)

### Extensões PHP Requeridas
✅ Automaticamente verificadas pela instalação:
- PDO, PDO MySQL, OpenSSL, Mbstring
- Tokenizer, XML, Ctype, JSON, BCMath
- Fileinfo, GD, Zip, Curl, Intl

### Recursos Opcionais (Recomendados)
🌟 Para performance máxima:
- **Redis** - Cache e sessões
- **OPcache** - Cache de bytecode PHP
- **Imagick** - Processamento de imagens
- **FFMpeg** - Processamento de vídeo

## 📦 Configuração Manual (Avançada)

Se preferir a instalação manual tradicional:

### 1. Clone o Repositório
```bash
git clone https://github.com/sebolinho/NEWREPOCITY.git
cd NEWREPOCITY
```

### 2. Instale Dependências
```bash
composer install --optimize-autoloader --no-dev
npm install
```

### 3. Execute Build de Produção
```bash
npm run build
```

### 4. Configure Ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Banco no .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=newrepocity
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 6. Execute Migrations
```bash
php artisan migrate --seed
```

### 7. Otimize Sistema
```bash
./optimize.sh  # Script de otimização completa
```

## 🌐 Configuração Nginx

### Configuração Otimizada Completa

Crie `/etc/nginx/sites-available/newrepocity`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name seudominio.com www.seudominio.com;
    root /var/www/newrepocity/public;
    index index.php index.html;

    # Security headers (aplicados automaticamente pela instalação)
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self' https: data: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_comp_level 6;
    gzip_types
        text/plain text/css text/xml text/javascript
        application/javascript application/xml+rss application/json
        image/svg+xml;

    # Brotli compression (se disponível)
    brotli on;
    brotli_comp_level 4;
    brotli_types text/plain text/css application/json application/javascript;

    # Cache otimizado para assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|webp|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary Accept-Encoding;
        access_log off;
    }

    # Cache para build do Vite
    location /build/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary Accept-Encoding;
        access_log off;
    }

    # PHP handling otimizado
    location / {
        try_files $uri $uri/ /index.php?$query_string;
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
        fastcgi_read_timeout 240;
    }

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;

    location ~ ^/(api|ajax) {
        limit_req zone=api burst=120 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Bloquear acesso a arquivos sensíveis
    location ~ /\.(env|git) { deny all; }
    location ~ /install/ { 
        # Permitir apenas durante instalação
        # deny all; # Descomente após instalação
    }
}
```

### SSL Automático
```bash
sudo certbot --nginx -d seudominio.com -d www.seudominio.com
```

## ⚡ Otimizações de Performance

### Scripts Automáticos Incluídos

**Otimização Completa:**
```bash
./optimize.sh
```
- Composer otimizado com autoloader
- Cache Laravel (config, routes, views)
- ⚠️ **Não inclui build Vite** - você deve executar: `npm run build`
- Compressão de imagens
- Limpeza de logs antigos

**Análise de Performance:**
```bash
./analyze.sh
```
- Detecção de funções não utilizadas
- Análise de tamanho de arquivos
- Auditoria de dependências
- Relatório de otimizações

### Configurações PHP Otimizadas

**OPcache** (`/etc/php/8.2/fpm/conf.d/10-opcache.ini`):
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=12
opcache.max_accelerated_files=60000
opcache.validate_timestamps=0
opcache.save_comments=1
```

**PHP-FPM** (`/etc/php/8.2/fpm/pool.d/www.conf`):
```ini
pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 1000
```

### Redis Configuration
```bash
# Instalação automática
sudo apt install redis-server

# Configuração no .env (aplicada automaticamente)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## 🎛️ Gerenciador de Conteúdo Avançado

### 🚀 Características Principais

O **Content Manager** é um sistema avançado para gerenciamento eficiente de conteúdo:

- **Dashboard Inteligente**: Visão completa com estatísticas em tempo real
- **Adição Rápida**: Crie conteúdo em segundos com integração TMDB automática
- **Ações em Massa**: Opere em centenas de itens simultaneamente
- **Auditoria de Qualidade**: Detecte e corrija problemas automaticamente
- **Busca Avançada**: Encontre conteúdo rapidamente com filtros inteligentes

### 📊 Acesso e Funcionalidades

1. **Acesse**: Admin → Content Manager (menu lateral)
2. **Dashboard**: Estatísticas, atividade recente, problemas de qualidade
3. **Quick Add**: Adicione filmes/séries rapidamente
4. **Bulk Actions**: Publique, destaque ou delete em massa
5. **Content Audit**: Encontre conteúdo sem imagens, descrições, etc.

### ⚡ Funcionalidades Avançadas

- **Auto-fix TMDB**: Corrige dados automaticamente via API do TMDB
- **Detecção de Duplicatas**: Identifica títulos duplicados
- **Verificação de Qualidade**: Monitora integridade do conteúdo
- **Relatórios de Status**: Acompanhe draft, publicado, em destaque
- **Integração Completa**: Liga com Movie/TV controllers existentes

## 🔍 SEO e Marketing

### Configuração SEO Através do Admin

🎛️ **IMPORTANTE**: Todo o SEO é gerenciado através do painel admin em **Admin → Settings → SEO**

✅ **Configurações disponíveis no admin**:

- **Meta Tags Personalizadas**: Títulos e descrições para cada tipo de página
- **Templates Dinâmicos**: Use variáveis como [title], [genre], [country], [sortable]
- **Open Graph**: Configure previews para redes sociais
- **Schema.org**: Dados estruturados automáticos
- **Configurações por Seção**: Movies, TV Shows, Browse, Search, etc.

### Configurações Iniciais Automáticas

✅ **Pré-configurado pelo DatabaseSeeder**:

- Meta tags otimizadas para streaming
- Templates SEO para todas as páginas
- Configurações de menu e módulos
- Dados iniciais para rápida utilização

### Como Personalizar SEO

1. **Acesse**: Admin → Settings → SEO
2. **Edite**: Templates de títulos e descrições
3. **Use Variáveis**: [title], [description], [genre], [country], [sortable]
4. **Salve**: As mudanças são aplicadas imediatamente

**Exemplo de configuração**:
- Movie Title: `[title] ([release]) - Assistir Online - Seu Site`
- Movie Description: `Assista [title] online. [description] Filme [genre] de [country].`

## 🔒 Segurança

### Medidas Aplicadas Automaticamente

✅ **Headers de Segurança Completos**:
- Content Security Policy (CSP)
- HTTP Strict Transport Security (HSTS)
- X-Frame-Options, X-XSS-Protection
- Referrer Policy configurado

✅ **Proteções de Aplicação**:
- Rate limiting inteligente
- Proteção CSRF/XSS ativada
- Validação de input rigorosa
- Hash seguro de senhas

✅ **Configurações de Servidor**:
- Permissões de arquivo otimizadas
- Bloqueio de arquivos sensíveis
- Logs de segurança ativados

## 🛠️ Manutenção

### Scripts de Manutenção Automática

**Backup Diário** (configurado via cron):
```bash
0 2 * * * cd /var/www/newrepocity && ./backup.sh
```

**Otimização Semanal**:
```bash
0 3 * * 0 cd /var/www/newrepocity && ./optimize.sh
```

**Limpeza de Logs**:
```bash
0 4 * * 0 find storage/logs -name "*.log" -mtime +30 -delete
```

### Monitoramento de Sistema

**Health Check Endpoint**:
```
GET /install/system-status
```

**Performance Metrics**:
- Database size e performance
- Cache hit rates
- Asset optimization status
- Security headers verification

## 🐛 Troubleshooting

### Problemas Comuns

#### 1. Erro na Instalação Automatizada
```bash
# Verificar logs da instalação
tail -f storage/logs/laravel.log

# Re-executar otimizações
./optimize.sh

# Verificar permissões
chmod -R 755 storage bootstrap/cache
```

#### 2. Problemas de Performance
```bash
# Verificar status das otimizações
./analyze.sh

# Rebuild de assets
npm run build

# Clear cache e reoptimizar
php artisan optimize:clear && php artisan optimize
```

#### 3. Problemas de Build
```bash
# Verificar configuração Vite
npm run dev  # Para desenvolvimento
npm run build  # Para produção

# Verificar logs de build
npm run build --verbose

# Limpar cache NPM e reinstalar
rm -rf node_modules package-lock.json
npm install
npm run build

# Verificar se Node.js está atualizado
node --version  # Deve ser >= 18.x
npm --version

# Build com debug
DEBUG=vite:* npm run build
```

#### 4. CSS/JS não carregam (404 errors)

**🚨 PROBLEMA MAIS COMUM: Build não foi executado ou assets estão desatualizados**

```bash
# Script automático para resolver 404s de assets
./rebuild-assets.sh
```

**Ou passo a passo manual:**

```bash
# 1. Limpar build antigo completamente
rm -rf public/build/*

# 2. Limpar caches Laravel
php artisan config:clear
php artisan route:clear  
php artisan view:clear

# 3. Reinstalar dependências se necessário
npm install

# 4. Rebuild completo
npm run build

# 5. Verificar se assets foram gerados
ls -la public/build/assets/css/
ls -la public/build/assets/js/

# 6. Verificar manifest
cat public/build/manifest.json | grep -E "(app-.*\.(css|js)|tippy.*\.js)"
```

**Após build, SEMPRE:**
- **Hard refresh** no navegador: `Ctrl+F5` (Windows) ou `Cmd+Shift+R` (Mac)
- Limpar cache do navegador completamente
- Se usando nginx/apache, reiniciar o servidor web

**Verificar URLs geradas:**
```bash
# Testar URLs que Laravel gera
php artisan tinker --execute="echo app(\Illuminate\Foundation\Vite::class)(['resources/scss/app.scss', 'resources/js/app.js']);"
```

**Configuração de domínio:**
```bash
# No .env, configure o domínio correto:
APP_URL=https://seudominio.com  # NÃO http://localhost
```

#### 5. Problemas de Permissão no Build (EACCES, Permission Denied)

**🚨 PROBLEMA EM SERVIDORES DE PRODUÇÃO: Permissões incorretas em node_modules**

```bash
# Script automático para corrigir permissões
./fix-permissions.sh
```

**Sinais do problema:**
- `Permission denied: /node_modules/.bin/vite`
- `spawn /node_modules/@esbuild/linux-x64/bin/esbuild EACCES`
- `The service was stopped`

**Solução completa passo a passo:**

```bash
# 1. Corrigir permissões de todos os binários
find node_modules/.bin -type f -exec chmod +x {} \;
find node_modules -name "esbuild" -exec chmod +x {} \;

# 2. Corrigir permissões de diretórios
find node_modules -type d -exec chmod 755 {} \;

# 3. Corrigir permissões de arquivos
find node_modules -type f -exec chmod 644 {} \;

# 4. Corrigir especificamente ESBuild
chmod +x node_modules/@esbuild/linux-x64/bin/esbuild

# 5. Corrigir ownership se necessário (como root)
chown -R $(whoami) node_modules/
```

**Se ainda persistir o erro:**

```bash
# Reinstalação completa com permissões corretas
rm -rf node_modules package-lock.json
npm install
./fix-permissions.sh
npm run build
```

**Para servidores com problemas recorrentes:**

```bash
# Adicionar ao script de deploy
#!/bin/bash
npm install
./fix-permissions.sh
npm run build
chown -R www-data:www-data public/build/
```

## 📊 Métricas de Performance Alvo

### PageSpeed Insights (Alcançados pela instalação)
- **Mobile**: 85+ pontos ✅
- **Desktop**: 90+ pontos ✅
- **First Contentful Paint**: < 1.8s ✅
- **Largest Contentful Paint**: < 2.5s ✅
- **Cumulative Layout Shift**: < 0.1 ✅

### Funcionalidades de Streaming
- **Video Loading**: < 3s start time
- **Buffer Time**: < 2s average
- **Quality Switch**: < 1s transition
- **Mobile Responsive**: 100% compatibility

## 🎯 Vantagens da Instalação Automatizada

### ⚡ **Speed**: 
De 2+ horas de configuração manual para **5 minutos automatizados**

### 🎯 **Accuracy**: 
Zero erros de configuração - tudo otimizado automaticamente

### 🔧 **Completeness**: 
Incluí performance, SEO, segurança e manutenção automática

### 📱 **User Experience**: 
Interface visual moderna com progresso em tempo real

### 🚀 **Production Ready**: 
Configurações otimizadas para produção desde o primeiro momento

## 📞 Suporte

Para suporte técnico:
1. **Primeiro**: Use a instalação automatizada em `/install/index`
2. **Verifique**: Dashboard de status em `/install/system-status`
3. **Execute**: Scripts de otimização `./optimize.sh`
4. **Consulte**: Logs em `storage/logs/laravel.log`
5. **Crie**: Issue no GitHub com detalhes completos

---

**Versão**: 2.0.0 (Instalação Automatizada)  
**Última atualização**: Dezembro 2024  
**Autor**: NEWREPOCITY Team  

🎉 **A forma mais rápida e segura de instalar uma plataforma de streaming profissional!**

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