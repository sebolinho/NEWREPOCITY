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
            'Curl PHP Extension' => extension_loaded('curl'),
        ];
    }

    public function directories()
    {
        return [
            '.env file writable' => is_writable(base_path('.env')),
            'storage/ writable' => is_writable(storage_path()),
            'bootstrap/cache/ writable' => is_writable(base_path('bootstrap/cache')),
            'public/ writable' => is_writable(public_path()),
        ];
    }

    public function serverRequirements()
    {
        return [
            'Web Server' => $this->checkWebServer(),
            'Composer Installed' => $this->checkComposer(),
            'Node.js Available' => $this->checkNodeJs(),
            'NPM Available' => $this->checkNpm(),
        ];
    }

    public function optionalFeatures()
    {
        return [
            'Redis Extension' => extension_loaded('redis'),
            'Imagick Extension' => extension_loaded('imagick'),
            'FFmpeg Available' => $this->checkFFmpeg(),
            'ImageMagick Available' => $this->checkImageMagick(),
        ];
    }

    public function getAllRequirements()
    {
        return [
            'PHP Extensions' => $this->extensions(),
            'Directory Permissions' => $this->directories(),
            'Server Requirements' => $this->serverRequirements(),
            'Optional Features' => $this->optionalFeatures(),
        ];
    }

    public function satisfied()
    {
        $extensions = collect($this->extensions())->every(function ($item) {
            return $item;
        });

        $directories = collect($this->directories())->every(function ($item) {
            return $item;
        });

        $server = collect($this->serverRequirements())->every(function ($item) {
            return $item;
        });

        return $extensions && $directories && $server;
    }

    public function criticalRequirementsSatisfied()
    {
        return collect($this->extensions())
            ->merge($this->directories())
            ->every(function ($item) {
                return $item;
            });
    }

    private function checkWebServer()
    {
        return isset($_SERVER['SERVER_SOFTWARE']) || php_sapi_name() === 'cli-server';
    }

    private function checkComposer()
    {
        if (File::exists(base_path('composer.json')) && File::exists(base_path('vendor'))) {
            return true;
        }
        
        exec('composer --version 2>&1', $output, $return_var);
        return $return_var === 0;
    }

    private function checkNodeJs()
    {
        exec('node --version 2>&1', $output, $return_var);
        return $return_var === 0;
    }

    private function checkNpm()
    {
        exec('npm --version 2>&1', $output, $return_var);
        return $return_var === 0;
    }

    private function checkFFmpeg()
    {
        exec('ffmpeg -version 2>&1', $output, $return_var);
        return $return_var === 0;
    }

    private function checkImageMagick()
    {
        exec('convert -version 2>&1', $output, $return_var);
        return $return_var === 0;
    }
}
