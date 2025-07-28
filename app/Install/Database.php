<?php

namespace App\Install;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class Database
{
    public function setup($data)
    {
        $this->checkDatabaseConnection($data);
        $this->setEnvVariables($data);
        $this->migrateDatabase();
        $this->seedDatabase();
        
        return [
            'success' => true,
            'message' => 'Database setup completed successfully'
        ];
    }

    public function testConnection($data)
    {
        try {
            $this->setupDatabaseConnectionConfig($data);
            DB::connection('mysql')->reconnect();
            DB::connection('mysql')->getPdo();
            
            return [
                'success' => true,
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    public function detectDatabaseConfiguration()
    {
        $detectedConfig = [];
        
        // Try to detect common database configurations
        $commonHosts = ['localhost', '127.0.0.1', 'mysql', 'mariadb'];
        $commonPorts = [3306, 3307];
        
        foreach ($commonHosts as $host) {
            foreach ($commonPorts as $port) {
                try {
                    $testData = (object) [
                        'host' => $host,
                        'port' => $port,
                        'name' => '',
                        'username' => 'root',
                        'password' => ''
                    ];
                    
                    if ($this->testConnectionSilent($testData)) {
                        $detectedConfig = [
                            'host' => $host,
                            'port' => $port,
                            'detected' => true
                        ];
                        break 2;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        
        return $detectedConfig;
    }

    public function createDatabaseIfNotExists($data)
    {
        try {
            // Connect without specifying database
            $tempData = clone $data;
            $tempData->name = '';
            
            $this->setupDatabaseConnectionConfig($tempData);
            DB::connection('mysql')->reconnect();
            
            // Check if database exists using information_schema (safer and supports parameter binding)
            $databases = DB::connection('mysql')->select('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?', [$data->name]);
            
            if (empty($databases)) {
                // Create database (database name is pre-validated to contain only safe characters)
                $safeDatabaseName = preg_replace('/[^a-zA-Z0-9_]/', '', $data->name); // Extra safety
                DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$safeDatabaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                return [
                    'success' => true,
                    'message' => 'Database created successfully',
                    'created' => true
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Database already exists',
                'created' => false
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create database: ' . $e->getMessage(),
                'created' => false
            ];
        }
    }

    private function checkDatabaseConnection($data)
    {
        $this->setupDatabaseConnectionConfig($data);

        try {
            DB::connection('mysql')->reconnect();
            DB::connection('mysql')->getPdo();
        } catch (\Exception $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    private function setupDatabaseConnectionConfig($data)
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $data->host,
            'database.connections.mysql.port' => $data->port ?? 3306,
            'database.connections.mysql.database' => $data->name,
            'database.connections.mysql.username' => $data->username,
            'database.connections.mysql.password' => $data->password,
            'database.connections.mysql.charset' => 'utf8mb4',
            'database.connections.mysql.collation' => 'utf8mb4_unicode_ci',
            'database.connections.mysql.strict' => true,
            'database.connections.mysql.engine' => null,
        ]);
    }

    private function setEnvVariables($data)
    {
        $env = DotenvEditor::load();
        $env->autoBackup(false);

        $env->setKey('DB_CONNECTION', 'mysql');
        $env->setKey('DB_HOST', $data->host);
        $env->setKey('DB_PORT', $data->port ?? 3306);
        $env->setKey('DB_DATABASE', $data->name);
        $env->setKey('DB_USERNAME', $data->username);
        $env->setKey('DB_PASSWORD', $data->password);
        
        // Set additional database configurations
        $env->setKey('DB_CHARSET', 'utf8mb4');
        $env->setKey('DB_COLLATION', 'utf8mb4_unicode_ci');
        
        // License key (if provided)
        if (isset($data->license_key) && !empty($data->license_key)) {
            $env->setKey('LICENSE_KEY', $data->license_key);
        }
        
        $env->save();
    }

    private function migrateDatabase()
    {
        try {
            // Fresh migration with seeding
            Artisan::call('migrate:fresh', ['--force' => true]);
            
            return [
                'success' => true,
                'message' => 'Database migrations completed successfully'
            ];
        } catch (\Exception $e) {
            // Try regular migration if fresh fails
            try {
                Artisan::call('migrate', ['--force' => true]);
                return [
                    'success' => true,
                    'message' => 'Database migrations completed successfully'
                ];
            } catch (\Exception $e2) {
                throw new \Exception('Migration failed: ' . $e2->getMessage());
            }
        }
    }

    private function seedDatabase()
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            
            return [
                'success' => true,
                'message' => 'Database seeding completed successfully'
            ];
        } catch (\Exception $e) {
            // Seeding is optional, don't fail the installation
            return [
                'success' => false,
                'message' => 'Database seeding failed (optional): ' . $e->getMessage()
            ];
        }
    }

    private function testConnectionSilent($data)
    {
        try {
            $this->setupDatabaseConnectionConfig($data);
            DB::connection('mysql')->reconnect();
            DB::connection('mysql')->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function optimizeDatabase()
    {
        try {
            // Optimize database tables
            $tables = DB::select('SHOW TABLES');
            $databaseName = config('database.connections.mysql.database');
            
            foreach ($tables as $table) {
                $tableName = $table->{"Tables_in_{$databaseName}"};
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
            }
            
            return [
                'success' => true,
                'message' => 'Database optimization completed'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Database optimization failed: ' . $e->getMessage()
            ];
        }
    }

    public function getDatabaseInfo()
    {
        try {
            $version = DB::select('SELECT VERSION() as version')[0]->version;
            $size = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb'
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [config('database.connections.mysql.database')])[0]->size_mb ?? 0;
            
            $tables = DB::select("
                SELECT COUNT(*) as count 
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [config('database.connections.mysql.database')])[0]->count ?? 0;

            return [
                'version' => $version,
                'size_mb' => $size,
                'tables_count' => $tables,
                'connection' => 'MySQL/MariaDB'
            ];
        } catch (\Exception $e) {
            return [
                'version' => 'Unknown',
                'size_mb' => 0,
                'tables_count' => 0,
                'connection' => 'Failed to connect'
            ];
        }
    }

    public function validateDatabaseRequirements($data)
    {
        $errors = [];
        
        if (empty($data->host)) {
            $errors[] = 'Database host is required';
        }
        
        if (empty($data->name)) {
            $errors[] = 'Database name is required';
        }
        
        if (empty($data->username)) {
            $errors[] = 'Database username is required';
        }
        
        // Validate database name format
        if (!empty($data->name) && !preg_match('/^[a-zA-Z0-9_]+$/', $data->name)) {
            $errors[] = 'Database name can only contain letters, numbers, and underscores';
        }
        
        // Validate port
        if (!empty($data->port) && (!is_numeric($data->port) || $data->port < 1 || $data->port > 65535)) {
            $errors[] = 'Database port must be a number between 1 and 65535';
        }
        
        return $errors;
    }

    public function backupDatabase()
    {
        try {
            $databaseName = config('database.connections.mysql.database');
            $backupPath = storage_path('app/backups');
            
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            $filename = 'backup_' . $databaseName . '_' . date('Y_m_d_H_i_s') . '.sql';
            $filepath = $backupPath . '/' . $filename;
            
            $command = sprintf(
                'mysqldump -h %s -P %s -u %s -p%s %s > %s',
                escapeshellarg(config('database.connections.mysql.host')),
                escapeshellarg(config('database.connections.mysql.port')),
                escapeshellarg(config('database.connections.mysql.username')),
                escapeshellarg(config('database.connections.mysql.password')),
                escapeshellarg($databaseName),
                escapeshellarg($filepath)
            );
            
            $result = $this->executeCommand($command);
            
            if ($result && file_exists($filepath)) {
                return [
                    'success' => true,
                    'message' => 'Database backup created successfully',
                    'file' => $filename
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Database backup failed - mysqldump not available or insufficient permissions'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Database backup failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Safely execute command with fallback methods
     */
    private function executeCommand($command)
    {
        // Check if exec functions are available
        $disabledFunctions = explode(',', ini_get('disable_functions'));
        $disabledFunctions = array_map('trim', $disabledFunctions);
        
        // Try exec first (best for commands with output)
        if (!in_array('exec', $disabledFunctions) && function_exists('exec')) {
            $output = [];
            $returnVar = 0;
            @exec($command . ' 2>/dev/null', $output, $returnVar);
            return $returnVar === 0;
        }
        
        // Try shell_exec as fallback
        if (!in_array('shell_exec', $disabledFunctions) && function_exists('shell_exec')) {
            $output = @shell_exec($command . ' 2>/dev/null');
            return $output !== null;
        }
        
        // Try system as another fallback
        if (!in_array('system', $disabledFunctions) && function_exists('system')) {
            ob_start();
            $returnVar = @system($command . ' 2>/dev/null');
            ob_get_clean();
            return $returnVar !== false;
        }
        
        // If all methods fail, return false but don't break installation
        return false;
    }
}
