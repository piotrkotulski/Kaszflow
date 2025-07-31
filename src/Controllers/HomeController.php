<?php

namespace Kaszflow\Controllers;

use Kaszflow\Core\Request;
use Kaszflow\Core\Response;
use Kaszflow\Services\FinancialApiService;
use Kaszflow\Services\Cache;

/**
 * Kontroler strony głównej
 */
class HomeController
{
    private $apiService;
    private $cache;
    
    public function __construct()
    {
        $this->apiService = new FinancialApiService();
        $this->cache = new Cache();
    }
    
    /**
     * Strona główna
     */
    public function index(Request $request): Response
    {
        // Pobieranie najlepszych ofert bez cache (tymczasowo)
        $bestOffers = [
            'loans' => $this->apiService->getBestOffers('kredyty_gotowkowe', 3),
            'accounts' => $this->apiService->getBestOffers('konta_osobiste', 3),
            'deposits' => $this->apiService->getBestOffers('lokaty', 3)
        ];
        
        // Pobieranie najlepszych ofert dla sekcji hero
        $heroOffers = $this->getHeroOffers();
        
        // Pobieranie unikalnych banków partnerskich
        $bankPartners = $this->getBankPartners();
        
        $content = $this->renderView('home', [
            'bestOffers' => $bestOffers,
            'heroOffers' => $heroOffers,
            'bankPartners' => $bankPartners,
            'pageTitle' => 'Kaszflow - Porównywarka Produktów Finansowych',
            'pageDescription' => 'Porównuj kredyty, konta, lokaty i inne produkty finansowe. Znajdź najlepsze oferty na rynku.'
        ]);
        
        return new Response($content);
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
        
        // Ekstrakcja danych do zmiennych
        extract($data);
        
        // Buforowanie wyjścia
        ob_start();
        include $viewFile;
        return ob_get_clean();
    }
    
