<?php

namespace Kaszflow\Core;

/**
 * Klasa do obsługi odpowiedzi HTTP
 */
class Response
{
    private $content;
    private $statusCode;
    private $headers;
    
    public function __construct($content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = array_merge([
            'Content-Type' => 'text/html; charset=UTF-8'
        ], $headers);
    }
    
    /**
     * Ustawienie zawartości
     */
    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * Ustawienie kodu statusu
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }
    
    /**
     * Dodanie nagłówka
     */
    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    /**
     * Ustawienie nagłówków
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }
    
    /**
     * Ustawienie typu zawartości JSON
     */
    public function json($data): self
    {
        $this->headers['Content-Type'] = 'application/json';
        $this->content = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $this;
    }
    
    /**
     * Przekierowanie
     */
    public function redirect(string $url, int $statusCode = 302): self
    {
        $this->headers['Location'] = $url;
        $this->statusCode = $statusCode;
        return $this;
    }
    
    /**
     * Wysłanie odpowiedzi
     */
    public function send(): void
    {
        // Ustawienie kodu statusu
        http_response_code($this->statusCode);
        
        // Wysłanie nagłówków
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        // Wysłanie zawartości
        echo $this->content;
    }
    
    /**
     * Pobieranie zawartości
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Pobieranie kodu statusu
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    /**
     * Pobieranie nagłówków
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
} 