<?php

namespace App\Install;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use App\Models\User;
use Exception;

class Database
{
    public function setup($data)
    {
        try {
            $this->checkDatabaseConnection($data);
            $this->setEnvVariables($data);
            $this->setupStorage();
            $this->migrateDatabase();
            $this->createAdminUser($data);
            $this->setMailConfiguration($data);
            $this->optimizeSystem();
            
            return ['success' => true, 'message' => 'Installation completed successfully'];
        } catch (Exception $e) {
            Log::error('Installation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function checkDatabaseConnection($data)
    {
        $this->setupDatabaseConnectionConfig($data);

        try {
            DB::connection('mysql')->reconnect();
            DB::connection('mysql')->getPdo();
        } catch (Exception $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    private function setupDatabaseConnectionConfig($data)
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $data->host,
            'database.connections.mysql.port' => $data->port ?: 3306,
            'database.connections.mysql.database' => $data->name,
            'database.connections.mysql.username' => $data->username,
            'database.connections.mysql.password' => $data->password,
        ]);
    }

    private function setEnvVariables($data)
    {
        $env = DotenvEditor::load();
        $env->autoBackup(false);

        // Database configuration
        $env->setKey('DB_HOST', $data->host);
        $env->setKey('DB_PORT', $data->port ?: 3306);
        $env->setKey('DB_DATABASE', $data->name);
        $env->setKey('DB_USERNAME', $data->username);
        $env->setKey('DB_PASSWORD', $data->password);
        
        // License and app configuration
        $env->setKey('LICENSE_KEY', $data->license_key ?? '');
        $env->setKey('APP_URL', $data->app_url ?? url('/'));
        $env->setKey('APP_NAME', $data->app_name ?? 'Streaming Platform');
        
        $env->save();
    }

    private function setMailConfiguration($data)
    {
        if (isset($data->mail_host) && $data->mail_host) {
            $env = DotenvEditor::load();
            $env->autoBackup(false);

            $env->setKey('MAIL_MAILER', 'smtp');
            $env->setKey('MAIL_HOST', $data->mail_host);
            $env->setKey('MAIL_PORT', $data->mail_port ?? 587);
            $env->setKey('MAIL_USERNAME', $data->mail_username ?? '');
            $env->setKey('MAIL_PASSWORD', $data->mail_password ?? '');
            $env->setKey('MAIL_ENCRYPTION', $data->mail_encryption ?? 'tls');
            $env->setKey('MAIL_FROM_ADDRESS', $data->mail_from ?? $data->admin_email ?? 'admin@example.com');
            $env->setKey('MAIL_FROM_NAME', $data->app_name ?? 'Streaming Platform');

            $env->save();
        }
    }

    private function setupStorage()
    {
        // Create storage directories if they don't exist
        $directories = [
            storage_path('app/public'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
            public_path('storage'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }

        // Create storage link
        if (!File::exists(public_path('storage'))) {
            Artisan::call('storage:link');
        }
    }

    private function migrateDatabase()
    {
        try {
            Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
        } catch (Exception $e) {
            throw new Exception('Database migration failed: ' . $e->getMessage());
        }
    }

    private function createAdminUser($data)
    {
        if (isset($data->admin_email) && isset($data->admin_password)) {
            try {
                // Check if admin user already exists
                $existingUser = User::where('email', $data->admin_email)->first();
                
                if (!$existingUser) {
                    User::create([
                        'name' => $data->admin_name ?? 'Administrator',
                        'email' => $data->admin_email,
                        'password' => Hash::make($data->admin_password),
                        'email_verified_at' => now(),
                        'role' => 'admin'
                    ]);
                }
            } catch (Exception $e) {
                Log::warning('Admin user creation failed: ' . $e->getMessage());
                // Don't throw error here, just log it
            }
        }
    }

    private function optimizeSystem()
    {
        try {
            // Run basic optimization commands
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            
            // Generate sitemap if possible
            if (class_exists('\\App\\Console\\Commands\\GenerateSitemap')) {
                Artisan::call('sitemap:generate');
            }
        } catch (Exception $e) {
            Log::warning('System optimization failed: ' . $e->getMessage());
            // Don't throw error here, just log it
        }
    }
}