    /**
     * Strona o nas
     */
    public function about(Request $request): Response
    {
        $content = $this->renderView('o-nas', [
            'pageTitle' => 'O nas - Kaszflow',
            'pageDescription' => 'Poznaj naszą misję i wartości. Jesteśmy ekspertami w dziedzinie finansów osobistych.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Strona kontakt
     */
    public function contact(Request $request): Response
    {
        $content = $this->renderView('contact', [
            'pageTitle' => 'Kontakt - Kaszflow',
            'pageDescription' => 'Skontaktuj się z nami. Jesteśmy tutaj, aby Ci pomóc.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Strona polityka prywatności
     */
    public function privacy(Request $request): Response
    {
        $content = $this->renderView('privacy', [
            'pageTitle' => 'Polityka Prywatności - Kaszflow',
            'pageDescription' => 'Dowiedz się, jak chronimy Twoje dane osobowe.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Strona regulamin
     */
    public function terms(Request $request): Response
    {
        $content = $this->renderView('terms', [
            'pageTitle' => 'Regulamin - Kaszflow',
            'pageDescription' => 'Regulamin korzystania z serwisu Kaszflow.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Pobieranie najlepszych ofert dla sekcji hero
     */
    private function getHeroOffers(): array
    {
        // Pobierz wszystkie oferty do obliczenia średnich
        $allLoans = $this->apiService->getLoans([]);
        $allAccounts = $this->apiService->getPersonalAccounts();
        $allDeposits = $this->apiService->getDeposits();
        
        // Najlepszy kredyt (najniższe RRSO)
        $bestLoan = null;
        if (!empty($allLoans) && !isset($allLoans['error'])) {
            usort($allLoans, function($a, $b) {
                return ($a['aprc'] ?? 999) <=> ($b['aprc'] ?? 999);
            });
            $bestLoan = $allLoans[0];
        }
        
        // Najlepsze konto (najwyższa premia)
        $bestAccount = null;
        if (!empty($allAccounts) && !isset($allAccounts['error'])) {
            usort($allAccounts, function($a, $b) {
                $bonusA = $this->extractBonusValue($a['bonus'] ?? '');
                $bonusB = $this->extractBonusValue($b['bonus'] ?? '');
                return $bonusB <=> $bonusA;
            });
            $bestAccount = $allAccounts[0];
        }
        
        // Najlepsza lokata (najwyższe oprocentowanie)
        $bestDeposit = null;
        if (!empty($allDeposits) && !isset($allDeposits['error'])) {
            usort($allDeposits, function($a, $b) {
                $rateA = $this->getMaxInterestRate($a['interest_rate'] ?? []);
                $rateB = $this->getMaxInterestRate($b['interest_rate'] ?? []);
                return $rateB <=> $rateA;
            });
            $bestDeposit = $allDeposits[0];
        }
        
        // Oblicz średnie
        $avgLoanRRSO = $this->calculateAverageRRSO($allLoans);
        $avgDepositRate = $this->calculateAverageDepositRate($allDeposits);
        
        return [
            'loan' => $bestLoan,
            'account' => $bestAccount,
            'deposit' => $bestDeposit,
            'avgLoanRRSO' => $avgLoanRRSO,
            'avgDepositRate' => $avgDepositRate
        ];
    }
    
    /**
     * Pobieranie unikalnych banków partnerskich
     */
    private function getBankPartners(): array
    {
        $loans = $this->apiService->getLoans([]);
        $accounts = $this->apiService->getPersonalAccounts();
        $deposits = $this->apiService->getDeposits();

        $allBanks = [];

        // Pobieranie banków z kredytów
        if (!empty($loans) && !isset($loans['error'])) {
            foreach ($loans as $loan) {
                $bankName = $loan['bank_name'] ?? 'Nieznany bank';
                $allBanks[$bankName] = [
                    'name' => $bankName,
                    'logo_url' => $loan['logo_url_format'] ?? $loan['logo_url'] ?? null,
                    'initials' => strtoupper(substr($bankName, 0, 2))
                ];
            }
        }
        
        // Pobieranie banków z kont
        if (!empty($accounts) && !isset($accounts['error'])) {
            foreach ($accounts as $account) {
                $bankName = $account['bank_name'] ?? 'Nieznany bank';
                if (!isset($allBanks[$bankName])) {
                    $allBanks[$bankName] = [
                        'name' => $bankName,
                        'logo_url' => $account['logo_url_format'] ?? $account['logo_url'] ?? null,
                        'initials' => strtoupper(substr($bankName, 0, 2))
                    ];
                }
            }
        }
        
        // Pobieranie banków z lokat
        if (!empty($deposits) && !isset($deposits['error'])) {
            foreach ($deposits as $deposit) {
                $bankName = $deposit['bank_name'] ?? 'Nieznany bank';
                if (!isset($allBanks[$bankName])) {
                    $allBanks[$bankName] = [
                        'name' => $bankName,
                        'logo_url' => $deposit['logo_url_format'] ?? $deposit['logo_url'] ?? null,
                        'initials' => strtoupper(substr($bankName, 0, 2))
                    ];
                }
            }
        }

        return array_values($allBanks);
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
    
    /**
     * Obliczanie średniego RRSO
     */
    private function calculateAverageRRSO(array $loans): float
    {
        if (empty($loans) || isset($loans['error'])) {
            return 0;
        }
        
        $totalRRSO = 0;
        $count = 0;
        
        foreach ($loans as $loan) {
            if (isset($loan['aprc'])) {
                $totalRRSO += $loan['aprc'];
                $count++;
            }
        }
        
        return $count > 0 ? $totalRRSO / $count : 0;
    }
    
    /**
     * Obliczanie średniego oprocentowania lokat
     */
    private function calculateAverageDepositRate(array $deposits): float
    {
        if (empty($deposits) || isset($deposits['error'])) {
            return 0;
        }
        
        $totalRate = 0;
        $count = 0;
        
        foreach ($deposits as $deposit) {
            $maxRate = $this->getMaxInterestRate($deposit['interest_rate'] ?? []);
            if ($maxRate > 0) {
                $totalRate += $maxRate;
                $count++;
            }
        }
        
        return $count > 0 ? $totalRate / $count : 0;
    }
} 