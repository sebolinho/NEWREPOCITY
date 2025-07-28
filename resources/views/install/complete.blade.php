@extends('layouts.install')
@section('content')

    <div class="max-w-4xl mx-auto w-full py-10">
        <div class="text-center mb-8">
            <div class="mx-auto w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-check text-3xl text-white"></i>
            </div>
            <h3 class="text-3xl font-bold text-white mb-2">
                {{__('Instalação Concluída com Sucesso!')}}
            </h3>
            <p class="text-lg text-gray-400">{{__('Sua plataforma NEWREPOCITY está pronta para uso com todas as otimizações aplicadas.')}}</p>
        </div>

        <!-- Admin Credentials -->
        <div class="bg-gradient-to-r from-blue-900 to-purple-900 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-bold text-white mb-4 flex items-center">
                <i class="fas fa-user-shield mr-3"></i>
                Credenciais do Administrador
            </h4>
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-black/20 rounded p-4">
                    <div class="text-gray-300 text-sm mb-1">Email</div>
                    <div class="text-white font-bold text-lg">admin@admin.com</div>
                </div>
                <div class="bg-black/20 rounded p-4">
                    <div class="text-gray-300 text-sm mb-1">Senha</div>
                    <div class="text-white font-bold text-lg">admin</div>
                </div>
            </div>
            <div class="mt-4 p-3 bg-yellow-900/50 border border-yellow-500 rounded">
                <p class="text-yellow-200 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Importante:</strong> Altere a senha padrão após o primeiro login para manter a segurança.
                </p>
            </div>
        </div>

        <!-- Installation Steps -->
        @if(isset($installationSteps) && count($installationSteps) > 0)
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-tasks mr-3 text-blue-400"></i>
                Passos da Instalação
            </h4>
            <div class="space-y-3">
                @foreach($installationSteps as $step)
                    <div class="flex items-center justify-between p-3 bg-gray-700 rounded">
                        <span class="text-gray-200">{{ $step['step'] }}</span>
                        <span class="flex items-center">
                            @if($step['status'] == 'completed')
                                <i class="fas fa-check text-green-400 mr-2"></i>
                                <span class="text-green-400 text-sm">Concluído</span>
                            @elseif($step['status'] == 'warning')
                                <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                                <span class="text-yellow-400 text-sm">Aviso</span>
                            @else
                                <i class="fas fa-times text-red-400 mr-2"></i>
                                <span class="text-red-400 text-sm">Erro</span>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- System Information -->
        @if(isset($systemInfo))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- System Info -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h5 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-server mr-2 text-blue-400"></i>
                    Sistema
                </h5>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">PHP:</span>
                        <span class="text-white">{{ $systemInfo['php_version'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Laravel:</span>
                        <span class="text-white">{{ $systemInfo['laravel_version'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">OS:</span>
                        <span class="text-white">{{ $systemInfo['os'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Memória:</span>
                        <span class="text-white">{{ $systemInfo['memory_limit'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Database Info -->
            @if(isset($databaseInfo))
            <div class="bg-gray-800 rounded-lg p-6">
                <h5 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-database mr-2 text-green-400"></i>
                    Banco de Dados
                </h5>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tipo:</span>
                        <span class="text-white">{{ $databaseInfo['connection'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Versão:</span>
                        <span class="text-white">{{ $databaseInfo['version'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tabelas:</span>
                        <span class="text-white">{{ $databaseInfo['tables_count'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tamanho:</span>
                        <span class="text-white">{{ $databaseInfo['size_mb'] }} MB</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Extensions -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h5 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-puzzle-piece mr-2 text-purple-400"></i>
                    Extensões
                </h5>
                <div class="space-y-2 text-sm">
                    @if(isset($systemInfo['extensions']))
                        @foreach($systemInfo['extensions'] as $ext => $status)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">{{ ucfirst($ext) }}:</span>
                            <span class="flex items-center">
                                <i class="fas {{ $status ? 'fa-check text-green-400' : 'fa-times text-red-400' }} mr-1"></i>
                                <span class="{{ $status ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $status ? 'Sim' : 'Não' }}
                                </span>
                            </span>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Optimization Status -->
        @if(isset($optimizationChecks))
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-tachometer-alt mr-3 text-orange-400"></i>
                Status das Otimizações
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($optimizationChecks as $check => $status)
                    <div class="bg-gray-700 rounded p-3 flex items-center justify-between">
                        <span class="text-gray-300 text-sm">{{ ucwords(str_replace('_', ' ', $check)) }}</span>
                        <i class="fas {{ $status ? 'fa-check text-green-400' : 'fa-times text-red-400' }}"></i>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Installation Warnings -->
        @if(isset($installationErrors) && count($installationErrors) > 0)
        <div class="bg-yellow-900 border border-yellow-500 rounded-lg p-6 mb-8">
            <h4 class="text-lg font-semibold text-yellow-100 mb-3 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Avisos da Instalação
            </h4>
            <ul class="space-y-2">
                @foreach($installationErrors as $error)
                    <li class="text-yellow-200 text-sm">• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Quick Start Guide -->
        <div class="bg-gradient-to-r from-green-900 to-blue-900 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-rocket mr-3"></i>
                Próximos Passos
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <h5 class="font-semibold text-white">Configuração Inicial:</h5>
                    <ul class="space-y-2 text-sm text-gray-200">
                        <li class="flex items-center"><i class="fas fa-check mr-2 text-green-400"></i> Acesse o painel admin</li>
                        <li class="flex items-center"><i class="fas fa-check mr-2 text-green-400"></i> Altere a senha padrão</li>
                        <li class="flex items-center"><i class="fas fa-check mr-2 text-green-400"></i> Configure TMDB API</li>
                        <li class="flex items-center"><i class="fas fa-check mr-2 text-green-400"></i> Configure provedores de streaming</li>
                    </ul>
                </div>
                <div class="space-y-3">
                    <h5 class="font-semibold text-white">Otimizações Aplicadas:</h5>
                    <ul class="space-y-2 text-sm text-gray-200">
                        <li class="flex items-center"><i class="fas fa-rocket mr-2 text-blue-400"></i> Performance automática</li>
                        <li class="flex items-center"><i class="fas fa-search mr-2 text-purple-400"></i> SEO otimizado</li>
                        <li class="flex items-center"><i class="fas fa-shield-alt mr-2 text-red-400"></i> Segurança configurada</li>
                        <li class="flex items-center"><i class="fas fa-cog mr-2 text-orange-400"></i> Cache habilitado</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Performance Tips -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-lightbulb mr-3 text-yellow-400"></i>
                Dicas de Performance
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <h5 class="font-semibold text-white mb-2">Scripts de Manutenção:</h5>
                    <div class="bg-gray-900 rounded p-3 font-mono text-gray-300">
                        <div># Otimização completa</div>
                        <div>./optimize.sh</div>
                        <div class="mt-2"># Análise de performance</div>
                        <div>./analyze.sh</div>
                    </div>
                </div>
                <div>
                    <h5 class="font-semibold text-white mb-2">Monitoramento:</h5>
                    <ul class="space-y-1 text-gray-300">
                        <li>• Configure Google Analytics</li>
                        <li>• Monitore PageSpeed Insights</li>
                        <li>• Verifique Search Console</li>
                        <li>• Execute otimizações semanalmente</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center space-y-4">
            <div class="space-x-4">
                <a href="{{ route('index') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-blue-600 text-white font-bold text-lg rounded-lg hover:from-green-700 hover:to-blue-700 transition-all duration-300 shadow-lg">
                    <i class="fas fa-home mr-3"></i>
                    Ir para o Site
                </a>
                <a href="{{ route('admin.index') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold text-lg rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg">
                    <i class="fas fa-cogs mr-3"></i>
                    Painel Admin
                </a>
            </div>
            
            <p class="text-sm text-gray-400">
                Para suporte técnico, consulte a documentação ou crie uma issue no GitHub.
            </p>
        </div>
    </div>

    <script>
    // Add some celebration animation
    document.addEventListener('DOMContentLoaded', function() {
        // Simple confetti effect would go here if needed
        console.log('🎉 NEWREPOCITY instalado com sucesso!');
    });
    </script>
@endsection
