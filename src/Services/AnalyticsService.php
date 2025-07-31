<?php

namespace Kaszflow\Services;

use Kaszflow\Services\Database;

/**
 * Serwis analityki i śledzenia
 */
class AnalyticsService
{
    private $database;
    private $logger;
    
    public function __construct()
    {
        $this->database = new Database();
        $this->logger = new Logger();
    }
    
    /**
     * Śledzenie wyświetlenia strony
     */
    public function trackPageView(string $page, array $data = []): void
    {
        try {
            $this->database->insert('page_views', [
                'page' => $page,
                'user_ip' => $this->getClientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
                'session_id' => session_id(),
                'data' => json_encode($data),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Błąd śledzenia wyświetlenia strony: ' . $e->getMessage());
        }
    }
    
    /**
     * Śledzenie kliknięcia w link afiliacyjny
     */
    public function trackAffiliateClick(int $productId, string $bankName, array $userData = []): void
    {
        try {
            $this->database->insert('affiliate_clicks', [
                'product_id' => $productId,
                'bank_name' => $bankName,
                'user_ip' => $this->getClientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
                'click_time' => date('Y-m-d H:i:s'),
                'session_id' => session_id(),
                'user_data' => json_encode($userData)
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Błąd śledzenia kliknięcia afiliacyjnego: ' . $e->getMessage());
        }
    }
    
    /**
     * Śledzenie konwersji
     */
    public function trackConversion(int $productId, string $bankName, float $amount = 0): void
    {
        try {
            $this->database->insert('conversions', [
                'product_id' => $productId,
                'bank_name' => $bankName,
                'user_ip' => $this->getClientIp(),
                'session_id' => session_id(),
                'amount' => $amount,
                'conversion_time' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Błąd śledzenia konwersji: ' . $e->getMessage());
        }
    }
    
    /**
     * Śledzenie wyszukiwania
     */
    public function trackSearch(string $query, array $filters = []): void
    {
        try {
            $this->database->insert('searches', [
                'query' => $query,
                'filters' => json_encode($filters),
                'user_ip' => $this->getClientIp(),
                'session_id' => session_id(),
                'search_time' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Błąd śledzenia wyszukiwania: ' . $e->getMessage());
        }
    }
    
    /**
     * Pobieranie statystyk
     */
    public function getStats(string $period = '30days'): array
    {
        $stats = [];
        
        try {
            // Statystyki wyświetleń stron
            $stats['page_views'] = $this->getPageViewStats($period);
            
            // Statystyki kliknięć afiliacyjnych
            $stats['affiliate_clicks'] = $this->getAffiliateClickStats($period);
            
            // Statystyki konwersji
            $stats['conversions'] = $this->getConversionStats($period);
            
            // Statystyki wyszukiwań
            $stats['searches'] = $this->getSearchStats($period);
            
        } catch (\Exception $e) {
            $this->logger->error('Błąd pobierania statystyk: ' . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Statystyki wyświetleń stron
     */
    private function getPageViewStats(string $period): array
    {
        $dateFilter = $this->getDateFilter($period);
        
        $query = "SELECT page, COUNT(*) as views 
                  FROM page_views 
                  WHERE created_at >= :date_filter 
                  GROUP BY page 
                  ORDER BY views DESC";
        
        return $this->database->select($query, ['date_filter' => $dateFilter]);
    }
    
    /**
     * Statystyki kliknięć afiliacyjnych
     */
    private function getAffiliateClickStats(string $period): array
    {
        $dateFilter = $this->getDateFilter($period);
        
        $query = "SELECT bank_name, COUNT(*) as clicks 
                  FROM affiliate_clicks 
                  WHERE click_time >= :date_filter 
                  GROUP BY bank_name 
                  ORDER BY clicks DESC";
        
        return $this->database->select($query, ['date_filter' => $dateFilter]);
    }
    
    /**
     * Statystyki konwersji
     */
    private function getConversionStats(string $period): array
    {
        $dateFilter = $this->getDateFilter($period);
        
        $query = "SELECT bank_name, COUNT(*) as conversions, SUM(amount) as total_amount 
                  FROM conversions 
                  WHERE conversion_time >= :date_filter 
                  GROUP BY bank_name 
                  ORDER BY conversions DESC";
        
        return $this->database->select($query, ['date_filter' => $dateFilter]);
    }
    
    /**
     * Statystyki wyszukiwań
     */
    private function getSearchStats(string $period): array
    {
        $dateFilter = $this->getDateFilter($period);
        
        $query = "SELECT query, COUNT(*) as searches 
                  FROM searches 
                  WHERE search_time >= :date_filter 
                  GROUP BY query 
                  ORDER BY searches DESC 
                  LIMIT 10";
        
        return $this->database->select($query, ['date_filter' => $dateFilter]);
    }
    
    /**
     * Pobieranie filtra daty
     */
    private function getDateFilter(string $period): string
    {
        switch ($period) {
            case '7days':
                return date('Y-m-d H:i:s', strtotime('-7 days'));
            case '30days':
                return date('Y-m-d H:i:s', strtotime('-30 days'));
            case '90days':
                return date('Y-m-d H:i:s', strtotime('-90 days'));
            default:
                return date('Y-m-d H:i:s', strtotime('-30 days'));
        }
    }
    
    /**
     * Pobieranie IP klienta
     */
    private function getClientIp(): string
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Analiza trendów wyszukiwań
     */
    public function analyzeSearchTrends(): array
    {
        try {
            $query = "SELECT query, COUNT(*) as count, DATE(search_time) as date 
                      FROM searches 
                      WHERE search_time >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                      GROUP BY query, DATE(search_time) 
                      ORDER BY date DESC, count DESC";
            
            $results = $this->database->select($query);
            
            // Grupowanie wyników
            $trends = [];
            foreach ($results as $row) {
                $trends[$row['query']][] = [
                    'date' => $row['date'],
                    'count' => $row['count']
                ];
            }
            
            return $trends;
            
        } catch (\Exception $e) {
            $this->logger->error('Błąd analizy trendów: ' . $e->getMessage());
            return [];
        }
    }
} 