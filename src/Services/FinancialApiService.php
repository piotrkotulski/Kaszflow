<?php

namespace Kaszflow\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Serwis API produktów finansowych
 */
class FinancialApiService
{
    private $client;
    private $baseUrl;
    private $token;
    private $timeout;
    
    public function __construct()
    {
        $this->baseUrl = $_ENV['API_BASE_URL'] ?? 'https://api.systempartnerski.pl';
        $this->token = $_ENV['API_TOKEN'] ?? '';
        $this->timeout = $_ENV['API_TIMEOUT'] ?? 30;
        
        $this->client = new Client([
            'timeout' => $this->timeout,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
    }
    
    /**
     * Tworzenie klienta z odpowiednim tokenem
     */
    private function createClient(string $token): Client
    {
        return new Client([
            'timeout' => $this->timeout,
            'headers' => [
                'Accept' => 'application/json',
                'X-Auth-Token' => $token
            ]
        ]);
    }
    
    /**
     * Pobieranie danych z API
     */
    public function getData(array $params = [], string $token = null): array
    {
        try {
            $url = $this->baseUrl . '/publishers/financial-products-api/getdata';
            
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
            
            // Logowanie URL i tokenu dla debugowania
            error_log("API URL: " . $url);
            error_log("API Token: " . substr($token ?? 'null', 0, 10) . '...');
            
            $client = $token ? $this->createClient($token) : $this->client;
            $response = $client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);
            
            return $data ?: [];
            
        } catch (RequestException $e) {
            $errorMessage = 'Błąd API: ' . $e->getMessage();
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $errorMessage .= ' Response: ' . $responseBody;
            }
            error_log($errorMessage);
            return ['error' => $errorMessage];
        } catch (\Exception $e) {
            $errorMessage = 'Błąd połączenia: ' . $e->getMessage();
            error_log($errorMessage);
            return ['error' => $errorMessage];
        }
    }
    
