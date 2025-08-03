<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Kaszflow\Services\CacheService;

// Ustawienie strefy czasowej
date_default_timezone_set('Europe/Warsaw');

// Ładowanie zmiennych środowiskowych
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Inicjalizacja serwisu cache
$cacheService = new CacheService();

echo "=== Aktualizacja cache Kaszflow ===\n";
echo "Data: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Aktualizacja wszystkich typów danych
    $results = $cacheService->refreshCache();
    
    echo "Wyniki aktualizacji:\n";
    foreach ($results as $type => $result) {
        echo "- {$type}: {$result['count']} produktów zaktualizowanych\n";
    }
    
    // Czyszczenie starego cache
    $deleted = $cacheService->cleanOldCache();
    echo "\nUsunięto {$deleted} starych plików cache.\n";
    
    echo "\n✅ Aktualizacja zakończona pomyślnie!\n";
    
} catch (Exception $e) {
    echo "❌ Błąd podczas aktualizacji: " . $e->getMessage() . "\n";
    exit(1);
} 