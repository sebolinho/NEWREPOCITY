@extends('layouts.install')
@section('content')
    <div class="max-w-4xl mx-auto w-full py-10">
        <div class="mb-6">
            <a class="inline-block" href="{{route('index')}}">
                <x-ui.logo height="38" class="text-gray-700 dark:text-white"/>
            </a>
        </div>
        <div class="mb-8">
            <h3 class="text-3xl mb-2 font-bold text-white">Configuração Automatizada</h3>
            <p class="text-lg text-gray-400">Configure sua plataforma de streaming com otimizações automáticas de performance.</p>
            <div class="bg-blue-900/30 border border-blue-500/50 rounded-lg p-4 mt-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-400 mr-3"></i>
                    <div>
                        <p class="text-blue-200 font-medium">Configurações SEO</p>
                        <p class="text-blue-300 text-sm">Todas as configurações SEO serão gerenciadas via <strong>Admin → Settings → SEO</strong> após a instalação.</p>
                    </div>
                </div>
            </div>
        </div>

        @if (session()->has('error'))
            <div class="bg-red-900 border border-red-500 text-red-100 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session()->get('error') }}
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('install.store') }}" class="space-y-8" id="install-form">
            @csrf
            
            <!-- Database Configuration -->
            <div class="bg-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-2xl font-semibold text-white flex items-center">
                        <i class="fas fa-database mr-3 text-blue-400"></i>
                        Configuração do Banco de Dados
                    </h4>
                    <button type="button" onclick="detectDatabase()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-search mr-2"></i>Auto-detectar
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-form.label for="license_key" :value="__('Chave de Licença')" class="text-white"/>
                        <x-form.input id="license_key" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                                      type="text" name="license_key" value="{{ old('license_key')}}"
                                      placeholder="{{__('Chave de Licença (Opcional)')}}"/>
                        <x-form.error :messages="$errors->get('license_key')" class="mt-2"/>
                        <div class="text-xs text-gray-400 mt-1">Válida para usuários registrados em codelug.com ou escreva "codester" para usuários do codester</div>
                    </div>

                    <div>
                        <x-form.label for="host" :value="__('Host do Banco')" class="text-white"/>
                        <x-form.input id="host" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                                      type="text" name="host" value="{{ old('host', 'localhost')}}"
                                      required placeholder="localhost"/>
                        <x-form.error :messages="$errors->get('host')" class="mt-2"/>
                    </div>

                    <div>
                        <x-form.label for="port" :value="__('Porta do Banco')" class="text-white"/>
                        <x-form.input id="port" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                                      type="text" name="port" value="{{ old('port', '3306') }}"
                                      placeholder="3306"/>
                        <x-form.error :messages="$errors->get('port')" class="mt-2"/>
                    </div>

                    <div>
                        <x-form.label for="name" :value="__('Nome do Banco')" class="text-white"/>
                        <x-form.input id="name" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                                      type="text" name="name" value="{{ old('name')}}"
                                      required placeholder="newrepocity"/>
                        <x-form.error :messages="$errors->get('name')" class="mt-2"/>
                    </div>

                    <div>
                        <x-form.label for="username" :value="__('Usuário do Banco')" class="text-white"/>
                        <x-form.input id="username" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                                      type="text" name="username" value="{{ old('username')}}"
                                      required placeholder="root"/>
                        <x-form.error :messages="$errors->get('username')" class="mt-2"/>
                    </div>

                    <div>
                        <x-form.label for="password" :value="__('Senha do Banco')" class="text-white"/>
                        <x-form.input id="password" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                                      type="password" name="password" value="{{ old('password') }}"
                                      placeholder="{{__('Senha (opcional)')}}"/>
                        <x-form.error :messages="$errors->get('password')" class="mt-2"/>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" onclick="testDatabaseConnection()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plug mr-2"></i>Testar Conexão
                    </button>
                    <div id="db-test-result" class="mt-2"></div>
                </div>
            </div>

            <!-- Site Configuration -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h4 class="text-2xl font-semibold text-white mb-6 flex items-center">
                    <i class="fas fa-globe mr-3 text-green-400"></i>
                    Configuração do Site
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-form.label for="site_name" :value="__('Nome do Site')" class="text-white"/>
                        <x-form.input id="site_name" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                                      type="text" name="site_name" value="{{ old('site_name', 'NEWREPOCITY Streaming')}}"
                                      required placeholder="NEWREPOCITY Streaming"/>
                        <x-form.error :messages="$errors->get('site_name')" class="mt-2"/>
                    </div>

                    <div>
                        <x-form.label for="site_url" :value="__('URL do Site')" class="text-white"/>
                        <x-form.input id="site_url" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                                      type="url" name="site_url" value="{{ old('site_url', config('app.url'))}}"
                                      placeholder="https://seudominio.com"/>
                        <x-form.error :messages="$errors->get('site_url')" class="mt-2"/>
                    </div>

                    <div>
                        <x-form.label for="admin_email" :value="__('Email do Administrador')" class="text-white"/>
                        <x-form.input id="admin_email" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                                      type="email" name="admin_email" value="{{ old('admin_email', 'admin@admin.com')}}"
                                      required placeholder="admin@seudominio.com"/>
                        <x-form.error :messages="$errors->get('admin_email')" class="mt-2"/>
                    </div>

                    <div>
                        <x-form.label for="admin_password" :value="__('Senha do Administrador')" class="text-white"/>
                        <x-form.input id="admin_password" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                                      type="password" name="admin_password" value="{{ old('admin_password')}}"
                                      placeholder="Deixe vazio para usar 'admin'"/>
                        <x-form.error :messages="$errors->get('admin_password')" class="mt-2"/>
                        <div class="text-xs text-gray-400 mt-1">Se deixar vazio, a senha padrão será "admin"</div>
                    </div>

                    <div>
                        <x-form.label for="timezone" :value="__('Fuso Horário')" class="text-white"/>
                        <select id="timezone" name="timezone" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white rounded-md">
                            <option value="America/Sao_Paulo" {{ old('timezone') == 'America/Sao_Paulo' ? 'selected' : '' }}>São Paulo (UTC-3)</option>
                            <option value="America/Rio_Branco" {{ old('timezone') == 'America/Rio_Branco' ? 'selected' : '' }}>Rio Branco (UTC-5)</option>
                            <option value="America/Manaus" {{ old('timezone') == 'America/Manaus' ? 'selected' : '' }}>Manaus (UTC-4)</option>
                            <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                        <x-form.error :messages="$errors->get('timezone')" class="mt-2"/>
                    </div>

                    <div>
                        <x-form.label for="locale" :value="__('Idioma')" class="text-white"/>
                        <select id="locale" name="locale" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white rounded-md">
                            <option value="pt_BR" {{ old('locale') == 'pt_BR' ? 'selected' : '' }}>Português (Brasil)</option>
                            <option value="en" {{ old('locale') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ old('locale') == 'es' ? 'selected' : '' }}>Español</option>
                        </select>
                        <x-form.error :messages="$errors->get('locale')" class="mt-2"/>
                    </div>
                </div>
            </div>

            <!-- Installation Features -->
            <div class="bg-gradient-to-r from-blue-900 to-purple-900 rounded-lg p-6">
                <h4 class="text-2xl font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-magic mr-3 text-yellow-400"></i>
                    O que será instalado automaticamente
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-black/20 rounded p-4">
                        <h5 class="font-semibold text-white mb-2 flex items-center">
                            <i class="fas fa-database mr-2 text-blue-400"></i>Banco de Dados
                        </h5>
                        <ul class="text-sm text-gray-300 space-y-1">
                            <li>• Criação automática do banco</li>
                            <li>• Migrations e seeds</li>
                            <li>• Usuário administrador</li>
                            <li>• Configurações otimizadas</li>
                        </ul>
                    </div>

                    <div class="bg-black/20 rounded p-4">
                        <h5 class="font-semibold text-white mb-2 flex items-center">
                            <i class="fas fa-rocket mr-2 text-green-400"></i>Performance
                        </h5>
                        <ul class="text-sm text-gray-300 space-y-1">
                            <li>• Build de assets otimizado</li>
                            <li>• Cache Laravel configurado</li>
                            <li>• Compressão automática</li>
                            <li>• Storage links</li>
                        </ul>
                    </div>

                    <div class="bg-black/20 rounded p-4">
                        <h5 class="font-semibold text-white mb-2 flex items-center">
                            <i class="fas fa-search mr-2 text-purple-400"></i>SEO (Admin Panel)
                        </h5>
                        <ul class="text-sm text-gray-300 space-y-1">
                            <li>• Configurações via Admin → SEO</li>
                            <li>• Meta tags customizáveis</li>
                            <li>• Templates com variáveis</li>
                            <li>• Configuração pós-instalação</li>
                        </ul>
                    </div>

                    <div class="bg-black/20 rounded p-4">
                        <h5 class="font-semibold text-white mb-2 flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-red-400"></i>Segurança
                        </h5>
                        <ul class="text-sm text-gray-300 space-y-1">
                            <li>• Headers de segurança</li>
                            <li>• Proteção CSRF/XSS</li>
                            <li>• Rate limiting</li>
                            <li>• Permissões otimizadas</li>
                        </ul>
                    </div>

                    <div class="bg-black/20 rounded p-4">
                        <h5 class="font-semibold text-white mb-2 flex items-center">
                            <i class="fas fa-cog mr-2 text-orange-400"></i>Sistema
                        </h5>
                        <ul class="text-sm text-gray-300 space-y-1">
                            <li>• Composer optimizado</li>
                            <li>• Dependências NPM</li>
                            <li>• Scripts de automação</li>
                            <li>• Configuração de produção</li>
                        </ul>
                    </div>

                    <div class="bg-black/20 rounded p-4">
                        <h5 class="font-semibold text-white mb-2 flex items-center">
                            <i class="fas fa-file-alt mr-2 text-cyan-400"></i>Conteúdo
                        </h5>
                        <ul class="text-sm text-gray-300 space-y-1">
                            <li>• Páginas padrão</li>
                            <li>• Política de privacidade</li>
                            <li>• Termos de uso</li>
                            <li>• Robots.txt otimizado</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" id="install-btn" 
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-blue-600 text-white font-bold text-lg rounded-lg hover:from-green-700 hover:to-blue-700 transition-all duration-300 shadow-lg">
                    <i class="fas fa-magic mr-3"></i>
                    Iniciar Instalação Automática
                </button>
                <p class="text-sm text-gray-400 mt-3">
                    A instalação pode levar alguns minutos. Não feche esta página durante o processo.
                </p>
            </div>
        </form>
    </div>

    <script>
    function detectDatabase() {
        fetch('{{ route("install.detect_database") }}')
            .then(response => response.json())
            .then(data => {
                if (data.detected) {
                    document.getElementById('host').value = data.host;
                    document.getElementById('port').value = data.port;
                    
                    showNotification('Configuração de banco detectada automaticamente!', 'success');
                } else {
                    showNotification('Nenhuma configuração de banco detectada automaticamente.', 'info');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erro ao detectar configuração do banco.', 'error');
            });
    }

    function testDatabaseConnection() {
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('host', document.getElementById('host').value);
        formData.append('port', document.getElementById('port').value);
        formData.append('name', document.getElementById('name').value);
        formData.append('username', document.getElementById('username').value);
        formData.append('password', document.getElementById('password').value);

        const resultDiv = document.getElementById('db-test-result');
        resultDiv.innerHTML = '<div class="text-blue-400"><i class="fas fa-spinner fa-spin mr-2"></i>Testando conexão...</div>';

        fetch('{{ route("install.test_database") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = '<div class="text-green-400"><i class="fas fa-check mr-2"></i>' + data.message + '</div>';
            } else {
                resultDiv.innerHTML = '<div class="text-red-400"><i class="fas fa-times mr-2"></i>' + data.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = '<div class="text-red-400"><i class="fas fa-times mr-2"></i>Erro ao testar conexão</div>';
        });
    }

    function showNotification(message, type) {
        const colors = {
            success: 'bg-green-600',
            error: 'bg-red-600',
            info: 'bg-blue-600'
        };

        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-4 py-2 rounded-lg shadow-lg z-50`;
        notification.innerHTML = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Auto-detect database on page load
    document.addEventListener('DOMContentLoaded', function() {
        // detectDatabase();
    });
    </script>
@endsection
