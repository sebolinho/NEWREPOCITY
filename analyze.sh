#!/bin/bash
# NEWREPOCITY Performance Analysis Script
# Script para análise de performance e identificação de funções não utilizadas

set -e

echo "🔍 Iniciando análise de performance do NEWREPOCITY..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Check if running in project directory
if [ ! -f "package.json" ] || [ ! -f "artisan" ]; then
    print_error "Execute este script na pasta raiz do projeto Laravel!"
    exit 1
fi

# Create analysis directory
mkdir -p /tmp/newrepocity-analysis
ANALYSIS_DIR="/tmp/newrepocity-analysis"

echo "📊 1. Análise de Código PHP..."

# Find unused methods (basic analysis)
echo "🔍 Procurando métodos potencialmente não utilizados..."
find app/ -name "*.php" -exec grep -l "public function" {} \; | while read file; do
    grep -o "public function [a-zA-Z_][a-zA-Z0-9_]*" "$file" | while read func; do
        method_name=$(echo "$func" | sed 's/public function //')
        if [ "$method_name" != "__construct" ] && [ "$method_name" != "index" ] && [ "$method_name" != "create" ] && [ "$method_name" != "store" ] && [ "$method_name" != "show" ] && [ "$method_name" != "edit" ] && [ "$method_name" != "update" ] && [ "$method_name" != "destroy" ]; then
            usage_count=$(grep -r "$method_name" app/ resources/ routes/ --include="*.php" --include="*.blade.php" | wc -l)
            if [ "$usage_count" -le 2 ]; then
                echo "⚠️  Método possivelmente não utilizado: $method_name em $file (usado $usage_count vezes)"
            fi
        fi
    done
done > "$ANALYSIS_DIR/unused_methods.txt"

print_status "Análise de métodos não utilizados salva em $ANALYSIS_DIR/unused_methods.txt"

echo "📈 2. Análise de Performance de Queries..."

# Find N+1 query problems (basic patterns)
echo "🔍 Procurando possíveis problemas de N+1 queries..."
grep -r "foreach.*->" app/ --include="*.php" | grep -v "->get()" | grep -v "->first()" > "$ANALYSIS_DIR/potential_n1_queries.txt" || true

# Find missing eager loading
grep -r "->with(" app/ --include="*.php" > "$ANALYSIS_DIR/eager_loading_usage.txt" || true

print_status "Análise de queries salva em $ANALYSIS_DIR/potential_n1_queries.txt"

echo "📁 3. Análise de Assets e Arquivos..."

# Large files analysis
echo "🔍 Analisando arquivos grandes..."
find public/ -type f -size +500k -exec ls -lh {} \; | sort -k5 -h > "$ANALYSIS_DIR/large_files.txt" 2>/dev/null || true

# Image optimization opportunities
echo "🖼️ Verificando oportunidades de otimização de imagens..."
find public/ -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" -o -name "*.gif" | wc -l > "$ANALYSIS_DIR/total_images.txt"
find public/ -name "*.webp" | wc -l > "$ANALYSIS_DIR/webp_images.txt"

print_status "Análise de arquivos salva em $ANALYSIS_DIR/"

echo "🔧 4. Análise de Dependências..."

# NPM dependency analysis
echo "🔍 Analisando dependências NPM..."
npm ls --depth=0 > "$ANALYSIS_DIR/npm_dependencies.txt" 2>/dev/null || true
npm outdated > "$ANALYSIS_DIR/npm_outdated.txt" 2>/dev/null || true

# Composer dependency analysis
echo "🔍 Analisando dependências Composer..."
composer show > "$ANALYSIS_DIR/composer_dependencies.txt" 2>/dev/null || true
composer outdated > "$ANALYSIS_DIR/composer_outdated.txt" 2>/dev/null || true

print_status "Análise de dependências concluída"

echo "⚡ 5. Análise de Performance do Build..."

