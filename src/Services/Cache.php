<?php

namespace Kaszflow\Services;

/**
 * Serwis cache
 */
class Cache
{
    private $redis;
    private $enabled;
    
    public function __construct()
    {
        $this->enabled = $_ENV['REDIS_HOST'] ?? false;
        
        if ($this->enabled) {
            $this->connectRedis();
        }
    }
    
    /**
     * Połączenie z Redis
     */
    private function connectRedis(): void
    {
        try {
            if (!class_exists('Redis')) {
                $this->enabled = false;
                return;
            }
            $this->redis = new \Redis();
            $this->redis->connect(
                $_ENV['REDIS_HOST'] ?? 'localhost',
                $_ENV['REDIS_PORT'] ?? 6379
            );
            
            if ($_ENV['REDIS_PASSWORD'] ?? false) {
                $this->redis->auth($_ENV['REDIS_PASSWORD']);
            }
        } catch (\Exception $e) {
            $this->enabled = false;
        }
    }
    
    /**
     * Pobieranie z cache
     */
    public function get(string $key)
    {
        if (!$this->enabled) {
            return null;
        }
        
        try {
            $value = $this->redis->get($key);
            return $value ? json_decode($value, true) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Zapis do cache
     */
    public function set(string $key, $value, int $ttl = 3600): bool
    {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->setex($key, $ttl, json_encode($value));
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Usuwanie z cache
     */
    public function delete(string $key): bool
    {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->del($key) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Sprawdzenie czy klucz istnieje
     */
    public function has(string $key): bool
    {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->exists($key);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Czyszczenie całego cache
     */
    public function clear(): bool
    {
        if (!$this->enabled) {
            return false;
        }
        
        try {
            return $this->redis->flushDB();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Pobieranie z cache lub wykonanie callback
     */
    public function remember(string $key, callable $callback, int $ttl = 3600)
    {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }
} 