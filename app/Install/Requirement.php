<?php

namespace App\Install;

use Illuminate\Support\Facades\File;

class Requirement
{
    public function extensions()
    {
        return [
            'PHP >= 8.1' => version_compare(phpversion(), '8.1.0', '>='),
            'Intl PHP Extension' => extension_loaded('intl'),
            'OpenSSL PHP Extension' => extension_loaded('openssl'),
            'PDO PHP Extension' => extension_loaded('pdo'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'Mbstring PHP Extension' => extension_loaded('mbstring'),
            'Tokenizer PHP Extension' => extension_loaded('tokenizer'),
            'XML PHP Extension' => extension_loaded('xml'),
            'Ctype PHP Extension' => extension_loaded('ctype'),
            'JSON PHP Extension' => extension_loaded('json'),
            'BCMath PHP Extension' => extension_loaded('bcmath'),
            'Fileinfo PHP Extension' => extension_loaded('fileinfo'),
            'GD PHP Extension' => extension_loaded('gd'),
            'Zip PHP Extension' => extension_loaded('zip'),
            'Curl PHP Extension' => extension_loaded('curl'),
        ];
    }

    public function directories()
    {
        return [
            '.env' => $this->checkEnvWritable(),
            'storage' => is_writable(storage_path()),
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework' => is_writable(storage_path('framework')),
            'storage/logs' => is_writable(storage_path('logs')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            'public' => is_writable(public_path()),
        ];
    }

    public function systemRequirements()
    {
        return [
            'Memory Limit >= 256M' => $this->checkMemoryLimit(),
            'Max Execution Time >= 120s' => $this->checkExecutionTime(),
            'Upload Max Size >= 32M' => $this->checkUploadLimit(),
            'Post Max Size >= 32M' => $this->checkPostLimit(),
            'Composer Available' => $this->checkComposer(),
            'Node.js Available' => $this->checkNodejs(),
            'NPM Available' => $this->checkNpm(),
        ];
    }

    public function recommendedFeatures()
    {
        return [
            'Redis Extension' => extension_loaded('redis'),
            'OPcache Extension' => extension_loaded('opcache'),
            'Imagick Extension' => extension_loaded('imagick'),
            'Exif Extension' => extension_loaded('exif'),
            'FFMpeg Available' => $this->checkFFMpeg(),
            'ImageMagick Available' => $this->checkImageMagick(),
        ];
    }

    public function satisfied()
    {
        return collect($this->extensions())
            ->merge($this->directories())
            ->merge($this->systemRequirements())
            ->every(function ($item) {
                return $item;
            });
    }

    public function allChecksPassed()
    {
        return $this->satisfied() && collect($this->recommendedFeatures())->filter()->count() >= 3;
    }

    private function checkEnvWritable()
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return is_writable(base_path()) && File::copy(base_path('.env.example'), $envPath);
        }
        return is_writable($envPath);
    }

    private function checkMemoryLimit()
    {
        $limit = ini_get('memory_limit');
        if ($limit == -1) return true;
        
        $limit = $this->convertToBytes($limit);
        return $limit >= 256 * 1024 * 1024; // 256MB
    }

    private function checkExecutionTime()
    {
        $limit = ini_get('max_execution_time');
        return $limit == 0 || $limit >= 120;
    }

    private function checkUploadLimit()
    {
        $limit = ini_get('upload_max_filesize');
        $limit = $this->convertToBytes($limit);
        return $limit >= 32 * 1024 * 1024; // 32MB
    }

    private function checkPostLimit()
    {
        $limit = ini_get('post_max_size');
        $limit = $this->convertToBytes($limit);
        return $limit >= 32 * 1024 * 1024; // 32MB
    }

    private function checkComposer()
    {
        return shell_exec('composer --version') !== null;
    }

    private function checkNodejs()
    {
        return shell_exec('node --version') !== null;
    }

    private function checkNpm()
    {
        return shell_exec('npm --version') !== null;
    }

    private function checkFFMpeg()
    {
        return shell_exec('ffmpeg -version') !== null;
    }

    private function checkImageMagick()
    {
        return shell_exec('convert -version') !== null;
    }

    private function convertToBytes($value)
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g':
                $value *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $value *= 1024 * 1024;
                break;
            case 'k':
                $value *= 1024;
                break;
        }

        return $value;
    }

    public function getOptimizationSuggestions()
    {
        $suggestions = [];
        
        if (!extension_loaded('opcache')) {
            $suggestions[] = 'Install OPcache for better PHP performance';
        }
        
        if (!extension_loaded('redis')) {
            $suggestions[] = 'Install Redis for caching and sessions';
        }
        
        if ($this->convertToBytes(ini_get('memory_limit')) < 512 * 1024 * 1024) {
            $suggestions[] = 'Increase PHP memory_limit to 512M or higher';
        }
        
        if (!$this->checkFFMpeg()) {
            $suggestions[] = 'Install FFMpeg for video processing capabilities';
        }
        
        return $suggestions;
    }
}