    /**
     * Pobieranie kredytów gotówkowych
     */
    public function getLoans(array $params = []): array
    {
        // Podstawowe parametry - tylko product_type
        $defaultParams = [
            'product_type' => 'kredyty_gotowkowe'
        ];
        
        // Dodajemy tylko te parametry, które są niepuste i nie są null
        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '' && $value !== false) {
                $defaultParams[$key] = $value;
            }
        }
        
        $token = $_ENV['API_TOKEN_LOANS'] ?? $_ENV['API_TOKEN'];
        $result = $this->getData($defaultParams, $token);
        
        // Jeśli API zwróciło błąd, sprawdźmy czy to błąd parametrów
        if (isset($result['error'])) {
            $errorMessage = $result['error'];
            
            // Jeśli to błąd parametrów (nie 404), zwróćmy pustą listę
            if (strpos($errorMessage, 'Nie znaleziono wyników') !== false || 
                strpos($errorMessage, 'Brak dostępnych produktów') !== false) {
                error_log("API error with params: " . json_encode($defaultParams) . ". No products found for given criteria.");
                return [];
            }
            
            // Jeśli to 404, spróbujmy z podstawowymi parametrami
            if (strpos($errorMessage, '404') !== false) {
                error_log("API error with params: " . json_encode($defaultParams) . ". Trying with basic params.");
                return $this->getData(['product_type' => 'kredyty_gotowkowe'], $token);
            }
        }
        
        // Filtrowanie wyników zgodnie z warunkami brzegowymi
        if (!isset($result['error']) && is_array($result)) {
            $result = $this->filterLoansByConstraints($result, $params);
        }
        
        return $result;
    }
    
    /**
     * Pobieranie kredytów hipotecznych
     */
    public function getMortgages(array $params = []): array
    {
        $defaultParams = [
            'amount' => 400000,
            'period' => 30,
            'property_value' => 500000,
            'product_type' => 'kredyty_hipoteczne'
        ];
        
        $params = array_merge($defaultParams, $params);
        $token = $_ENV['API_TOKEN_MORTGAGES'] ?? $_ENV['API_TOKEN'];
        $result = $this->getData($params, $token);
        
        // Filtrowanie wyników zgodnie z warunkami brzegowymi
        if (!isset($result['error']) && is_array($result)) {
            $result = $this->filterLoansByConstraints($result, $params);
        }
        
        return $result;
    }
    
    /**
     * Pobieranie kont osobistych
     */
    public function getPersonalAccounts(): array
    {
        $token = $_ENV['API_TOKEN_ACCOUNTS'] ?? $_ENV['API_TOKEN'];
        return $this->getData(['product_type' => 'konta_osobiste'], $token);
    }
    
    /**
     * Pobieranie kont firmowych
     */
    public function getBusinessAccounts(): array
    {
        $token = $_ENV['API_TOKEN_BUSINESS_ACCOUNTS'] ?? $_ENV['API_TOKEN'];
        return $this->getData(['product_type' => 'konta_firmowe'], $token);
    }
    
    /**
     * Pobieranie kont oszczędnościowych
     */
    public function getSavingsAccounts(): array
    {
        $token = $_ENV['API_TOKEN_SAVINGS_ACCOUNTS'] ?? $_ENV['API_TOKEN'];
        return $this->getData(['product_type' => 'konta_oszczednosciowe'], $token);
    }
    
    /**
     * Pobieranie lokat
     */
    public function getDeposits(array $params = []): array
    {
        // Podstawowe parametry - tylko product_type
        $defaultParams = [
            'product_type' => 'lokaty'
        ];
        
        // Dodajemy tylko te parametry, które są niepuste i nie są null
        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '' && $value !== false) {
                $defaultParams[$key] = $value;
            }
        }
        
        $token = $_ENV['API_TOKEN_DEPOSITS'] ?? $_ENV['API_TOKEN'];
        $result = $this->getData($defaultParams, $token);
        
        // Jeśli API zwróciło błąd, sprawdźmy czy to błąd parametrów
        if (isset($result['error'])) {
            $errorMessage = $result['error'];
            
            // Jeśli to błąd parametrów (nie 404), zwróćmy pustą listę
            if (strpos($errorMessage, 'Nie znaleziono wyników') !== false || 
                strpos($errorMessage, 'Brak dostępnych produktów') !== false) {
                error_log("API error with params: " . json_encode($defaultParams) . ". No products found for given criteria.");
                return [];
            }
            
            // Jeśli to 404, spróbujmy z podstawowymi parametrami
            if (strpos($errorMessage, '404') !== false) {
                error_log("API error with params: " . json_encode($defaultParams) . ". Trying with basic params.");
                return $this->getData(['product_type' => 'lokaty'], $token);
            }
        }
        
        return $result;
    }
    
    /**
     * Pobieranie szczegółów produktu
     */
    public function getProductDetails(int $productId, int $versionId = null): array
    {
        $params = ['product_id' => $productId];
        
        if ($versionId) {
            $params['version_id'] = $versionId;
        }
        
        try {
            $url = $this->baseUrl . '/publishers/financial-products-api/product_details';
            $url .= '?' . http_build_query($params);
            
            $response = $this->client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);
            
            return $data ?: [];
            
        } catch (RequestException $e) {
            return ['error' => 'Błąd API: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => 'Błąd połączenia: ' . $e->getMessage()];
        }
    }
    
    /**
     * Filtrowanie produktów według parametrów
     */
    public function filterProducts(string $type, array $filters = []): array
    {
        switch ($type) {
            case 'kredyty_gotowkowe':
                return $this->getLoans($filters);
            case 'kredyty_hipoteczne':
                return $this->getMortgages($filters);
            case 'konta_osobiste':
                return $this->getPersonalAccounts();
            case 'konta_firmowe':
                return $this->getBusinessAccounts();
            case 'konta_oszczednosciowe':
                return $this->getSavingsAccounts();
            case 'lokaty':
                return $this->getDeposits($filters);
            default:
                return [];
        }
    }
    
    /**
     * Pobieranie najlepszych ofert
     */
    public function getBestOffers(string $type, int $limit = 5): array
    {
        $products = $this->filterProducts($type);
        
        if (isset($products['error'])) {
            return $products;
        }
        
        // Sortowanie według typu produktu
        switch ($type) {
            case 'kredyty_gotowkowe':
                usort($products, function($a, $b) {
                    return $a['aprc'] <=> $b['aprc'];
                });
                break;
                
            case 'konta_osobiste':
                usort($products, function($a, $b) {
                    $bonusA = $this->extractBonusValue($a['bonus'] ?? '');
                    $bonusB = $this->extractBonusValue($b['bonus'] ?? '');
                    return $bonusB <=> $bonusA;
                });
                break;
                
            case 'lokaty':
                usort($products, function($a, $b) {
                    $rateA = $this->getMaxInterestRate($a['interest_rate'] ?? []);
                    $rateB = $this->getMaxInterestRate($b['interest_rate'] ?? []);
                    return $rateB <=> $rateA;
                });
                break;
        }
        
        return array_slice($products, 0, $limit);
    }
    
    /**
     * Ekstrakcja wartości bonusu
     */
    private function extractBonusValue(string $bonus): float
    {
        preg_match('/(\d+(?:\.\d+)?)/', $bonus, $matches);
        return isset($matches[1]) ? (float)$matches[1] : 0;
    }
    
    /**
     * Filtrowanie kredytów zgodnie z warunkami brzegowymi
     */
    private function filterLoansByConstraints(array $loans, array $filters): array
    {
        $filteredLoans = [];
        
        foreach ($loans as $loan) {
            $isValid = true;
            
            // Sprawdzanie kwoty minimalnej
            if (isset($filters['amount']) && isset($loan['amount_min'])) {
                if ($filters['amount'] < $loan['amount_min']) {
                    $isValid = false;
                }
            }
            
            // Sprawdzanie kwoty maksymalnej
            if (isset($filters['amount']) && isset($loan['amount_max'])) {
                if ($filters['amount'] > $loan['amount_max']) {
                    $isValid = false;
                }
            }
            
            // Sprawdzanie okresu minimalnego
            if (isset($filters['period']) && isset($loan['period_min'])) {
                if ($filters['period'] < $loan['period_min']) {
                    $isValid = false;
                }
            }
            
            // Sprawdzanie okresu maksymalnego
            if (isset($filters['period']) && isset($loan['period_max'])) {
                if ($filters['period'] > $loan['period_max']) {
                    $isValid = false;
                }
            }
            
            if ($isValid) {
                $filteredLoans[] = $loan;
            }
        }
        
        return $filteredLoans;
    }
    
    /**
     * Pobieranie maksymalnego oprocentowania
     */
    private function getMaxInterestRate(array $rates): float
    {
        if (empty($rates)) {
            return 0;
        }
        
        $maxRate = 0;
        foreach ($rates as $rate) {
            if (isset($rate[1]) && $rate[1] > $maxRate) {
                $maxRate = $rate[1];
            }
        }
        
        return $maxRate;
    }
} 