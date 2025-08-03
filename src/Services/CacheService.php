<?php

namespace Kaszflow\Services;

use Kaszflow\Services\FinancialApiService;

class CacheService
{
    private $apiService;
    private $cacheDir;
    private $cacheExpiry = 86400; // 24 godziny w sekundach
    
    public function __construct()
    {
        $this->apiService = new FinancialApiService();
        $this->cacheDir = __DIR__ . '/../../data/cache';
        
        // Utwórz katalog cache jeśli nie istnieje
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Pobieranie danych z cache lub API
     */
    public function getData(string $type, array $params = []): array
    {
        $cacheKey = $this->generateCacheKey($type, $params);
        $cacheFile = $this->cacheDir . '/' . $cacheKey . '.json';
        
        // Sprawdź czy cache istnieje i jest aktualny
        if ($this->isCacheValid($cacheFile)) {
            $cachedData = $this->loadFromCache($cacheFile);
            if ($cachedData !== false) {
                return $cachedData;
            }
        }
        
        // Pobierz dane z API
        $data = $this->fetchFromApi($type, $params);
        
        // Zapisz do cache
        $this->saveToCache($cacheFile, $data);
        
        return $data;
    }
    
    /**
     * Wymuszenie aktualizacji cache
     */
    public function refreshCache(string $type = null): array
    {
        $types = $type ? [$type] : ['loans', 'accounts', 'deposits', 'mortgages'];
        $results = [];
        
        foreach ($types as $type) {
            $cacheKey = $this->generateCacheKey($type);
            $cacheFile = $this->cacheDir . '/' . $cacheKey . '.json';
            
            // Pobierz dane z API
            $data = $this->fetchFromApi($type);
            
            // Zapisz do cache
            $this->saveToCache($cacheFile, $data);
            
            $results[$type] = [
                'status' => 'updated',
                'count' => is_array($data) ? count($data) : 0,
                'timestamp' => time()
            ];
        }
        
        return $results;
    }
    
    /**
     * Sprawdzenie czy cache jest aktualny
     */
    private function isCacheValid(string $cacheFile): bool
    {
        if (!file_exists($cacheFile)) {
            return false;
        }
        
        $fileTime = filemtime($cacheFile);
        $currentTime = time();
        
        return ($currentTime - $fileTime) < $this->cacheExpiry;
    }
    
    /**
     * Ładowanie danych z cache
     */
    private function loadFromCache(string $cacheFile): array
    {
        if (!file_exists($cacheFile)) {
            return [];
        }
        
        $content = file_get_contents($cacheFile);
        $data = json_decode($content, true);
        
        return $data !== null ? $data : [];
    }
    
    /**
     * Zapisywanie danych do cache
     */
    private function saveToCache(string $cacheFile, array $data): bool
    {
        $content = json_encode($data, JSON_PRETTY_PRINT);
        return file_put_contents($cacheFile, $content) !== false;
    }
    
    /**
     * Pobieranie danych z API
     */
    private function fetchFromApi(string $type, array $params = []): array
    {
        switch ($type) {
            case 'loans':
                return $this->apiService->getLoans($params);
            case 'accounts':
                return $this->apiService->getPersonalAccounts();
            case 'deposits':
                return $this->apiService->getDeposits($params);
            case 'mortgages':
                return $this->apiService->getMortgages($params);
            default:
                return [];
        }
    }
    
    /**
     * Generowanie klucza cache
     */
    private function generateCacheKey(string $type, array $params = []): string
    {
        $key = $type;
        if (!empty($params)) {
            $key .= '_' . md5(serialize($params));
        }
        return $key;
    }
    
    /**
     * Pobieranie statystyk cache
     */
    public function getCacheStats(): array
    {
        $stats = [];
        $files = glob($this->cacheDir . '/*.json');
        
        foreach ($files as $file) {
            $filename = basename($file, '.json');
            $fileTime = filemtime($file);
            $fileSize = filesize($file);
            
            // Pobierz liczbę produktów z pliku cache
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            $productCount = is_array($data) ? count($data) : 0;
            
            $stats[$filename] = [
                'last_updated' => date('Y-m-d H:i:s', $fileTime),
                'age_hours' => round((time() - $fileTime) / 3600, 2),
                'size_kb' => round($fileSize / 1024, 2),
                'is_valid' => $this->isCacheValid($file),
                'count' => $productCount
            ];
        }
        
        return $stats;
    }
    
    /**
     * Czyszczenie starego cache
     */
    public function cleanOldCache(): int
    {
        $deleted = 0;
        $files = glob($this->cacheDir . '/*.json');
        
        foreach ($files as $file) {
            if (!$this->isCacheValid($file)) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }
        
        return $deleted;
    }
} 