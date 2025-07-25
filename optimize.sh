#!/bin/bash
# NEWREPOCITY Build Optimization Script
# Script para otimização de build e performance

set -e

echo "🚀 Iniciando otimizações do NEWREPOCITY..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Check if running in project directory
if [ ! -f "package.json" ] || [ ! -f "artisan" ]; then
    print_error "Execute este script na pasta raiz do projeto Laravel!"
    exit 1
fi

# 1. Laravel optimizations
echo "📦 Otimizando Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
print_status "Cache do Laravel limpo e otimizado"

# 2. Composer optimizations
echo "🎼 Otimizando Composer..."
composer dump-autoload --optimize --no-dev
print_status "Autoloader do Composer otimizado"

# 3. NPM dependency optimization
echo "📦 Verificando e otimizando dependências NPM..."
npm audit fix --silent
print_status "Vulnerabilidades NPM corrigidas"

# 4. Build assets with optimization
echo "🏗️ Construindo assets otimizados..."
NODE_ENV=production npm run build
print_status "Assets construídos com otimizações de produção"

# 5. Image optimization (if images exist)
if command -v jpegoptim &> /dev/null && command -v optipng &> /dev/null; then
    echo "🖼️ Otimizando imagens..."
    find public -name "*.jpg" -exec jpegoptim --max=85 {} \; 2>/dev/null || true
    find public -name "*.jpeg" -exec jpegoptim --max=85 {} \; 2>/dev/null || true
    find public -name "*.png" -exec optipng -o2 {} \; 2>/dev/null || true
    print_status "Imagens otimizadas"
else
    print_warning "jpegoptim e optipng não encontrados. Instale para otimização de imagens:"
    print_warning "Ubuntu/Debian: sudo apt-get install jpegoptim optipng"
    print_warning "CentOS/RHEL: sudo yum install jpegoptim optipng"
fi

# 6. Generate sitemap (if artisan command exists)
if php artisan list | grep -q "sitemap:generate"; then
    echo "🗺️ Gerando sitemap..."
    php artisan sitemap:generate
    print_status "Sitemap gerado"
fi

# 7. Database optimization
echo "🗄️ Otimizando banco de dados..."
if php artisan list | grep -q "db:optimize"; then
    php artisan db:optimize
    print_status "Banco de dados otimizado"
else
    print_warning "Comando de otimização de DB não encontrado"
fi

# 8. Check file permissions
echo "🔐 Verificando permissões..."
chmod -R 755 storage bootstrap/cache
print_status "Permissões corrigidas"

# 9. Generate application key if not exists
if grep -q "APP_KEY=$" .env 2>/dev/null; then
    echo "🔑 Gerando chave da aplicação..."
    php artisan key:generate
    print_status "Chave da aplicação gerada"
fi

# 10. Final size report
echo "📊 Relatório de tamanhos:"
if [ -d "public/build" ]; then
    echo "Tamanho do diretório build:"
    du -sh public/build/
    echo "Assets maiores que 100KB:"
    find public/build -type f -size +100k -exec ls -lh {} \; | awk '{ print $5 " " $9 }'
fi

echo ""
echo -e "${GREEN}🎉 Otimização concluída com sucesso!${NC}"
echo ""
echo "📋 Próximos passos:"
echo "1. Teste a aplicação em ambiente de produção"
echo "2. Configure compressão gzip/brotli no servidor web"
echo "3. Configure cache de assets com headers adequados"
echo "4. Monitore performance com ferramentas como PageSpeed Insights"
echo ""
echo "⚡ Para máxima performance, considere:"
echo "- Redis para cache e sessões"
echo "- CDN para assets estáticos"  
echo "- OPcache para PHP"
echo "- Compressão de imagens WebP"