<?php
// Helper function for environment variables
if (!function_exists('env')) {
    function env($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}

return [
    'name' => env('APP_NAME', 'Kaszflow'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'https://kaszflow.pl'),
    
    'timezone' => 'Europe/Warsaw',
    'locale' => 'pl',
    'fallback_locale' => 'en',
    
    'key' => env('APP_KEY', 'base64:your-32-character-key-here'),
    'cipher' => 'AES-256-CBC',
    
    'providers' => [
        // Framework Service Providers
        Kaszflow\Providers\AppServiceProvider::class,
        Kaszflow\Providers\RouteServiceProvider::class,
        
        // Application Service Providers
        Kaszflow\Providers\FinancialServiceProvider::class,
        Kaszflow\Providers\BlogServiceProvider::class,
        Kaszflow\Providers\AnalyticsServiceProvider::class,
    ],
    
    'aliases' => [
        'App' => Kaszflow\Facades\App::class,
        'Config' => Kaszflow\Facades\Config::class,
        'DB' => Kaszflow\Facades\DB::class,
        'Cache' => Kaszflow\Facades\Cache::class,
        'Session' => Kaszflow\Facades\Session::class,
    ],
]; 