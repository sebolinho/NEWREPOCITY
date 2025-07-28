<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectToInstaller;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Install\Requirement;
use App\Install\Database;
use App\Install\AppSettings;
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
        $requirements = $requirement->getAllRequirements();
        $satisfied = $requirement->criticalRequirementsSatisfied();
        
        return view('install.index', compact('requirement', 'requirements', 'satisfied'));
    }

    public function config(Requirement $requirement)
    {
        if (!$requirement->criticalRequirementsSatisfied()) {
            return redirect()->route('install.index')->with('error', 'Please ensure all critical requirements are met before proceeding.');
        }
        
        return view('install.config', compact('requirement'));
    }

    public function store(Request $request, Database $database, AppSettings $settings)
    {
        // Validate the form data
        $validator = Validator::make($request->all(), [
            'host' => 'required|string',
            'port' => 'nullable|numeric|min:1|max:65535',
            'name' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
            'license_key' => 'nullable|string',
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|url',
            'admin_name' => 'nullable|string|max:255',
            'admin_email' => 'nullable|email|max:255',
            'admin_password' => 'nullable|string|min:8',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|numeric|min:1|max:65535',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl,none',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        try {
            Log::info('Starting installation process...');

            // Setup database
            $result = $database->setup((object) $request->all());
            if (!$result['success']) {
                throw new Exception('Database setup failed');
            }

            // Setup application settings
            $settings->setup((object) $request->all());

            Log::info('Installation completed successfully');

            return redirect()->route('install.complete')->with('success', 'Installation completed successfully!');

        } catch (Exception $e) {
            Log::error('Installation failed: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Installation failed: ' . $e->getMessage());
        }
    }

    public function complete(Factory $cache)
    {
        try {
            // Set final environment variables
            $env = DotenvEditor::load();
            $env->autoBackup(false);
            $env->setKey('APP_ENV', 'production');
            $env->setKey('APP_DEBUG', 'false');
            $env->save();

            // Clear any installer cache
            $cache->forget('installer_*');

            Log::info('Installation finalization completed');

            // Check if environment is properly set to production
            if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
                return redirect()->route('index')->with('success', 'Installation completed! Welcome to your streaming platform.');
            }

            return view('install.complete');

        } catch (Exception $e) {
            Log::error('Installation finalization failed: ' . $e->getMessage());
            return view('install.complete')->with('error', 'Installation completed but finalization had issues. Please check your logs.');
        }
    }

    public function checkStatus()
    {
        // Ajax endpoint to check installation status
        try {
            $requirement = new Requirement();
            $satisfied = $requirement->criticalRequirementsSatisfied();
            
            return response()->json([
                'success' => true,
                'satisfied' => $satisfied,
                'requirements' => $requirement->getAllRequirements()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
