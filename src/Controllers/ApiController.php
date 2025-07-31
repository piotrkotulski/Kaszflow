<?php

namespace Kaszflow\Controllers;

use Kaszflow\Core\Request;
use Kaszflow\Core\Response;
use Kaszflow\Services\FinancialApiService;
use Kaszflow\Services\Cache;
use Kaszflow\Services\AnalyticsService;

/**
 * Kontroler API dla produktów finansowych
 */
class ApiController
{
    private $apiService;
    private $cache;
    private $analytics;
    
    public function __construct()
    {
        $this->apiService = new FinancialApiService();
        $this->cache = new Cache();
        $this->analytics = new AnalyticsService();
    }
    
    /**
     * API kredytów gotówkowych
     */
    public function getLoans(Request $request): Response
    {
        $filters = $this->parseFilters($request);
        
        $cacheKey = 'api_loans_' . md5(serialize($filters));
        $loans = $this->cache->remember($cacheKey, function() use ($filters) {
            return $this->apiService->getLoans($filters);
        }, 1800);
        
        $this->analytics->trackEvent('api_loans_requested', $filters);
        
        return new Response(json_encode($loans), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API kredytów hipotecznych
     */
    public function getMortgages(Request $request): Response
    {
        $filters = $this->parseFilters($request);
        
        $cacheKey = 'api_mortgages_' . md5(serialize($filters));
        $mortgages = $this->cache->remember($cacheKey, function() use ($filters) {
            return $this->apiService->getMortgages($filters);
        }, 1800);
        
        $this->analytics->trackEvent('api_mortgages_requested', $filters);
        
        return new Response(json_encode($mortgages), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API kont osobistych
     */
    public function getAccounts(Request $request): Response
    {
        $cacheKey = 'api_accounts';
        $accounts = $this->cache->remember($cacheKey, function() {
            return $this->apiService->getPersonalAccounts();
        }, 3600);
        
        $this->analytics->trackEvent('api_accounts_requested');
        
        return new Response(json_encode($accounts), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API kont oszczędnościowych
     */
    public function getSavings(Request $request): Response
    {
        $cacheKey = 'api_savings';
        $savings = $this->cache->remember($cacheKey, function() {
            return $this->apiService->getSavingsAccounts();
        }, 3600);
        
        $this->analytics->trackEvent('api_savings_requested');
        
        return new Response(json_encode($savings), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API lokat
     */
    public function getDeposits(Request $request): Response
    {
        $cacheKey = 'api_deposits';
        $deposits = $this->cache->remember($cacheKey, function() {
            return $this->apiService->getDeposits();
        }, 3600);
        
        $this->analytics->trackEvent('api_deposits_requested');
        
        return new Response(json_encode($deposits), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API szczegółów produktu
     */
    public function getProductDetails(Request $request): Response
    {
        $productId = (int)$request->getPathParam('id');
        
        if (!$productId) {
            return new Response(json_encode(['error' => 'Brak ID produktu']), 400, ['Content-Type' => 'application/json']);
        }
        
        $cacheKey = "api_product_{$productId}";
        $product = $this->cache->remember($cacheKey, function() use ($productId) {
            return $this->apiService->getProductDetails($productId);
        }, 3600);
        
        $this->analytics->trackEvent('api_product_details_requested', ['product_id' => $productId]);
        
        return new Response(json_encode($product), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API śledzenia zdarzeń
     */
    public function trackEvent(Request $request): Response
    {
        $data = json_decode($request->getBody(), true);
        
        if (!$data || !isset($data['event'])) {
            return new Response(json_encode(['error' => 'Nieprawidłowe dane']), 400, ['Content-Type' => 'application/json']);
        }
        
        $this->analytics->trackEvent($data['event'], $data['data'] ?? []);
        
        return new Response(json_encode(['success' => true]), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API statystyk
     */
    public function getStats(Request $request): Response
    {
        $stats = $this->analytics->getStats();
        
        return new Response(json_encode($stats), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API newsletter - zapisanie
     */
    public function subscribeNewsletter(Request $request): Response
    {
        $data = json_decode($request->getBody(), true);
        
        if (!$data || !isset($data['email'])) {
            return new Response(json_encode(['error' => 'Brak adresu email']), 400, ['Content-Type' => 'application/json']);
        }
        
        // Tutaj logika zapisywania do newslettera
        $this->analytics->trackEvent('newsletter_subscribed', ['email' => $data['email']]);
        
        return new Response(json_encode(['success' => true, 'message' => 'Zapisano do newslettera']), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API newsletter - wypisanie
     */
    public function unsubscribeNewsletter(Request $request): Response
    {
        $data = json_decode($request->getBody(), true);
        
        if (!$data || !isset($data['email'])) {
            return new Response(json_encode(['error' => 'Brak adresu email']), 400, ['Content-Type' => 'application/json']);
        }
        
        // Tutaj logika wypisywania z newslettera
        $this->analytics->trackEvent('newsletter_unsubscribed', ['email' => $data['email']]);
        
        return new Response(json_encode(['success' => true, 'message' => 'Wypisano z newslettera']), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API personalizacji - zapisywanie preferencji
     */
    public function savePreferences(Request $request): Response
    {
        $data = json_decode($request->getBody(), true);
        
        if (!$data) {
            return new Response(json_encode(['error' => 'Nieprawidłowe dane']), 400, ['Content-Type' => 'application/json']);
        }
        
        // Tutaj logika zapisywania preferencji
        $this->analytics->trackEvent('preferences_saved', $data);
        
        return new Response(json_encode(['success' => true]), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API personalizacji - rekomendacje
     */
    public function getRecommendations(Request $request): Response
    {
        $userId = $request->get('user_id');
        
        // Tutaj logika generowania rekomendacji
        $recommendations = [
            'loans' => $this->apiService->getBestOffers('kredyty_gotowkowe', 3),
            'accounts' => $this->apiService->getBestOffers('konta_osobiste', 3),
            'deposits' => $this->apiService->getBestOffers('lokaty', 3)
        ];
        
        $this->analytics->trackEvent('recommendations_requested', ['user_id' => $userId]);
        
        return new Response(json_encode($recommendations), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * Parsowanie filtrów z żądania
     */
    private function parseFilters(Request $request): array
    {
        $filters = [];
        
        // Filtry dla kredytów
        if ($amount = $request->get('amount')) {
            $filters['amount'] = (int)$amount;
        }
        
        if ($period = $request->get('period')) {
            $filters['period'] = (int)$period;
        }
        
        if ($paymentType = $request->get('payment_type')) {
            $filters['payment_type'] = $paymentType;
        }
        
        // Filtry boolean
        $booleanFilters = [
            'cross_selling', 'without_certificate', 'without_insurance',
            'without_protection', 'without_spouse_agree'
        ];
        
        foreach ($booleanFilters as $filter) {
            if ($request->get($filter) !== null) {
                $filters[$filter] = $request->get($filter) ? 'true' : 'false';
            }
        }
        
        return $filters;
    }
} 