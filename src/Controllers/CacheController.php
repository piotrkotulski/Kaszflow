<?php

namespace Kaszflow\Controllers;

use Kaszflow\Core\Request;
use Kaszflow\Core\Response;
use Kaszflow\Services\CacheService;

class CacheController
{
    private $cacheService;
    
    public function __construct()
    {
        $this->cacheService = new CacheService();
    }
    
    /**
     * Panel zarządzania cache
     */
    public function admin(Request $request): Response
    {
        $stats = $this->cacheService->getCacheStats();
        $content = $this->renderView('cache/admin', [
            'stats' => $stats,
            'pageTitle' => 'Zarządzanie Cache - Kaszflow',
            'pageDescription' => 'Panel zarządzania cache danych z API'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Wymuszenie aktualizacji cache
     */
    public function refresh(Request $request): Response
    {
        $type = $request->get('type');
        $results = $this->cacheService->refreshCache($type);
        
        $content = $this->renderView('cache/refresh', [
            'results' => $results,
            'pageTitle' => 'Aktualizacja Cache - Kaszflow'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Czyszczenie starego cache
     */
    public function clean(Request $request): Response
    {
        $deleted = $this->cacheService->cleanOldCache();
        
        $content = $this->renderView('cache/clean', [
            'deleted' => $deleted,
            'pageTitle' => 'Czyszczenie Cache - Kaszflow'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Renderowanie widoku
     */
    private function renderView(string $view, array $data = []): string
    {
        $viewFile = __DIR__ . '/../../resources/views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            return '<h1>Błąd: Widok nie istnieje</h1>';
        }
        
        // Ekstrakcja danych do zmiennych
        extract($data);
        
        // Buforowanie wyjścia
        ob_start();
        include $viewFile;
        return ob_get_clean();
    }
} 