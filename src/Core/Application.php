<?php

namespace Kaszflow\Core;

use Kaszflow\Services\Router;
use Kaszflow\Services\Database;
use Kaszflow\Services\Cache;
use Kaszflow\Services\Logger;

/**
 * Główna klasa aplikacji
 */
class Application
{
    private $router;
    private $database;
    private $cache;
    private $logger;
    
    public function __construct()
    {
        $this->initializeServices();
        $this->loadConfiguration();
    }
    
    /**
     * Inicjalizacja serwisów
     */
    private function initializeServices(): void
    {
        $this->router = new Router();
        $this->database = new Database();
        $this->cache = new Cache();
        $this->logger = new Logger();
    }
    
    /**
     * Ładowanie konfiguracji
     */
    private function loadConfiguration(): void
    {
        $config = require __DIR__ . '/../../config/app.php';
        
        // Ustawienie konfiguracji aplikacji
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * Obsługa żądania HTTP
     */
    public function handle(): void
    {
        try {
            $request = new Request();
            $response = $this->router->dispatch($request);
            $response->send();
        } catch (\Exception $e) {
            $this->logger->error('Application error: ' . $e->getMessage());
            
            if ($_ENV['APP_DEBUG'] ?? false) {
                throw $e;
            } else {
                http_response_code(500);
                echo 'Wystąpił błąd aplikacji.';
            }
        }
    }
    
    /**
     * Pobieranie instancji routera
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
    
    /**
     * Pobieranie instancji bazy danych
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }
    
    /**
     * Pobieranie instancji cache
     */
    public function getCache(): Cache
    {
        return $this->cache;
    }
    
    /**
     * Pobieranie instancji loggera
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }
} 