<?php

namespace App\Install;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Exception;

class AppSettings
{
    public function setup($data)
    {
        try {
            $this->generateAppKey();
            $this->buildAssets();
            $this->setPermissions();
            $this->setupEnvironment();
            $this->runOptimization();
            
            return ['success' => true];
        } catch (Exception $e) {
            Log::error('App settings setup failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateAppKey()
    {
        Artisan::call('key:generate', ['--force' => true]);
    }

    private function buildAssets()
    {
        try {
            // Check if node_modules exists and npm is available
            if (File::exists(base_path('package.json'))) {
                // Run npm install if node_modules doesn't exist
                if (!File::exists(base_path('node_modules'))) {
                    Log::info('Installing npm dependencies...');
                    exec('cd ' . base_path() . ' && npm install --production 2>&1', $output, $return_var);
                    
                    if ($return_var !== 0) {
                        Log::warning('npm install failed: ' . implode('\n', $output));
                    }
                }

                // Build assets
                Log::info('Building production assets...');
                exec('cd ' . base_path() . ' && npm run build 2>&1', $output, $return_var);
                
                if ($return_var !== 0) {
                    Log::warning('Asset build failed: ' . implode('\n', $output));
                }
            }
        } catch (Exception $e) {
            Log::warning('Asset building failed: ' . $e->getMessage());
            // Don't throw error, just log it
        }
    }

    private function setPermissions()
    {
        try {
            $directories = [
                storage_path(),
                base_path('bootstrap/cache'),
                public_path('storage'),
            ];

            foreach ($directories as $directory) {
                if (File::exists($directory)) {
                    chmod($directory, 0755);
                }
            }

            // Set execute permission on optimize script if it exists
            $optimizeScript = base_path('optimize.sh');
            if (File::exists($optimizeScript)) {
                chmod($optimizeScript, 0755);
            }
        } catch (Exception $e) {
            Log::warning('Permission setting failed: ' . $e->getMessage());
        }
    }

    private function setupEnvironment()
    {
        $env = DotenvEditor::load();
        $env->autoBackup(false);

        // Set production environment
        $env->setKey('APP_ENV', 'production');
        $env->setKey('APP_DEBUG', 'false');
        
        // Set session and cache drivers
        $env->setKey('SESSION_DRIVER', 'file');
        $env->setKey('CACHE_DRIVER', 'file');
        
        // Set security settings
        $env->setKey('SESSION_SECURE_COOKIE', 'true');
        $env->setKey('SESSION_SAME_SITE', 'lax');
        
        $env->save();
    }

    private function runOptimization()
    {
        try {
            // Clear all caches first
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Then optimize
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            // Run additional optimization if available
            if (Artisan::call('optimize:clear-all', [], false) === 0) {
                Log::info('Custom optimization commands executed successfully');
            }

            // Run the optimization script if it exists
            $optimizeScript = base_path('optimize.sh');
            if (File::exists($optimizeScript) && is_executable($optimizeScript)) {
                Log::info('Running optimization script...');
                exec($optimizeScript . ' 2>&1', $output, $return_var);
                
                if ($return_var === 0) {
                    Log::info('Optimization script completed successfully');
                } else {
                    Log::warning('Optimization script failed: ' . implode('\n', $output));
                }
            }
        } catch (Exception $e) {
            Log::warning('Optimization failed: ' . $e->getMessage());
        }
    }
}
