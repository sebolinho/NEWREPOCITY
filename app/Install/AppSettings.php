<?php

namespace App\Install;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class AppSettings
{
    public function setup($data)
    {
        $this->generateAppKey();
        $this->setupEnvironmentVariables($data);
        $this->setupApplicationSettings($data);
        $this->setupPerformanceSettings();
        $this->setupSecuritySettings();
        
        return [
            'success' => true,
            'message' => 'Application settings configured successfully'
        ];
    }

    private function generateAppKey()
    {
        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
        }
    }

    private function setupEnvironmentVariables($data)
    {
        $env = DotenvEditor::load();
        $env->autoBackup(false);

        // Basic Application Settings
        $env->setKey('APP_ENV', 'production');
        $env->setKey('APP_DEBUG', 'false');
        $env->setKey('APP_TIMEZONE', $data->timezone ?? 'America/Sao_Paulo');
        $env->setKey('APP_LOCALE', $data->locale ?? 'pt_BR');
        $env->setKey('APP_FALLBACK_LOCALE', 'en');
        
        // Security Settings
        $env->setKey('SESSION_LIFETIME', '120');
        $env->setKey('SESSION_ENCRYPT', 'true');
        $env->setKey('SESSION_SAME_SITE', 'lax');
        $env->setKey('SESSION_SECURE_COOKIES', 'true');
        
        // Cache Configuration
        $cacheDriver = extension_loaded('redis') ? 'redis' : 'file';
        $env->setKey('CACHE_DRIVER', $cacheDriver);
        $env->setKey('SESSION_DRIVER', $cacheDriver);
        $env->setKey('QUEUE_CONNECTION', $cacheDriver === 'redis' ? 'redis' : 'sync');
        
        // Broadcasting (if Redis available)
        if ($cacheDriver === 'redis') {
            $env->setKey('BROADCAST_DRIVER', 'redis');
            $env->setKey('REDIS_HOST', '127.0.0.1');
            $env->setKey('REDIS_PASSWORD', 'null');
            $env->setKey('REDIS_PORT', '6379');
        }
        
        // Mail Configuration
        $env->setKey('MAIL_MAILER', 'smtp');
        $env->setKey('MAIL_HOST', $data->mail_host ?? 'localhost');
        $env->setKey('MAIL_PORT', $data->mail_port ?? '587');
        $env->setKey('MAIL_USERNAME', $data->mail_username ?? '');
        $env->setKey('MAIL_PASSWORD', $data->mail_password ?? '');
        $env->setKey('MAIL_ENCRYPTION', $data->mail_encryption ?? 'tls');
        $env->setKey('MAIL_FROM_ADDRESS', $data->mail_from ?? 'noreply@' . parse_url(config('app.url'), PHP_URL_HOST));
        $env->setKey('MAIL_FROM_NAME', '"' . ($data->site_name ?? config('app.name')) . '"');
        
        // File Storage
        $env->setKey('FILESYSTEM_DISK', 'public');
        
        // Performance Settings
        $env->setKey('OPTIMIZE_IMAGES', 'true');
        $env->setKey('ENABLE_COMPRESSION', 'true');
        $env->setKey('MINIFY_HTML', 'true');
        $env->setKey('PRELOAD_CRITICAL_CSS', 'true');
        
        // API Settings
        $env->setKey('API_RATE_LIMIT', '60');
        $env->setKey('API_THROTTLE_REQUESTS', '1000');
        $env->setKey('API_THROTTLE_MINUTES', '1');

        $env->save();
    }

    private function setupApplicationSettings($data)
    {
        try {
            // Wait a moment for database to be ready
            sleep(2);
            
            // Check if settings table exists
            if (!$this->checkTableExists('settings')) {
                return;
            }

            $appSettings = [
                [
                    'key' => 'app_installed',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'installation_date',
                    'value' => now()->toDateTimeString(),
                    'type' => 'datetime'
                ],
                [
                    'key' => 'app_version',
                    'value' => '1.0.0',
                    'type' => 'text'
                ],
                [
                    'key' => 'maintenance_mode',
                    'value' => '0',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'site_name',
                    'value' => $data->site_name ?? 'NEWREPOCITY Streaming',
                    'type' => 'text'
                ],
                [
                    'key' => 'admin_email',
                    'value' => $data->admin_email ?? 'admin@admin.com',
                    'type' => 'email'
                ],
                [
                    'key' => 'timezone',
                    'value' => $data->timezone ?? 'America/Sao_Paulo',
                    'type' => 'text'
                ],
                [
                    'key' => 'language',
                    'value' => $data->locale ?? 'pt_BR',
                    'type' => 'text'
                ]
            ];

            foreach ($appSettings as $setting) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    $setting
                );
            }
        } catch (\Exception $e) {
            // Continue if database settings fail
        }
    }

    private function setupPerformanceSettings()
    {
        try {
            if (!$this->checkTableExists('settings')) {
                return;
            }

            $performanceSettings = [
                [
                    'key' => 'cache_enabled',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'cache_lifetime',
                    'value' => '3600',
                    'type' => 'integer'
                ],
                [
                    'key' => 'compression_enabled',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'image_optimization',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'lazy_loading',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'minify_css',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'minify_js',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'webp_support',
                    'value' => extension_loaded('gd') || extension_loaded('imagick') ? '1' : '0',
                    'type' => 'boolean'
                ]
            ];

            foreach ($performanceSettings as $setting) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    $setting
                );
            }
        } catch (\Exception $e) {
            // Continue if database settings fail
        }
    }

    private function setupSecuritySettings()
    {
        try {
            if (!$this->checkTableExists('settings')) {
                return;
            }

            $securitySettings = [
                [
                    'key' => 'security_headers_enabled',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'csrf_protection',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'xss_protection',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'content_security_policy',
                    'value' => "default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:;",
                    'type' => 'text'
                ],
                [
                    'key' => 'force_https',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'hsts_enabled',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'rate_limiting_enabled',
                    'value' => '1',
                    'type' => 'boolean'
                ],
                [
                    'key' => 'max_login_attempts',
                    'value' => '5',
                    'type' => 'integer'
                ],
                [
                    'key' => 'session_timeout',
                    'value' => '120',
                    'type' => 'integer'
                ]
            ];

            foreach ($securitySettings as $setting) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    $setting
                );
            }
        } catch (\Exception $e) {
            // Continue if database settings fail
        }
    }

    private function checkTableExists($tableName)
    {
        try {
            return DB::getSchemaBuilder()->hasTable($tableName);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createAdminUser($data)
    {
        try {
            if (!$this->checkTableExists('users')) {
                return false;
            }

            $adminData = [
                'name' => 'Administrator',
                'email' => $data->admin_email ?? 'admin@admin.com',
                'password' => bcrypt($data->admin_password ?? 'admin'),
                'email_verified_at' => now(),
                'is_admin' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Check if admin user already exists
            $existingAdmin = DB::table('users')->where('email', $adminData['email'])->first();
            
            if (!$existingAdmin) {
                DB::table('users')->insert($adminData);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function setupDefaultContent()
    {
        try {
            // Setup default pages, categories, etc.
            if ($this->checkTableExists('pages')) {
                $defaultPages = [
                    [
                        'title' => 'Política de Privacidade',
                        'slug' => 'privacy-policy',
                        'content' => $this->getDefaultPrivacyPolicy(),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title' => 'Termos de Uso',
                        'slug' => 'terms-of-service',
                        'content' => $this->getDefaultTermsOfService(),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ];

                foreach ($defaultPages as $page) {
                    DB::table('pages')->updateOrInsert(
                        ['slug' => $page['slug']],
                        $page
                    );
                }
            }
        } catch (\Exception $e) {
            // Continue if content setup fails
        }
    }

    private function getDefaultPrivacyPolicy()
    {
        return '<h2>Política de Privacidade</h2>
<p>Esta política de privacidade descreve como coletamos, usamos e protegemos suas informações pessoais.</p>
<h3>Informações que Coletamos</h3>
<p>Coletamos informações que você nos fornece diretamente, como quando você cria uma conta ou entra em contato conosco.</p>
<h3>Como Usamos suas Informações</h3>
<p>Usamos suas informações para fornecer, manter e melhorar nossos serviços.</p>
<h3>Compartilhamento de Informações</h3>
<p>Não vendemos, negociamos ou transferimos suas informações pessoais para terceiros.</p>
<h3>Segurança</h3>
<p>Implementamos medidas de segurança apropriadas para proteger suas informações pessoais.</p>';
    }

    private function getDefaultTermsOfService()
    {
        return '<h2>Termos de Uso</h2>
<p>Ao acessar e usar este site, você aceita estar vinculado aos seguintes termos e condições.</p>
<h3>Uso do Site</h3>
<p>Você pode usar nosso site para fins pessoais e não comerciais, de acordo com estes termos.</p>
<h3>Conteúdo</h3>
<p>Todo o conteúdo deste site é protegido por direitos autorais e outras leis de propriedade intelectual.</p>
<h3>Limitação de Responsabilidade</h3>
<p>Em nenhuma circunstância seremos responsáveis por danos diretos, indiretos, incidentais ou consequenciais.</p>
<h3>Modificações</h3>
<p>Reservamo-nos o direito de modificar estes termos a qualquer momento.</p>';
    }
}
