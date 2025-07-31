<?php

namespace Kaszflow\Services;

/**
 * Serwis loggera
 */
class Logger
{
    private $logFile;
    
    public function __construct()
    {
        $this->logFile = __DIR__ . '/../../storage/logs/app.log';
        
        // Tworzenie katalogu logów jeśli nie istnieje
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Logowanie informacji
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }
    
    /**
     * Logowanie ostrzeżeń
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }
    
    /**
     * Logowanie błędów
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }
    
    /**
     * Logowanie debug
     */
    public function debug(string $message, array $context = []): void
    {
        if ($_ENV['APP_DEBUG'] ?? false) {
            $this->log('DEBUG', $message, $context);
        }
    }
    
    /**
     * Główna metoda logowania
     */
    private function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logEntry = "[{$timestamp}] {$level}: {$message}{$contextStr}" . PHP_EOL;
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Pobieranie logów
     */
    public function getLogs(int $lines = 100): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $logs = file($this->logFile);
        return array_slice($logs, -$lines);
    }
    
    /**
     * Czyszczenie logów
     */
    public function clear(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }
    
    /**
     * Rozmiar pliku logów
     */
    public function getSize(): int
    {
        return file_exists($this->logFile) ? filesize($this->logFile) : 0;
    }
} 