# Build size analysis
if [ -d "public/build" ]; then
    echo "🔍 Analisando tamanhos do build..."
    du -sh public/build/* | sort -h > "$ANALYSIS_DIR/build_sizes.txt"
    
    # CSS analysis
    find public/build -name "*.css" -exec wc -c {} \; | sort -n > "$ANALYSIS_DIR/css_sizes.txt"
    
    # JS analysis
    find public/build -name "*.js" -exec wc -c {} \; | sort -n > "$ANALYSIS_DIR/js_sizes.txt"
    
    print_status "Análise de build salva"
else
    print_warning "Diretório build não encontrado. Execute 'npm run build' primeiro."
fi

echo "🏗️ 6. Análise de Estrutura do Código..."

# Count lines of code
echo "🔍 Contando linhas de código..."
find app/ -name "*.php" | xargs wc -l | tail -1 > "$ANALYSIS_DIR/php_lines.txt"
find resources/js -name "*.js" | xargs wc -l | tail -1 > "$ANALYSIS_DIR/js_lines.txt" 2>/dev/null || echo "0 total" > "$ANALYSIS_DIR/js_lines.txt"
find resources/scss -name "*.scss" | xargs wc -l | tail -1 > "$ANALYSIS_DIR/scss_lines.txt" 2>/dev/null || echo "0 total" > "$ANALYSIS_DIR/scss_lines.txt"

# Controller complexity
echo "🔍 Analisando complexidade dos controllers..."
find app/Http/Controllers -name "*.php" -exec wc -l {} \; | sort -n > "$ANALYSIS_DIR/controller_complexity.txt"

print_status "Análise de estrutura concluída"

echo "🚀 7. Recomendações de Otimização..."

# Generate recommendations
cat > "$ANALYSIS_DIR/recommendations.md" << 'EOF'
# Recomendações de Otimização - NEWREPOCITY

## 🎯 Prioridade Alta

### 1. Otimização de Imagens
- [ ] Converter imagens para formato WebP
- [ ] Implementar lazy loading para imagens
- [ ] Comprimir imagens existentes
- [ ] Usar responsive images com srcset

### 2. Otimização de CSS/JS
- [ ] Remover CSS não utilizado
- [ ] Implementar code splitting
- [ ] Usar compressão gzip/brotli
- [ ] Minificar assets

### 3. Otimização de Database
- [ ] Adicionar índices necessários
- [ ] Implementar eager loading
- [ ] Resolver queries N+1
- [ ] Usar cache para queries frequentes

## 🎯 Prioridade Média

### 4. Performance do Servidor
- [ ] Configurar OPcache
- [ ] Implementar cache Redis
- [ ] Otimizar configuração PHP-FPM
- [ ] Configurar HTTP/2

### 5. SEO e Estrutura
- [ ] Implementar structured data
- [ ] Otimizar meta tags
- [ ] Melhorar Core Web Vitals
- [ ] Adicionar sitemap XML

## 🎯 Prioridade Baixa

### 6. Código e Manutenção
- [ ] Remover código não utilizado
- [ ] Atualizar dependências
- [ ] Implementar testes automatizados
- [ ] Documentar APIs

### 7. Monitoring
- [ ] Implementar APM
- [ ] Configurar alertas
- [ ] Monitorar performance
- [ ] Logs estruturados
EOF

print_status "Recomendações geradas em $ANALYSIS_DIR/recommendations.md"

echo "📋 8. Relatório Final..."

# Generate final report
cat > "$ANALYSIS_DIR/performance_report.txt" << EOF
RELATÓRIO DE ANÁLISE DE PERFORMANCE - NEWREPOCITY
Data: $(date)

=== RESUMO ===
EOF

# Add PHP lines count
echo "Linhas de código PHP: $(cat $ANALYSIS_DIR/php_lines.txt)" >> "$ANALYSIS_DIR/performance_report.txt"
echo "Linhas de código JS: $(cat $ANALYSIS_DIR/js_lines.txt)" >> "$ANALYSIS_DIR/performance_report.txt"
echo "Linhas de código SCSS: $(cat $ANALYSIS_DIR/scss_lines.txt)" >> "$ANALYSIS_DIR/performance_report.txt"

# Add image stats
echo "Total de imagens: $(cat $ANALYSIS_DIR/total_images.txt)" >> "$ANALYSIS_DIR/performance_report.txt"
echo "Imagens WebP: $(cat $ANALYSIS_DIR/webp_images.txt)" >> "$ANALYSIS_DIR/performance_report.txt"

# Add potential issues count
UNUSED_METHODS_COUNT=$(wc -l < "$ANALYSIS_DIR/unused_methods.txt")
echo "Métodos potencialmente não utilizados: $UNUSED_METHODS_COUNT" >> "$ANALYSIS_DIR/performance_report.txt"

echo ""
echo -e "${GREEN}🎉 Análise concluída!${NC}"
echo ""
echo "📁 Relatórios salvos em: $ANALYSIS_DIR/"
echo ""
echo "📊 Principais arquivos:"
echo "  - performance_report.txt (resumo geral)"
echo "  - recommendations.md (recomendações)"
echo "  - unused_methods.txt (métodos não utilizados)"
echo "  - large_files.txt (arquivos grandes)"
echo ""
echo "💡 Próximos passos:"
echo "1. Revisar métodos não utilizados"
echo "2. Otimizar arquivos grandes"
echo "3. Implementar recomendações de alta prioridade"
echo "4. Configurar monitoramento contínuo"
echo ""
echo "⚡ Para aplicar otimizações automáticas:"
echo "   ./optimize.sh"