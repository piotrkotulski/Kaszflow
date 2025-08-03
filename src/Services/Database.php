<?php

namespace Kaszflow\Services;

use PDO;
use PDOException;

/**
 * Serwis bazy danych
 */
class Database
{
    private $connection;
    private $config;
    
    public function __construct()
    {
        $this->config = [
            'path' => __DIR__ . '/../../database/kaszflow.db',
            'charset' => 'utf8mb4'
        ];
        
        $this->connect();
    }
    
    /**
     * Połączenie z bazą danych SQLite
     */
    private function connect(): void
    {
        try {
            $dsn = "sqlite:{$this->config['path']}";
            
            $this->connection = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
            // Włącz foreign keys
            $this->connection->exec('PRAGMA foreign_keys = ON');
            
        } catch (PDOException $e) {
            error_log('Błąd połączenia z bazą danych: ' . $e->getMessage());
            $this->connection = null;
        }
    }
    
    /**
     * Wykonanie zapytania SELECT
     */
    public function select(string $query, array $params = []): array
    {
        if (!$this->connection) {
            return [];
        }
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Wykonanie zapytania INSERT
     */
    public function insert(string $table, array $data): int
    {
        if (!$this->connection) {
            return 0;
        }
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute($data);
        
        return $this->connection->lastInsertId();
    }
    
    /**
     * Wykonanie zapytania UPDATE
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        if (!$this->connection) {
            return 0;
        }
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setParts);
        
        $query = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute(array_merge($data, $whereParams));
        
        return $stmt->rowCount();
    }
    
    /**
     * Wykonanie zapytania DELETE
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        if (!$this->connection) {
            return 0;
        }
        $query = "DELETE FROM {$table} WHERE {$where}";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }
    
    /**
     * Pobieranie pojedynczego rekordu
     */
    public function first(string $query, array $params = [])
    {
        if (!$this->connection) {
            return null;
        }
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Sprawdzenie czy tabela istnieje
     */
    public function tableExists(string $table): bool
    {
        if (!$this->connection) {
            return false;
        }
        $query = "SELECT name FROM sqlite_master WHERE type='table' AND name=:table";
        $result = $this->select($query, ['table' => $table]);
        return !empty($result);
    }
    
    /**
     * Wykonanie zapytania SQL
     */
    public function execute(string $query, array $params = []): bool
    {
        if (!$this->connection) {
            return false;
        }
        $stmt = $this->connection->prepare($query);
        return $stmt->execute($params);
    }
    
    /**
     * Pobieranie połączenia PDO
     */
    public function getConnection(): ?PDO
    {
        return $this->connection;
    }
} 