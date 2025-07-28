@extends('layouts.install')
@section('content')
    <div class="max-w-4xl mx-auto w-full py-10">
        <div class="mb-6">
            <a class="inline-block" href="{{route('index')}}">
                <x-ui.logo height="38" class="text-gray-700 dark:text-white"/>
            </a>
        </div>

        <!-- Progress Bar -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-semibold text-white mb-4">📋 Installation Progress</h4>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white font-bold">✓</div>
                    <span class="ml-2 text-green-400 font-medium">Requirements Check</span>
                </div>
                <div class="flex-1 h-1 bg-blue-500 mx-2"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">2</div>
                    <span class="ml-2 text-blue-400 font-medium">Configuration</span>
                </div>
                <div class="flex-1 h-1 bg-gray-600 mx-2"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold">3</div>
                    <span class="ml-2 text-gray-400">Complete</span>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-3xl mb-2 font-bold text-white">🔧 Platform Configuration</h3>
            <p class="text-gray-400">Configure your streaming platform with database, admin account, and optional features.</p>
        </div>

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>Error:</strong> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>Please fix the following errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('install.store') }}" class="space-y-8" id="installForm">
            @csrf

            <!-- Application Settings -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                    🏢 Application Settings
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-form.label for="app_name" :value="__('Application Name')"/>
                        <x-form.input id="app_name" class="block mt-1 w-full" type="text" name="app_name"
                                      value="{{ old('app_name', 'My Streaming Platform') }}"
                                      placeholder="My Streaming Platform"/>
                        <x-form.error :messages="$errors->get('app_name')" class="mt-2"/>
                    </div>
                    <div>
                        <x-form.label for="app_url" :value="__('Application URL')"/>
                        <x-form.input id="app_url" class="block mt-1 w-full" type="url" name="app_url"
                                      value="{{ old('app_url', url('/')) }}"
                                      placeholder="https://your-domain.com"/>
                        <x-form.error :messages="$errors->get('app_url')" class="mt-2"/>
                    </div>
                </div>
                <div class="mt-4">
                    <x-form.label for="license_key" :value="__('License Key')"/>
                    <x-form.input id="license_key" class="block mt-1 w-full" type="text" name="license_key"
                                  value="{{ old('license_key') }}"
                                  placeholder="Enter your license key"/>
                    <x-form.error :messages="$errors->get('license_key')" class="mt-2"/>
                    <div class="text-xs text-gray-400 py-2">
                        Valid for codelug.com registered users. CodeSter users can use "codester".
                    </div>
                </div>
            </div>

            <!-- Database Configuration -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                    🗄️ Database Configuration
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-form.label for="host" :value="__('Database Host')"/>
                        <x-form.input id="host" class="block mt-1 w-full" type="text" name="host"
                                      value="{{ old('host', '127.0.0.1') }}"
                                      required placeholder="127.0.0.1"/>
                        <x-form.error :messages="$errors->get('host')" class="mt-2"/>
                    </div>
                    <div>
                        <x-form.label for="port" :value="__('Database Port')"/>
                        <x-form.input id="port" class="block mt-1 w-full" type="number" name="port"
                                      value="{{ old('port', '3306') }}"
                                      placeholder="3306"/>
                        <x-form.error :messages="$errors->get('port')" class="mt-2"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <x-form.label for="name" :value="__('Database Name')"/>
                        <x-form.input id="name" class="block mt-1 w-full" type="text" name="name"
                                      value="{{ old('name') }}"
                                      required placeholder="streaming_platform"/>
                        <x-form.error :messages="$errors->get('name')" class="mt-2"/>
                    </div>
                    <div>
                        <x-form.label for="username" :value="__('Database Username')"/>
                        <x-form.input id="username" class="block mt-1 w-full" type="text" name="username"
                                      value="{{ old('username') }}"
                                      required placeholder="root"/>
                        <x-form.error :messages="$errors->get('username')" class="mt-2"/>
                    </div>
                </div>
                <div class="mt-6">
                    <x-form.label for="password" :value="__('Database Password')"/>
                    <x-form.input id="password" class="block mt-1 w-full" type="password" name="password"
                                  value="{{ old('password') }}"
                                  placeholder="Enter database password"/>
                    <x-form.error :messages="$errors->get('password')" class="mt-2"/>
                </div>
            </div>

            <!-- Admin Account -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                    👤 Administrator Account
                </h4>
                <p class="text-gray-400 text-sm mb-4">Create your admin account to manage the platform.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-form.label for="admin_name" :value="__('Admin Name')"/>
                        <x-form.input id="admin_name" class="block mt-1 w-full" type="text" name="admin_name"
                                      value="{{ old('admin_name', 'Administrator') }}"
                                      placeholder="Administrator"/>
                        <x-form.error :messages="$errors->get('admin_name')" class="mt-2"/>
                    </div>
                    <div>
                        <x-form.label for="admin_email" :value="__('Admin Email')"/>
                        <x-form.input id="admin_email" class="block mt-1 w-full" type="email" name="admin_email"
                                      value="{{ old('admin_email', 'admin@admin.com') }}"
                                      placeholder="admin@admin.com"/>
                        <x-form.error :messages="$errors->get('admin_email')" class="mt-2"/>
                    </div>
                </div>
                <div class="mt-6">
                    <x-form.label for="admin_password" :value="__('Admin Password')"/>
                    <x-form.input id="admin_password" class="block mt-1 w-full" type="password" name="admin_password"
                                  value="{{ old('admin_password') }}"
                                  placeholder="Enter a secure password (min 8 characters)"/>
                    <x-form.error :messages="$errors->get('admin_password')" class="mt-2"/>
                    <div class="text-xs text-gray-400 py-2">
                        Leave empty to use default: admin@admin.com / admin
                    </div>
                </div>
            </div>

            <!-- Email Configuration (Optional) -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                    📧 Email Configuration <span class="ml-2 text-sm text-gray-400">(Optional)</span>
                </h4>
                <p class="text-gray-400 text-sm mb-4">Configure email settings for notifications and user management.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-form.label for="mail_host" :value="__('SMTP Host')"/>
                        <x-form.input id="mail_host" class="block mt-1 w-full" type="text" name="mail_host"
                                      value="{{ old('mail_host') }}"
                                      placeholder="smtp.gmail.com"/>
                        <x-form.error :messages="$errors->get('mail_host')" class="mt-2"/>
                    </div>
                    <div>
                        <x-form.label for="mail_port" :value="__('SMTP Port')"/>
                        <x-form.input id="mail_port" class="block mt-1 w-full" type="number" name="mail_port"
                                      value="{{ old('mail_port', '587') }}"
                                      placeholder="587"/>
                        <x-form.error :messages="$errors->get('mail_port')" class="mt-2"/>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <x-form.label for="mail_username" :value="__('SMTP Username')"/>
                        <x-form.input id="mail_username" class="block mt-1 w-full" type="text" name="mail_username"
                                      value="{{ old('mail_username') }}"
                                      placeholder="your-email@gmail.com"/>
                        <x-form.error :messages="$errors->get('mail_username')" class="mt-2"/>
                    </div>
                    <div>
                        <x-form.label for="mail_password" :value="__('SMTP Password')"/>
                        <x-form.input id="mail_password" class="block mt-1 w-full" type="password" name="mail_password"
                                      value="{{ old('mail_password') }}"
                                      placeholder="Enter SMTP password"/>
                        <x-form.error :messages="$errors->get('mail_password')" class="mt-2"/>
                    </div>
                </div>
                
                <div class="mt-6">
                    <x-form.label for="mail_encryption" :value="__('Encryption')"/>
                    <select id="mail_encryption" name="mail_encryption" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                        <option value="tls" {{ old('mail_encryption', 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('mail_encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ old('mail_encryption') === 'none' ? 'selected' : '' }}>None</option>
                    </select>
                    <x-form.error :messages="$errors->get('mail_encryption')" class="mt-2"/>
                </div>
            </div>

            <!-- Installation Options -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                    ⚙️ Installation Options
                </h4>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="build_assets" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-white">Build frontend assets (recommended)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="run_optimization" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-white">Run optimization script</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="import_sample_data" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-white">Import sample data (optional)</span>
                    </label>
                </div>
            </div>

            <!-- Installation Button -->
            <div class="bg-gray-900 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="text-lg font-semibold text-white">Ready to Install?</h5>
                        <p class="text-gray-400 text-sm">This will set up your streaming platform with all the configurations above.</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('install.index') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            ← Back
                        </a>
                        <button type="submit" id="installBtn" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            🚀 Start Installation
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Installation Progress Modal -->
        <div id="installProgress" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-gray-800 rounded-lg p-8 max-w-md w-full mx-4">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                    <h3 class="text-xl font-semibold text-white mb-2">Installing Platform...</h3>
                    <p class="text-gray-400 text-sm mb-4">Please wait while we set up your streaming platform. This may take a few minutes.</p>
                    <div class="bg-gray-700 rounded-full h-2 mb-4">
                        <div id="progressBar" class="bg-blue-500 h-2 rounded-full transition-all duration-1000" style="width: 0%"></div>
                    </div>
                    <p id="progressText" class="text-gray-400 text-sm">Initializing...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('installForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show progress modal
            document.getElementById('installProgress').classList.remove('hidden');
            
            // Simulate progress
            let progress = 0;
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const steps = [
                'Connecting to database...',
                'Running migrations...',
                'Creating admin user...',
                'Setting up storage...',
                'Building assets...',
                'Optimizing system...',
                'Finalizing installation...'
            ];
            
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 90) progress = 90;
                
                progressBar.style.width = progress + '%';
                const stepIndex = Math.floor(progress / 15);
                if (steps[stepIndex]) {
                    progressText.textContent = steps[stepIndex];
                }
            }, 500);
            
            // Submit the actual form
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                clearInterval(interval);
                progressBar.style.width = '100%';
                progressText.textContent = 'Installation complete!';
                
                setTimeout(() => {
                    if (response.ok) {
                        window.location.href = '{{ route("install.complete") }}';
                    } else {
                        // Handle errors
                        document.getElementById('installProgress').classList.add('hidden');
                        alert('Installation failed. Please check the form for errors.');
                        location.reload();
                    }
                }, 1000);
            }).catch(error => {
                clearInterval(interval);
                document.getElementById('installProgress').classList.add('hidden');
                console.error('Installation error:', error);
                this.submit(); // Fallback to regular form submission
            });
        });
    </script>
@endsection
