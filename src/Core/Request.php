<?php

namespace Kaszflow\Core;

/**
 * Klasa do obsługi żądań HTTP
 */
class Request
{
    private $get;
    private $post;
    private $server;
    private $files;
    private $pathParams = [];
    
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;
    }
    
    /**
     * Pobieranie metody HTTP
     */
    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }
    
    /**
     * Pobieranie ścieżki URL
     */
    public function getPath(): string
    {
        $path = $this->server['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        
        return $path;
    }
    
    /**
     * Pobieranie parametrów GET
     */
    public function get(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }
    
    /**
     * Pobieranie parametrów POST
     */
    public function post(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }
    
    /**
     * Pobieranie wszystkich parametrów POST
     */
    public function all(): array
    {
        return $this->post;
    }
    
    /**
     * Sprawdzenie czy żądanie jest AJAX
     */
    public function isAjax(): bool
    {
        return isset($this->server['HTTP_X_REQUESTED_WITH']) && 
               strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Pobieranie nagłówków
     */
    public function getHeader(string $name): ?string
    {
        $headerName = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$headerName] ?? null;
    }
    
    /**
     * Pobieranie plików
     */
    public function getFile(string $key)
    {
        return $this->files[$key] ?? null;
    }
    
    /**
     * Pobieranie IP klienta
     */
    public function getClientIp(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '';
    }
    
    /**
     * Pobieranie User Agent
     */
    public function getUserAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }
    
    /**
     * Pobieranie parametrów ścieżki
     */
    public function getPathParams(): array
    {
        return $this->pathParams;
    }
    
    /**
     * Ustawianie parametrów ścieżki
     */
    public function setPathParams(array $params): void
    {
        $this->pathParams = $params;
    }
    
    /**
     * Pobieranie parametru ścieżki
     */
    public function getPathParam(string $key, $default = null)
    {
        return $this->pathParams[$key] ?? $default;
    }
} 