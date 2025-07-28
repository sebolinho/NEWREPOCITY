<?php

namespace App\Http\Controllers;
use App\Http\Middleware\RedirectToInstaller;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Http\Request;
use App\Install\Requirement;
use App\Install\Database;
use App\Install\AppSettings;
use App\Install\SystemOptimizer;
use App\Install\SEOSetup;
use Exception;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class InstallController extends Controller
{
    public function __construct()
    {
        $this->middleware(RedirectToInstaller::class);
    }

    public function index(Requirement $requirement)
    {
        // Auto-detect database configuration
        $detectedDb = (new Database())->detectDatabaseConfiguration();
        
        return view('install.index', compact('requirement', 'detectedDb'));
    }

    public function config(Requirement $requirement)
    {
        if (! $requirement->satisfied()) {
            return redirect()->route('install.index')->with('error', 'System requirements not met. Please resolve all issues before continuing.');
        }
        
        // Get default SEO configuration
        $seoDefaults = (new SEOSetup())->getDefaultSEOConfig();
        
        return view('install.config', compact('requirement', 'seoDefaults'));
    }

    public function store(Request $request, Database $database, AppSettings $settings, SystemOptimizer $optimizer, SEOSetup $seoSetup) 
    {
        // Validate input
        $request->validate([
            'host' => 'required|string',
            'name' => 'required|string|regex:/^[a-zA-Z0-9_]+$/',
            'username' => 'required|string',
            'port' => 'nullable|numeric|min:1|max:65535',
            'site_name' => 'required|string|max:255',
            'site_url' => 'nullable|url',
            'admin_email' => 'required|email',
            'admin_password' => 'nullable|string|min:6',
        ]);

        $installationSteps = [];
        $errors = [];

        try {
            // Step 1: Test Database Connection
            $installationSteps[] = ['step' => 'Testing database connection...', 'status' => 'running'];
            
            $connectionTest = $database->testConnection($request);
            if (!$connectionTest['success']) {
                return back()->withInput()->with('error', $connectionTest['message']);
            }
            $installationSteps[] = ['step' => 'Database connection successful', 'status' => 'completed'];

            // Step 2: Create Database if needed
            $installationSteps[] = ['step' => 'Setting up database...', 'status' => 'running'];
            
            $dbCreation = $database->createDatabaseIfNotExists($request);
            if (!$dbCreation['success']) {
                return back()->withInput()->with('error', $dbCreation['message']);
            }
            
            if (isset($dbCreation['created']) && $dbCreation['created']) {
                $installationSteps[] = ['step' => 'Database created successfully', 'status' => 'completed'];
            } else {
                $installationSteps[] = ['step' => 'Using existing database', 'status' => 'completed'];
            }

            // Step 3: Setup Database
            $installationSteps[] = ['step' => 'Running database migrations...', 'status' => 'running'];
            
            $database->setup($request);
            $installationSteps[] = ['step' => 'Database setup completed', 'status' => 'completed'];

            // Step 4: Configure Application Settings
            $installationSteps[] = ['step' => 'Configuring application settings...', 'status' => 'running'];
            
            $settings->setup($request);
            $installationSteps[] = ['step' => 'Application settings configured', 'status' => 'completed'];

            // Step 5: Create Admin User
            $installationSteps[] = ['step' => 'Creating admin user...', 'status' => 'running'];
            
            $adminCreated = $settings->createAdminUser($request);
            if ($adminCreated) {
                $installationSteps[] = ['step' => 'Admin user created successfully', 'status' => 'completed'];
            } else {
                $installationSteps[] = ['step' => 'Admin user already exists', 'status' => 'completed'];
            }

            // Step 6: Setup SEO Configuration
            $installationSteps[] = ['step' => 'Configuring SEO settings...', 'status' => 'running'];
            
            $seoSetup->setupBasicSEO($request);
            $installationSteps[] = ['step' => 'SEO configuration completed', 'status' => 'completed'];

            // Step 7: System Optimization
            $installationSteps[] = ['step' => 'Running system optimizations...', 'status' => 'running'];
            
            $optimization = $optimizer->runFullOptimization();
            if ($optimization['success']) {
                $installationSteps[] = ['step' => 'System optimization completed', 'status' => 'completed'];
            } else {
                $installationSteps[] = ['step' => 'System optimization completed with warnings', 'status' => 'warning'];
                $errors[] = $optimization['error'] ?? 'Unknown optimization error';
            }

            // Step 8: Generate Default Content
            $installationSteps[] = ['step' => 'Setting up default content...', 'status' => 'running'];
            
            $settings->setupDefaultContent();
            $installationSteps[] = ['step' => 'Default content created', 'status' => 'completed'];

            // Step 9: Advanced SEO Setup
            $installationSteps[] = ['step' => 'Finalizing SEO optimizations...', 'status' => 'running'];
            
            $seoSetup->setupAdvancedSEO();
            $installationSteps[] = ['step' => 'Advanced SEO setup completed', 'status' => 'completed'];

            // Store installation log
            session()->put('installation_steps', $installationSteps);
            session()->put('installation_errors', $errors);

        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Installation failed: ' . $e->getMessage());
        }

        return redirect('install/complete');
    }

    public function complete(Factory $cache)
    {
        $env = DotenvEditor::load();
        $env->autoBackup(false);
        $env->setKey('APP_ENV', 'production');
        $env->setKey('APP_DEBUG', 'false');
        $env->setKey('APP_INSTALLED', 'true');
        $env->save();

        // Get installation details
        $installationSteps = session()->get('installation_steps', []);
        $installationErrors = session()->get('installation_errors', []);
        
        // Get system information
        $systemInfo = $this->getSystemInfo();
        
        // Get database information
        $databaseInfo = (new Database())->getDatabaseInfo();
        
        // Get optimization checks
        $optimizationChecks = (new SystemOptimizer())->runSystemChecks();

        if (env('APP_ENV') == 'production') {
            return redirect()->route('index');
        }

        return view('install.complete', compact(
            'installationSteps', 
            'installationErrors', 
            'systemInfo', 
            'databaseInfo', 
            'optimizationChecks'
        ));
    }

    public function testDatabase(Request $request, Database $database)
    {
        $result = $database->testConnection($request);
        return response()->json($result);
    }

    public function detectDatabase(Database $database)
    {
        $detected = $database->detectDatabaseConfiguration();
        return response()->json($detected);
    }

    public function optimizeSystem(SystemOptimizer $optimizer)
    {
        $result = $optimizer->runFullOptimization();
        return response()->json($result);
    }

    public function systemStatus(Requirement $requirement, SystemOptimizer $optimizer)
    {
        $status = [
            'requirements' => [
                'extensions' => $requirement->extensions(),
                'directories' => $requirement->directories(),
                'system' => $requirement->systemRequirements(),
                'recommended' => $requirement->recommendedFeatures(),
                'satisfied' => $requirement->satisfied(),
                'all_passed' => $requirement->allChecksPassed(),
            ],
            'optimizations' => $optimizer->runSystemChecks(),
            'suggestions' => $requirement->getOptimizationSuggestions(),
        ];
        
        return response()->json($status);
    }

    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'os' => PHP_OS,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'extensions' => [
                'opcache' => extension_loaded('opcache'),
                'redis' => extension_loaded('redis'),
                'imagick' => extension_loaded('imagick'),
                'gd' => extension_loaded('gd'),
                'curl' => extension_loaded('curl'),
                'zip' => extension_loaded('zip'),
            ]
        ];
    }
}
