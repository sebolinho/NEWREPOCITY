<?php

namespace App\Install;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class SystemOptimizer
{
    private $output = [];

    public function runFullOptimization()
    {
        $this->output = [];
        
        try {
            $this->optimizeComposer();
            $this->buildAssets();
            $this->optimizeLaravel();
            $this->createStorageLinks();
            $this->setOptimalPermissions();
            $this->generateOptimizedCache();
            
            return [
                'success' => true,
                'output' => $this->output,
                'message' => 'System optimization completed successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'output' => $this->output,
                'error' => $e->getMessage()
            ];
        }
    }

    public function optimizeComposer()
    {
        $this->addOutput('Optimizing Composer dependencies...');
        
        $process = new Process(['composer', 'install', '--optimize-autoloader', '--no-dev']);
        $process->setWorkingDirectory(base_path());
        $process->setTimeout(300); // 5 minutes
        
        $process->run(function ($type, $buffer) {
            $this->addOutput($buffer);
        });
        
        if (!$process->isSuccessful()) {
            // Fallback to basic composer install
            $this->addOutput('Falling back to basic composer install...');
            $process = new Process(['composer', 'install']);
            $process->setWorkingDirectory(base_path());
            $process->run();
        }
        
        // Dump optimized autoloader
        Artisan::call('optimize:clear');
        $this->addOutput('Composer optimization completed');
    }

    public function buildAssets()
    {
        $this->addOutput('Installing NPM dependencies and building assets...');
        
        try {
            // Install NPM dependencies
            $process = new Process(['npm', 'ci']);
            $process->setWorkingDirectory(base_path());
            $process->setTimeout(300);
            
            $process->run(function ($type, $buffer) {
                $this->addOutput($buffer);
            });
            
            if (!$process->isSuccessful()) {
                $this->addOutput('NPM ci failed, trying npm install...');
                $process = new Process(['npm', 'install']);
                $process->setWorkingDirectory(base_path());
                $process->run();
            }
            
            // Build production assets
            $this->addOutput('Building production assets...');
            $process = new Process(['npm', 'run', 'build']);
            $process->setWorkingDirectory(base_path());
            $process->setTimeout(300);
            
            $process->run(function ($type, $buffer) {
                $this->addOutput($buffer);
            });
            
            $this->addOutput('Asset build completed');
        } catch (\Exception $e) {
            $this->addOutput('Asset build failed: ' . $e->getMessage());
            // Continue installation even if asset build fails
        }
    }

    public function optimizeLaravel()
    {
        $this->addOutput('Optimizing Laravel application...');
        
        // Clear all caches first
        Artisan::call('optimize:clear');
        $this->addOutput('Caches cleared');
        
        // Generate application cache
        Artisan::call('config:cache');
        $this->addOutput('Configuration cached');
        
        Artisan::call('route:cache');
        $this->addOutput('Routes cached');
        
        Artisan::call('view:cache');
        $this->addOutput('Views cached');
        
        // Event cache (if available)
        try {
            Artisan::call('event:cache');
            $this->addOutput('Events cached');
        } catch (\Exception $e) {
            $this->addOutput('Event caching skipped (not available)');
        }
        
        $this->addOutput('Laravel optimization completed');
    }

    public function createStorageLinks()
    {
        $this->addOutput('Creating storage symbolic links...');
        
        try {
            Artisan::call('storage:link');
            $this->addOutput('Storage links created successfully');
        } catch (\Exception $e) {
            $this->addOutput('Storage link creation failed: ' . $e->getMessage());
        }
    }

    public function setOptimalPermissions()
    {
        $this->addOutput('Setting optimal file permissions...');
        
        try {
            // Set directory permissions
            $directories = [
                storage_path(),
                storage_path('app'),
                storage_path('framework'),
                storage_path('logs'),
                base_path('bootstrap/cache'),
                public_path(),
            ];
            
            foreach ($directories as $directory) {
                if (is_dir($directory)) {
                    chmod($directory, 0755);
                    $this->addOutput("Set permissions for: {$directory}");
                }
            }
            
            // Set file permissions for sensitive files
            $files = [
                base_path('.env'),
                base_path('artisan'),
            ];
            
            foreach ($files as $file) {
                if (file_exists($file)) {
                    chmod($file, 0644);
                }
            }
            
            $this->addOutput('File permissions set successfully');
        } catch (\Exception $e) {
            $this->addOutput('Permission setting failed: ' . $e->getMessage());
        }
    }

    public function generateOptimizedCache()
    {
        $this->addOutput('Generating optimized application cache...');
        
        try {
            // Warm up the application
            Artisan::call('optimize');
            $this->addOutput('Application optimized');
            
            // Generate sitemap if available
            try {
                Artisan::call('sitemap:generate');
                $this->addOutput('Sitemap generated');
            } catch (\Exception $e) {
                $this->addOutput('Sitemap generation skipped (command not available)');
            }
            
            $this->addOutput('Cache generation completed');
        } catch (\Exception $e) {
            $this->addOutput('Cache generation failed: ' . $e->getMessage());
        }
    }

    public function runSystemChecks()
    {
        $checks = [];
        
        // Check if optimization scripts exist
        $checks['optimize_script'] = file_exists(base_path('optimize.sh'));
        $checks['analyze_script'] = file_exists(base_path('analyze.sh'));
        
        // Check if build directory exists
        $checks['build_assets'] = is_dir(public_path('build'));
        
        // Check if storage is linked
        $checks['storage_linked'] = is_link(public_path('storage'));
        
        // Check if cache files exist
        $checks['config_cached'] = file_exists(base_path('bootstrap/cache/config.php'));
        $checks['routes_cached'] = file_exists(base_path('bootstrap/cache/routes-v7.php'));
        
        return $checks;
    }

    public function runOptimizationScript()
    {
        $this->addOutput('Running optimization script...');
        
        $scriptPath = base_path('optimize.sh');
        if (file_exists($scriptPath)) {
            try {
                $process = new Process(['bash', $scriptPath]);
                $process->setWorkingDirectory(base_path());
                $process->setTimeout(180);
                
                $process->run(function ($type, $buffer) {
                    $this->addOutput($buffer);
                });
                
                $this->addOutput('Optimization script completed');
                return true;
            } catch (\Exception $e) {
                $this->addOutput('Optimization script failed: ' . $e->getMessage());
                return false;
            }
        } else {
            $this->addOutput('Optimization script not found, skipping...');
            return false;
        }
    }

    private function addOutput($message)
    {
        $this->output[] = trim($message);
    }

    public function getOutput()
    {
        return $this->output;
    }
}