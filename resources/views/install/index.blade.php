@extends('layouts.install')
@section('content')
    <div class="max-w-4xl mx-auto w-full py-10">
        <div class="mb-6">
            <a class="inline-block" href="{{route('index')}}">
                <x-ui.logo height="38" class="text-gray-700 dark:text-white"/>
            </a>
        </div>
        <div class="mb-6">
            <h3 class="text-3xl mb-2 font-bold text-white">Instalação Automatizada NEWREPOCITY</h3>
            <p class="text-lg text-gray-400">Sistema de instalação completo com otimizações automáticas de performance e SEO.</p>
        </div>

        <!-- System Status Card -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xl font-semibold text-white">Status do Sistema</h4>
                <button onclick="refreshSystemStatus()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-sync-alt mr-2"></i>Atualizar
                </button>
            </div>
            <div id="system-status" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="loading">Carregando status do sistema...</div>
            </div>
        </div>

        <!-- PHP Extensions -->
        <div class="mb-8">
            <h4 class="text-xl mb-4 font-semibold text-white flex items-center">
                <i class="fas fa-code mr-2 text-blue-400"></i>
                Extensões PHP Requeridas
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($requirement->extensions() as $label => $satisfied)
                    <div class="{{ $satisfied ? 'bg-green-900 border-green-500' : 'bg-red-900 border-red-500' }} border rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium {{ $satisfied ? 'text-green-100' : 'text-red-100' }}">
                                {{ $label }}
                            </span>
                            <i class="fas {{ $satisfied ? 'fa-check text-green-400' : 'fa-times text-red-400' }}"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- System Requirements -->
        <div class="mb-8">
            <h4 class="text-xl mb-4 font-semibold text-white flex items-center">
                <i class="fas fa-server mr-2 text-purple-400"></i>
                Requisitos do Sistema
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($requirement->systemRequirements() as $label => $satisfied)
                    <div class="{{ $satisfied ? 'bg-green-900 border-green-500' : 'bg-yellow-900 border-yellow-500' }} border rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium {{ $satisfied ? 'text-green-100' : 'text-yellow-100' }}">
                                {{ $label }}
                            </span>
                            <i class="fas {{ $satisfied ? 'fa-check text-green-400' : 'fa-exclamation-triangle text-yellow-400' }}"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Directory Permissions -->
        <div class="mb-8">
            <h4 class="text-xl mb-4 font-semibold text-white flex items-center">
                <i class="fas fa-folder-open mr-2 text-orange-400"></i>
                Permissões de Diretórios
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($requirement->directories() as $label => $satisfied)
                    <div class="{{ $satisfied ? 'bg-green-900 border-green-500' : 'bg-red-900 border-red-500' }} border rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium {{ $satisfied ? 'text-green-100' : 'text-red-100' }}">
                                {{ $label }}
                            </span>
                            <i class="fas {{ $satisfied ? 'fa-check text-green-400' : 'fa-times text-red-400' }}"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recommended Features -->
        <div class="mb-8">
            <h4 class="text-xl mb-4 font-semibold text-white flex items-center">
                <i class="fas fa-star mr-2 text-yellow-400"></i>
                Recursos Recomendados (Opcionais)
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($requirement->recommendedFeatures() as $label => $satisfied)
                    <div class="{{ $satisfied ? 'bg-green-900 border-green-500' : 'bg-gray-700 border-gray-600' }} border rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium {{ $satisfied ? 'text-green-100' : 'text-gray-300' }}">
                                {{ $label }}
                            </span>
                            <i class="fas {{ $satisfied ? 'fa-check text-green-400' : 'fa-minus text-gray-400' }}"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Optimization Suggestions -->
        @if(count($requirement->getOptimizationSuggestions()) > 0)
        <div class="mb-8">
            <h4 class="text-xl mb-4 font-semibold text-white flex items-center">
                <i class="fas fa-lightbulb mr-2 text-yellow-400"></i>
                Sugestões de Otimização
            </h4>
            <div class="bg-yellow-900 border border-yellow-500 rounded-lg p-4">
                <ul class="space-y-2">
                    @foreach($requirement->getOptimizationSuggestions() as $suggestion)
                        <li class="text-yellow-100 flex items-center">
                            <i class="fas fa-arrow-right mr-2 text-yellow-400"></i>
                            {{ $suggestion }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Database Detection -->
        @if(isset($detectedDb) && !empty($detectedDb))
        <div class="mb-8">
            <div class="bg-blue-900 border border-blue-500 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-blue-100 mb-2 flex items-center">
                    <i class="fas fa-database mr-2"></i>
                    Configuração de Banco Detectada
                </h4>
                <p class="text-blue-200">
                    Detectamos um servidor MySQL/MariaDB em <strong>{{ $detectedDb['host'] }}:{{ $detectedDb['port'] }}</strong>
                </p>
            </div>
        </div>
        @endif

        <!-- Continue Button -->
        <div class="text-center">
            @if($requirement->satisfied())
                <a href="{{ route('install.config') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold text-lg rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg">
                    <i class="fas fa-rocket mr-3"></i>
                    Continuar Instalação Automatizada
                </a>
                <p class="text-sm text-gray-400 mt-3">
                    A instalação será completamente automatizada com otimizações de performance e SEO
                </p>
            @else
                <div class="inline-flex items-center px-8 py-4 bg-gray-600 text-gray-300 font-bold text-lg rounded-lg cursor-not-allowed">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    Resolva os Problemas Acima Antes de Continuar
                </div>
                <p class="text-sm text-red-400 mt-3">
                    Todos os requisitos obrigatórios devem ser atendidos para prosseguir
                </p>
            @endif
        </div>
    </div>

    <script>
    function refreshSystemStatus() {
        document.getElementById('system-status').innerHTML = '<div class="loading">Atualizando status...</div>';
        
        fetch('{{ route("install.system_status") }}')
            .then(response => response.json())
            .then(data => {
                updateSystemStatus(data);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('system-status').innerHTML = '<div class="error">Erro ao carregar status</div>';
            });
    }

    function updateSystemStatus(data) {
        const statusHtml = `
            <div class="space-y-4">
                <div class="bg-gray-700 rounded p-4">
                    <h5 class="font-semibold text-white mb-2">Status Geral</h5>
                    <div class="text-sm space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-300">Requisitos Atendidos:</span>
                            <span class="${data.requirements.satisfied ? 'text-green-400' : 'text-red-400'}">
                                ${data.requirements.satisfied ? 'Sim' : 'Não'}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Otimizações Recomendadas:</span>
                            <span class="${data.requirements.all_passed ? 'text-green-400' : 'text-yellow-400'}">
                                ${data.requirements.all_passed ? 'Completas' : 'Parciais'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="bg-gray-700 rounded p-4">
                    <h5 class="font-semibold text-white mb-2">Otimizações</h5>
                    <div class="text-sm space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-300">Assets Compilados:</span>
                            <span class="${data.optimizations.build_assets ? 'text-green-400' : 'text-red-400'}">
                                ${data.optimizations.build_assets ? 'Sim' : 'Não'}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Cache Configurado:</span>
                            <span class="${data.optimizations.config_cached ? 'text-green-400' : 'text-red-400'}">
                                ${data.optimizations.config_cached ? 'Sim' : 'Não'}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Storage Linked:</span>
                            <span class="${data.optimizations.storage_linked ? 'text-green-400' : 'text-red-400'}">
                                ${data.optimizations.storage_linked ? 'Sim' : 'Não'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('system-status').innerHTML = statusHtml;
    }

    // Load system status on page load
    document.addEventListener('DOMContentLoaded', function() {
        refreshSystemStatus();
    });
    </script>

    <style>
    .loading {
        @apply text-gray-400 text-center p-4;
    }
    .error {
        @apply text-red-400 text-center p-4;
    }
    </style>
@endsection
