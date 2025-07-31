<?php

/**
 * Kaszflow - Serwis Porównywania Produktów Finansowych
 * 
 * Główny plik wejściowy aplikacji
 */

// Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Error handling
if ($_ENV['APP_DEBUG'] ?? false) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session
session_start();

// Initialize application
$app = new Kaszflow\Core\Application();

// Load routes
$router = $app->getRouter();
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

// Handle request
$app->handle(); 