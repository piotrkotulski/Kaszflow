<?php

namespace Kaszflow\Controllers;

use Kaszflow\Core\Request;
use Kaszflow\Core\Response;
use Kaszflow\Services\FinancialApiService;
use Kaszflow\Services\Cache;
use Kaszflow\Services\AnalyticsService;

/**
 * Kontroler porównywarki produktów finansowych
 */
class ComparisonController
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
     * Porównywarka kredytów gotówkowych
     */
    public function loans(Request $request): Response
    {
        $filters = $this->parseFilters($request);
        
        // Pobieranie danych z cache lub API
        $cacheKey = 'loans_' . md5(serialize($filters));
        $loans = $this->cache->remember($cacheKey, function() use ($filters) {
            return $this->apiService->getLoans($filters);
        }, 1800); // Cache na 30 minut
        
        // Sortowanie wyników
        if (!empty($loans) && !isset($loans['error'])) {
            $sortBy = $request->get('sort', 'rate');
            $loans = $this->sortLoans($loans, $sortBy);
        }
        
        // Śledzenie wizyty
        $this->analytics->trackPageView('comparison_loans', $filters);
        
        $content = $this->renderView('comparison/loans', [
            'loans' => $loans,
            'filters' => $filters,
            'pageTitle' => 'Porównywarka Kredytów Gotówkowych - Kaszflow',
            'pageDescription' => 'Porównaj kredyty gotówkowe i znajdź najlepszą ofertę dla siebie.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Porównywarka kredytów hipotecznych
     */
    public function mortgages(Request $request): Response
    {
        $filters = $this->parseFilters($request);
        
        $cacheKey = 'mortgages_' . md5(serialize($filters));
        $mortgages = $this->cache->remember($cacheKey, function() use ($filters) {
            return $this->apiService->getMortgages($filters);
        }, 1800);
        
        // Sortowanie wyników
        if (!empty($mortgages) && !isset($mortgages['error'])) {
            $sortBy = $request->get('sort', 'total');
            $mortgages = $this->sortMortgages($mortgages, $sortBy);
        }
        
        $this->analytics->trackPageView('comparison_mortgages', $filters);
        
        $content = $this->renderView('comparison/mortgages', [
            'mortgages' => $mortgages,
            'filters' => $filters,
            'pageTitle' => 'Porównywarka Kredytów Hipotecznych - Kaszflow',
            'pageDescription' => 'Porównaj kredyty hipoteczne i znajdź najlepszą ofertę na zakup mieszkania.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Porównywarka kont osobistych
     */
    public function accounts(Request $request): Response
    {
        $cacheKey = 'personal_accounts';
        $accounts = $this->cache->remember($cacheKey, function() {
            return $this->apiService->getPersonalAccounts();
        }, 3600);
        
        // Sortowanie wyników
        if (!empty($accounts) && !isset($accounts['error'])) {
            $sortBy = $request->get('sort', '');
            if ($sortBy) {
                $accounts = $this->sortAccounts($accounts, $sortBy);
            }
        }
        
        $this->analytics->trackPageView('comparison_accounts');
        
        $content = $this->renderView('comparison/accounts', [
            'accounts' => $accounts,
            'pageTitle' => 'Porównywarka Kont Osobistych - Kaszflow',
            'pageDescription' => 'Porównaj konta osobiste i znajdź najlepsze oferty banków.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Porównywarka kont firmowych
     */
    public function businessAccounts(Request $request): Response
    {
        $cacheKey = 'business_accounts';
        $accounts = $this->cache->remember($cacheKey, function() {
            return $this->apiService->getBusinessAccounts();
        }, 3600);
        
        $this->analytics->trackPageView('comparison_business_accounts');
        
        $content = $this->renderView('comparison/business-accounts', [
            'accounts' => $accounts,
            'pageTitle' => 'Porównywarka Kont Firmowych - Kaszflow',
            'pageDescription' => 'Porównaj konta firmowe i znajdź najlepsze oferty dla Twojej firmy.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Porównywarka lokat
     */
    public function deposits(Request $request): Response
    {
        $filters = $this->parseDepositFilters($request);
        
        // Ustaw domyślne parametry jeśli nie ma filtrów
        if (empty($filters)) {
            $filters = [
                'amount' => 10000,
                'period' => 6
            ];
        }
        
        $cacheKey = 'deposits_' . md5(serialize($filters));
        $deposits = $this->cache->remember($cacheKey, function() use ($filters) {
            return $this->apiService->getDeposits($filters);
        }, 3600);
        
        // Sortowanie wyników
        if (!empty($deposits) && !isset($deposits['error'])) {
            // Filtrowanie wyników zgodnie z kryteriami
            $deposits = $this->filterDepositsByCriteria($deposits, $filters);
            
            $sortBy = $request->get('sort', 'rate');
            $deposits = $this->sortDeposits($deposits, $sortBy);
        }
        
        $this->analytics->trackPageView('comparison_deposits', $filters);
        
        $content = $this->renderView('comparison/deposits', [
            'deposits' => $deposits,
            'filters' => $filters,
            'pageTitle' => 'Porównywarka Lokat - Kaszflow',
            'pageDescription' => 'Porównaj lokaty bankowe i znajdź najlepsze oprocentowanie.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Szczegóły produktu
     */
    public function productDetails(Request $request): Response
    {
        $productId = (int)$request->get('id');
        $versionId = $request->get('version_id') ? (int)$request->get('version_id') : null;
        
        if (!$productId) {
            return new Response('Brak ID produktu', 400);
        }
        
        $cacheKey = "product_{$productId}" . ($versionId ? "_{$versionId}" : '');
        $product = $this->cache->remember($cacheKey, function() use ($productId, $versionId) {
            return $this->apiService->getProductDetails($productId, $versionId);
        }, 3600);
        
        $this->analytics->trackPageView('product_details', ['product_id' => $productId]);
        
        $content = $this->renderView('comparison/product-details', [
            'product' => $product,
            'pageTitle' => 'Szczegóły Produktu - Kaszflow',
            'pageDescription' => 'Szczegółowe informacje o produkcie finansowym.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Sortowanie kredytów
     */
    private function sortLoans(array $loans, string $sortBy): array
    {
        switch ($sortBy) {
            case 'rrso':
                usort($loans, function($a, $b) {
                    return ($a['aprc'] ?? 999) <=> ($b['aprc'] ?? 999);
                });
                break;
            case 'rate':
            default:
                usort($loans, function($a, $b) {
                    return ($a['first_installment'] ?? 999) <=> ($b['first_installment'] ?? 999);
                });
                break;
        }
        
        return $loans;
    }

    /**
     * Sortowanie kredytów hipotecznych
     */
    private function sortMortgages(array $mortgages, string $sortBy): array
    {
        switch ($sortBy) {
            case 'rate':
                usort($mortgages, function($a, $b) {
                    return ($a['first_installment'] ?? 999) <=> ($b['first_installment'] ?? 999);
                });
                break;
            case 'rrso':
                usort($mortgages, function($a, $b) {
                    return ($a['apr'] ?? 999) <=> ($b['apr'] ?? 999);
                });
                break;
            case 'total':
                usort($mortgages, function($a, $b) {
                    return ($a['total_amount'] ?? 999) <=> ($b['total_amount'] ?? 999);
                });
                break;
            default:
                // Domyślne sortowanie alfabetyczne po nazwie banku
                usort($mortgages, function($a, $b) {
                    return strcmp($a['bank_name'] ?? '', $b['bank_name'] ?? '');
                });
                break;
        }
        return $mortgages;
    }

    /**
     * Sortowanie kont osobistych
     */
    private function sortAccounts(array $accounts, string $sortBy): array
    {
        switch ($sortBy) {
            case 'bonus':
                usort($accounts, function($a, $b) {
                    // Wyciągnij liczby z tekstu premii (np. "1000 zł" -> 1000)
                    $bonusA = $this->extractNumberFromText($a['bonus'] ?? '0');
                    $bonusB = $this->extractNumberFromText($b['bonus'] ?? '0');
                    return $bonusB <=> $bonusA; // Malejąco - najwyższa premia pierwsza
                });
                break;
            case 'fee':
                usort($accounts, function($a, $b) {
                    return ($a['management_fee_min'] ?? 999) <=> ($b['management_fee_min'] ?? 999);
                });
                break;
            default:
                // Domyślne sortowanie alfabetyczne po nazwie banku
                usort($accounts, function($a, $b) {
                    return strcmp($a['bank_name'] ?? '', $b['bank_name'] ?? '');
                });
                break;
        }
        return $accounts;
    }
    
    /**
     * Wyciąga liczbę z tekstu (np. "1000 zł" -> 1000)
     */
    private function extractNumberFromText($text): float
    {
        if (is_numeric($text)) {
            return (float)$text;
        }
        
        if (is_string($text)) {
            // Usuń wszystkie znaki oprócz cyfr, kropek i przecinków
            $number = preg_replace('/[^0-9.,]/', '', $text);
            // Zamień przecinek na kropkę
            $number = str_replace(',', '.', $number);
            return (float)$number;
        }
        
        return 0;
    }
    
    /**
     * Parsowanie filtrów z żądania
     */
    private function parseFilters(Request $request): array
    {
        $filters = [];
        
        // Filtry dla kredytów - tylko niepuste wartości
        if ($amount = $request->get('amount')) {
            $amount = (int)$amount;
            if ($amount > 0) {
                $filters['amount'] = $amount;
            }
        }
        
        if ($period = $request->get('period')) {
            $period = (int)$period;
            if ($period > 0) {
                $filters['period'] = $period;
            }
        }
        
        if ($paymentType = $request->get('payment_type')) {
            if (in_array($paymentType, ['s', 'm', 'o'])) {
                $filters['payment_type'] = $paymentType;
            }
        }
        
        // Filtry boolean - tylko jeśli są ustawione
        $booleanFilters = [
            'cross_selling', 'without_certificate', 'without_insurance',
            'without_protection', 'without_spouse_agree'
        ];
        
        foreach ($booleanFilters as $filter) {
            $value = $request->get($filter);
            if ($value !== null && $value !== '') {
                $filters[$filter] = $value ? 'true' : 'false';
            }
        }
        
        return $filters;
    }
    
    /**
     * Parsowanie filtrów lokat
     */
    private function parseDepositFilters(Request $request): array
    {
        $filters = [];
        
        // Filtry dla lokat - tylko niepuste wartości
        if ($amount = $request->get('deposit_amount')) {
            $amount = (int)$amount;
            if ($amount > 0) {
                $filters['amount'] = $amount;
            }
        }
        
        if ($period = $request->get('deposit_period')) {
            $period = (int)$period;
            if ($period > 0) {
                $filters['period'] = $period;
            }
        }
        
        if ($interestType = $request->get('deposit_interest_type')) {
            if (in_array($interestType, ['fixed', 'variable'])) {
                $filters['interest_type'] = $interestType;
            }
        }
        
        // Filtry boolean - tylko jeśli są ustawione
        $booleanFilters = [
            'cross_selling', 'without_insurance', 'without_protection'
        ];
        
        foreach ($booleanFilters as $filter) {
            $value = $request->get($filter);
            if ($value !== null && $value !== '') {
                $filters[$filter] = $value ? 'true' : 'false';
            }
        }
        
        return $filters;
    }
    
    /**
     * Sortowanie lokat
     */
    private function sortDeposits(array $deposits, string $sortBy): array
    {
        switch ($sortBy) {
            case 'rate':
                usort($deposits, function($a, $b) {
                    // Wyciągnij najwyższe oprocentowanie z tablicy
                    $maxRateA = $this->getMaxInterestRate($a['interest_rate'] ?? []);
                    $maxRateB = $this->getMaxInterestRate($b['interest_rate'] ?? []);
                    return $maxRateB <=> $maxRateA; // Malejąco - najwyższe oprocentowanie pierwsze
                });
                break;
            case 'total':
                usort($deposits, function($a, $b) {
                    return ($b['interest'] ?? 0) <=> ($a['interest'] ?? 0); // Malejąco - najwyższe odsetki pierwsze
                });
                break;
            default:
                // Domyślne sortowanie alfabetyczne po nazwie banku
                usort($deposits, function($a, $b) {
                    return strcmp($a['bank_name'] ?? '', $b['bank_name'] ?? '');
                });
                break;
        }
        return $deposits;
    }
    
    /**
     * Wyciąga najwyższe oprocentowanie z tablicy
     */
    private function getMaxInterestRate(array $interestRates): float
    {
        $maxRate = 0;
        foreach ($interestRates as $rate) {
            if (isset($rate[1]) && is_numeric($rate[1]) && $rate[1] > $maxRate) {
                $maxRate = (float)$rate[1];
            }
        }
        return $maxRate;
    }

    /**
     * Filtruje lokaty zgodnie z kryteriami
     */
    private function filterDepositsByCriteria(array $deposits, array $filters): array
    {
        $filteredDeposits = [];
        
        foreach ($deposits as $deposit) {
            $amountMatch = true;
            $periodMatch = true;
            
            // Sprawdzanie kwoty
            if (isset($filters['amount'])) {
                $requestedAmount = (int)$filters['amount'];
                $minAmount = (int)($deposit['min_amount'] ?? 0);
                $maxAmount = (int)($deposit['max_amount'] ?? PHP_INT_MAX);
                
                // Sprawdź czy kwota mieści się w zakresie lokaty
                if ($requestedAmount < $minAmount || $requestedAmount > $maxAmount) {
                    $amountMatch = false;
                }
            }
            
            // Sprawdzanie okresu
            if (isset($filters['period'])) {
                $requestedPeriod = (int)$filters['period'];
                $depositPeriod = $this->extractPeriodFromDescription($deposit['period_description'] ?? '');
                
                // Sprawdź czy okres pasuje
                if ($depositPeriod !== $requestedPeriod) {
                    $periodMatch = false;
                }
            }
            
            if ($amountMatch && $periodMatch) {
                $filteredDeposits[] = $deposit;
            }
        }
        
        return $filteredDeposits;
    }
    
    /**
     * Wyciąga okres z opisu (np. "3 miesiące" -> 3)
     */
    private function extractPeriodFromDescription(string $description): int
    {
        // Szukaj liczby w opisie
        if (preg_match('/(\d+)/', $description, $matches)) {
            return (int)$matches[1];
        }
        
        return 0;
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
        
        extract($data);
        
        ob_start();
        include $viewFile;
        return ob_get_clean();
    }
} 