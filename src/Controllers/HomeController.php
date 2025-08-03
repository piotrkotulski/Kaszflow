<?php

namespace Kaszflow\Controllers;

use Kaszflow\Core\Request;
use Kaszflow\Core\Response;
use Kaszflow\Services\FinancialApiService;
use Kaszflow\Services\Cache;
use Kaszflow\Services\BlogAutomationService;
use Kaszflow\Services\CacheService;

/**
 * Kontroler strony głównej
 */
class HomeController
{
    private $apiService;
    private $cache;
    private $blogService;
    private $cacheService;
    
    public function __construct()
    {
        $this->apiService = new FinancialApiService();
        $this->cache = new Cache();
        $this->blogService = new BlogAutomationService();
        $this->cacheService = new CacheService();
    }
    
    /**
     * Strona główna
     */
    public function home(Request $request): Response
    {
        // Pobieranie najlepszych ofert z cache
        $bestOffers = [
            'loans' => $this->cacheService->getData('loans'),
            'accounts' => $this->cacheService->getData('accounts'),
            'deposits' => $this->cacheService->getData('deposits')
        ];
        
        // Ograniczenie do 3 najlepszych ofert
        if (!empty($bestOffers['loans'])) {
            usort($bestOffers['loans'], function($a, $b) {
                return ($a['aprc'] ?? 999) <=> ($b['aprc'] ?? 999);
            });
            $bestOffers['loans'] = array_slice($bestOffers['loans'], 0, 3);
        }
        
        if (!empty($bestOffers['accounts'])) {
            usort($bestOffers['accounts'], function($a, $b) {
                $bonusA = 0;
                $bonusB = 0;
                if (!empty($a['bonus'])) {
                    preg_match('/(\d+(?:\.\d+)?)/', $a['bonus'], $matches);
                    $bonusA = isset($matches[1]) ? (float)$matches[1] : 0;
                }
                if (!empty($b['bonus'])) {
                    preg_match('/(\d+(?:\.\d+)?)/', $b['bonus'], $matches);
                    $bonusB = isset($matches[1]) ? (float)$matches[1] : 0;
                }
                return $bonusB <=> $bonusA;
            });
            $bestOffers['accounts'] = array_slice($bestOffers['accounts'], 0, 3);
        }
        
        if (!empty($bestOffers['deposits'])) {
            usort($bestOffers['deposits'], function($a, $b) {
                return ($b['interest_rate'] ?? 0) <=> ($a['interest_rate'] ?? 0);
            });
            $bestOffers['deposits'] = array_slice($bestOffers['deposits'], 0, 3);
        }
        
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
     * Strona bloga
     */
    public function blog(Request $request): Response
    {
        $category = $request->get('category', 'wszystkie');
        
        // Pobieranie listy artykułów blogowych z filtrowaniem
        $articles = $this->getBlogArticlesFiltered($category);
        
        $content = $this->renderView('blog/index', [
            'articles' => $articles,
            'currentCategory' => $category,
            'pageTitle' => 'Blog Kaszflow',
            'pageDescription' => 'Artykuły o finansach osobistych, porady ekspertów i aktualności ze świata finansów'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Strona bloga z filtrowaniem po kategorii
     */
    public function blogByCategory(Request $request): Response
    {
        $category = $request->getPathParam('category');
        
        // Pobieranie listy artykułów blogowych z filtrowaniem
        $articles = $this->getBlogArticlesByCategory($category);
        
        $content = $this->renderView('blog/index', [
            'articles' => $articles,
            'currentCategory' => $category,
            'pageTitle' => 'Blog - ' . $this->mapCategoryToDisplay($category),
            'pageDescription' => 'Artykuły o ' . mb_strtolower($this->mapCategoryToDisplay($category))
        ]);
        
        return new Response($content);
    }
    
    /**
     * Pojedynczy artykuł blogowy
     */
    public function blogPost(Request $request): Response
    {
        $slug = $request->getPathParam('slug');
        
        // Pobieranie artykułu na podstawie slug
        $article = $this->getBlogArticle($slug);
        
        if (!$article) {
            return new Response('<h1>Artykuł nie został znaleziony</h1>', 404);
        }
        
        // Pobieranie powiązanych artykułów
        $relatedArticles = $this->getRelatedArticles($article['category'], $article['id']);
        
        $content = $this->renderView('blog/post', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'pageTitle' => $article['title'] . ' - Kaszflow',
            'pageDescription' => $article['meta_description'] ?? $article['excerpt']
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
     * Pobieranie artykułów blogowych z bazy danych
     */
    private function getBlogArticles(): array
    {
        $database = new \Kaszflow\Services\Database();
        
        // Pobierz opublikowane artykuły
        $query = "SELECT id, title, slug, meta_description as excerpt, created_at as published_at, status, category, graphic_initials, graphic_colors, image_url FROM articles WHERE status = 'published' ORDER BY created_at DESC LIMIT 10";
        $articles = $database->select($query);
        
        $formattedArticles = [];
        foreach ($articles as $article) {
            $colors = json_decode($article['graphic_colors'], true) ?: $this->getDefaultColors($article['category']);
            
            $formattedArticles[] = [
                'id' => $article['id'],
                'title' => $article['title'],
                'slug' => $article['slug'],
                'excerpt' => $article['excerpt'] ?: substr(strip_tags($article['title']), 0, 150) . '...',
                'published_at' => date('Y-m-d', strtotime($article['published_at'])),
                'category' => $this->mapCategoryToDisplay($article['category']),
                'read_time' => '5 min',
                'graphic' => [
                    'initials' => $article['graphic_initials'] ?: $this->generateInitials($article['title']),
                    'colors' => $colors
                ],
                'image_url' => $article['image_url'] ?: null
            ];
        }
        
        // Jeśli brak artykułów w bazie, zwróć przykładowe
        if (empty($formattedArticles)) {
            return [
                [
                    'id' => 1,
                    'title' => 'Jak wybrać najlepszy kredyt gotówkowy',
                    'slug' => 'jak-wybrac-najlepszy-kredyt-gotowkowy',
                    'excerpt' => 'Poradnik wyboru kredytu gotówkowego - na co zwrócić uwagę przy porównywaniu ofert.',
                    'published_at' => '2024-01-15',
                    'category' => 'Kredyty',
                    'read_time' => '5 min',
                    'graphic' => [
                        'initials' => 'JK',
                        'colors' => $this->getDefaultColors('kredyty')
                    ],
                    'image_url' => null
                ],
                [
                    'id' => 2,
                    'title' => 'Najlepsze konta osobiste 2024',
                    'slug' => 'najlepsze-konta-osobiste-2024',
                    'excerpt' => 'Ranking najlepszych kont osobistych w 2024 roku. Sprawdź, które konto wybrać.',
                    'published_at' => '2024-01-10',
                    'category' => 'Bankowość',
                    'read_time' => '7 min',
                    'graphic' => [
                        'initials' => 'NK',
                        'colors' => $this->getDefaultColors('bankowość')
                    ],
                    'image_url' => null
                ]
            ];
        }
        
        return $formattedArticles;
    }
    
    /**
     * Pobieranie artykułów blogowych z filtrowaniem po kategorii
     */
    private function getBlogArticlesByCategory(string $category): array
    {
        $database = new \Kaszflow\Services\Database();
        
        // Mapowanie URL na kategorie w bazie
        $categoryMapping = [
            'kredyty' => 'kredyty',
            'hipoteki' => 'hipoteki',
            'oszczedzanie' => 'oszczędzanie',
            'bankowosc' => 'bankowość',
            'firmy' => 'firmy'
        ];
        
        $dbCategory = $categoryMapping[$category] ?? 'finanse';
        
        // Pobierz opublikowane artykuły z filtrowaniem
        $query = "SELECT id, title, slug, meta_description as excerpt, created_at as published_at, status, category FROM articles WHERE status = 'published' AND category = :category ORDER BY created_at DESC LIMIT 10";
        $articles = $database->select($query, ['category' => $dbCategory]);
        
        $formattedArticles = [];
        foreach ($articles as $article) {
            $formattedArticles[] = [
                'id' => $article['id'],
                'title' => $article['title'],
                'slug' => $article['slug'],
                'excerpt' => $article['excerpt'] ?: substr(strip_tags($article['title']), 0, 150) . '...',
                'published_at' => date('Y-m-d', strtotime($article['published_at'])),
                'category' => $this->mapCategoryToDisplay($article['category']),
                'read_time' => '5 min'
            ];
        }
        
        return $formattedArticles;
    }
    
    /**
     * Pobieranie artykułów blogowych z filtrowaniem
     */
    private function getBlogArticlesFiltered(string $category): array
    {
        $database = new \Kaszflow\Services\Database();
        
        // Mapowanie kategorii na kategorie w bazie (obsługa zakodowanych URL-i)
        $categoryMapping = [
            'wszystkie' => null,
            'kredyty' => ['kredyty'],
            'hipoteki' => ['hipoteki'],
            'bankowosc' => ['bankowość'],
            'oszczedzanie' => ['oszczędzanie'],
            'ubezpieczenia' => ['ubezpieczenia'],
            'firmy' => ['firmy']
        ];
        
        $dbCategories = $categoryMapping[$category] ?? null;
        
        if ($dbCategories === null) {
            // Wszystkie artykuły
            $query = "SELECT id, title, slug, meta_description as excerpt, created_at as published_at, status, category, graphic_initials, graphic_colors, image_url FROM articles WHERE status = 'published' ORDER BY created_at DESC LIMIT 10";
            $articles = $database->select($query);
        } else {
            // Filtrowanie po kategoriach
            $placeholders = str_repeat('?,', count($dbCategories) - 1) . '?';
            $query = "SELECT id, title, slug, meta_description as excerpt, created_at as published_at, status, category, graphic_initials, graphic_colors, image_url FROM articles WHERE status = 'published' AND category IN ($placeholders) ORDER BY created_at DESC LIMIT 10";
            $articles = $database->select($query, $dbCategories);
        }
        
        $formattedArticles = [];
        foreach ($articles as $article) {
            $colors = json_decode($article['graphic_colors'], true) ?: $this->getDefaultColors($article['category']);
            
            $formattedArticles[] = [
                'id' => $article['id'],
                'title' => $article['title'],
                'slug' => $article['slug'],
                'excerpt' => $article['excerpt'] ?: substr(strip_tags($article['title']), 0, 150) . '...',
                'published_at' => date('Y-m-d', strtotime($article['published_at'])),
                'category' => $this->mapCategoryToDisplay($article['category']),
                'read_time' => '5 min',
                'graphic' => [
                    'initials' => $article['graphic_initials'] ?: $this->generateInitials($article['title']),
                    'colors' => $colors
                ],
                'image_url' => $article['image_url'] ?: null
            ];
        }
        
        // Jeśli brak artykułów w bazie, zwróć przykładowe
        if (empty($formattedArticles)) {
            return [
                [
                    'id' => 1,
                    'title' => 'Jak wybrać najlepszy kredyt gotówkowy',
                    'slug' => 'jak-wybrac-najlepszy-kredyt-gotowkowy',
                    'excerpt' => 'Poradnik wyboru kredytu gotówkowego - na co zwrócić uwagę przy porównywaniu ofert.',
                    'published_at' => '2024-01-15',
                    'category' => 'Kredyty',
                    'read_time' => '5 min',
                    'graphic' => [
                        'initials' => 'JK',
                        'colors' => $this->getDefaultColors('kredyty')
                    ],
                    'image_url' => null
                ],
                [
                    'id' => 2,
                    'title' => 'Najlepsze konta osobiste 2024',
                    'slug' => 'najlepsze-konta-osobiste-2024',
                    'excerpt' => 'Ranking najlepszych kont osobistych w 2024 roku. Sprawdź, które konto wybrać.',
                    'published_at' => '2024-01-10',
                    'category' => 'Bankowość',
                    'read_time' => '7 min',
                    'graphic' => [
                        'initials' => 'NK',
                        'colors' => $this->getDefaultColors('bankowość')
                    ],
                    'image_url' => null
                ]
            ];
        }
        
        return $formattedArticles;
    }
    
    /**
     * Mapowanie kategorii na nazwy wyświetlane
     */
    private function mapCategoryToDisplay(string $category): string
    {
        $mapping = [
            'kredyty' => 'Kredyty',
            'hipoteki' => 'Hipoteki',
            'oszczędzanie' => 'Oszczędzanie',
            'bankowość' => 'Bankowość',
            'ubezpieczenia' => 'Ubezpieczenia',
            'firmy' => 'Firmy',
            'finanse' => 'Finanse'
        ];
        
        return $mapping[$category] ?? 'Finanse';
    }
    
    /**
     * Pobieranie powiązanych artykułów
     */
    private function getRelatedArticles(string $category, int $excludeId): array
    {
        $database = new \Kaszflow\Services\Database();
        
        // Najpierw próbuj znaleźć artykuły z tej samej kategorii
        $query = "SELECT id, title, slug, meta_description as excerpt, created_at as published_at, category, graphic_initials, graphic_colors, image_url FROM articles WHERE status = 'published' AND category = :category AND id != :excludeId ORDER BY created_at DESC LIMIT 2";
        $articles = $database->select($query, ['category' => $category, 'excludeId' => $excludeId]);
        
        // Jeśli nie ma wystarczająco artykułów z tej samej kategorii, dodaj artykuły z podobnych kategorii
        if (count($articles) < 2) {
            $similarCategories = $this->getSimilarCategories($category);
            $remainingCount = 2 - count($articles);
            
            $placeholders = str_repeat('?,', count($similarCategories) - 1) . '?';
            $query = "SELECT id, title, slug, meta_description as excerpt, created_at as published_at, category, graphic_initials, graphic_colors, image_url FROM articles WHERE status = 'published' AND category IN ($placeholders) AND id != :excludeId ORDER BY created_at DESC LIMIT :limit";
            
            $params = array_merge($similarCategories, ['excludeId' => $excludeId, 'limit' => $remainingCount]);
            $additionalArticles = $database->select($query, $params);
            
            $articles = array_merge($articles, $additionalArticles);
        }
        
        $formattedArticles = [];
        foreach ($articles as $article) {
            $colors = json_decode($article['graphic_colors'] ?? '{}', true) ?: $this->getDefaultColors($article['category']);
            
            $formattedArticles[] = [
                'id' => $article['id'],
                'title' => $article['title'],
                'slug' => $article['slug'],
                'excerpt' => $article['excerpt'] ?: substr(strip_tags($article['title']), 0, 150) . '...',
                'published_at' => date('d.m.Y', strtotime($article['published_at'])),
                'category' => $this->mapCategoryToDisplay($article['category']),
                'read_time' => '5 min',
                'graphic' => [
                    'initials' => $article['graphic_initials'] ?: $this->generateInitials($article['title']),
                    'colors' => $colors
                ],
                'image_url' => $article['image_url'] ?: null
            ];
        }
        
        return array_slice($formattedArticles, 0, 2);
    }
    
    /**
     * Pobieranie podobnych kategorii
     */
    private function getSimilarCategories(string $category): array
    {
        $categoryMap = [
            'kredyty' => ['hipoteki', 'oszczędzanie'],
            'hipoteki' => ['kredyty', 'oszczędzanie'],
            'oszczędzanie' => ['kredyty', 'hipoteki', 'bankowość'],
            'bankowość' => ['oszczędzanie', 'kredyty'],
            'ubezpieczenia' => ['kredyty', 'oszczędzanie'],
            'firmy' => ['kredyty', 'bankowość']
        ];
        
        return $categoryMap[$category] ?? ['kredyty', 'oszczędzanie'];
    }
    
    /**
     * Pobieranie pojedynczego artykułu blogowego z bazy danych
     */
    private function getBlogArticle(string $slug): ?array
    {
        $database = new \Kaszflow\Services\Database();
        
        // Pobierz artykuł z bazy danych
        $query = "SELECT id, title, slug, content, meta_description, created_at as published_at, tags, keywords, graphic_initials, graphic_colors, category, image_url FROM articles WHERE slug = :slug AND status = 'published'";
        $article = $database->first($query, ['slug' => $slug]);
        
        if ($article) {
            $colors = json_decode($article['graphic_colors'], true) ?: $this->getDefaultColors($article['category']);
            
            return [
                'id' => $article['id'],
                'title' => $article['title'],
                'slug' => $article['slug'],
                'content' => $article['content'],
                'meta_description' => $article['meta_description'],
                'published_at' => date('Y-m-d', strtotime($article['published_at'])),
                'category' => $this->mapCategoryToDisplay($article['category']),
                'read_time' => '5 min',
                'tags' => json_decode($article['tags'], true) ?: [],
                'graphic' => [
                    'initials' => $article['graphic_initials'] ?: $this->generateInitials($article['title']),
                    'colors' => $colors
                ],
                'image_url' => $article['image_url'] ?: null
            ];
        }
        
        // Fallback do statycznych danych
        $articles = [
            'jak-wybrac-najlepszy-kredyt-gotowkowy' => [
                'id' => 1,
                'title' => 'Jak wybrać najlepszy kredyt gotówkowy',
                'slug' => 'jak-wybrac-najlepszy-kredyt-gotowkowy',
                'content' => '<h2>Wprowadzenie</h2><p>Wybór odpowiedniego kredytu gotówkowego to jedna z najważniejszych decyzji finansowych...</p><h2>Na co zwrócić uwagę</h2><p>Przy porównywaniu ofert kredytów gotówkowych należy zwrócić uwagę na...</p>',
                'meta_description' => 'Poradnik wyboru kredytu gotówkowego - na co zwrócić uwagę przy porównywaniu ofert.',
                'published_at' => '2024-01-15',
                'category' => 'Kredyty',
                'read_time' => '5 min',
                'tags' => ['kredyty', 'finanse', 'porównywanie'],
                'graphic' => [
                    'initials' => 'JW',
                    'colors' => $this->getDefaultColors('kredyty')
                ],
                'image_url' => null
            ]
        ];
        
        return $articles[$slug] ?? null;
    }
    
    /**
     * Pobieranie najlepszych ofert dla sekcji hero
     */
    private function getHeroOffers(): array
    {
        // Pobieranie rzeczywistych danych z cache
        $loans = $this->cacheService->getData('loans');
        $accounts = $this->cacheService->getData('accounts');
        $deposits = $this->cacheService->getData('deposits');
        
        // Obliczanie średnich
        $avgLoanRRSO = 0;
        $avgAccountFee = 0;
        $avgDepositRate = 0;
        
        if (!empty($loans)) {
            $totalRRSO = 0;
            $count = 0;
            foreach ($loans as $loan) {
                if (isset($loan['aprc'])) {
                    $totalRRSO += $loan['aprc'];
                    $count++;
                }
            }
            $avgLoanRRSO = $count > 0 ? $totalRRSO / $count : 0;
        }
        
        if (!empty($accounts)) {
            $totalFee = 0;
            $count = 0;
            foreach ($accounts as $account) {
                if (isset($account['management_fee_min'])) {
                    $totalFee += $account['management_fee_min'];
                    $count++;
                }
            }
            $avgAccountFee = $count > 0 ? $totalFee / $count : 0;
        }
        
        if (!empty($deposits)) {
            $totalRate = 0;
            $count = 0;
            foreach ($deposits as $deposit) {
                if (isset($deposit['interest_rate'])) {
                    // Sprawdź czy interest_rate jest liczbą lub tablicą
                    if (is_numeric($deposit['interest_rate'])) {
                        $totalRate += (float)$deposit['interest_rate'];
                        $count++;
                    } elseif (is_array($deposit['interest_rate'])) {
                        // Jeśli to tablica, weź maksymalną wartość
                        $rates = [];
                        foreach ($deposit['interest_rate'] as $rate) {
                            if (is_numeric($rate)) {
                                $rates[] = (float)$rate;
                            }
                        }
                        if (!empty($rates)) {
                            $maxRate = max($rates);
                            $totalRate += $maxRate;
                            $count++;
                        }
                    }
                }
            }
            $avgDepositRate = $count > 0 ? $totalRate / $count : 0;
        }
        
        // Wybieranie najlepszych ofert
        $bestLoan = null;
        $bestAccount = null;
        $bestDeposit = null;
        
        if (!empty($loans)) {
            usort($loans, function($a, $b) {
                return ($a['aprc'] ?? 999) <=> ($b['aprc'] ?? 999);
            });
            $bestLoan = $loans[0];
        }
        
        if (!empty($accounts)) {
            usort($accounts, function($a, $b) {
                $bonusA = 0;
                $bonusB = 0;
                if (!empty($a['bonus'])) {
                    preg_match('/(\d+(?:\.\d+)?)/', $a['bonus'], $matches);
                    $bonusA = isset($matches[1]) ? (float)$matches[1] : 0;
                }
                if (!empty($b['bonus'])) {
                    preg_match('/(\d+(?:\.\d+)?)/', $b['bonus'], $matches);
                    $bonusB = isset($matches[1]) ? (float)$matches[1] : 0;
                }
                return $bonusB <=> $bonusA; // Najwyższa premia
            });
            $bestAccount = $accounts[0];
        }
        
        if (!empty($deposits)) {
            usort($deposits, function($a, $b) {
                return ($b['interest_rate'] ?? 0) <=> ($a['interest_rate'] ?? 0);
            });
            $bestDeposit = $deposits[0];
        }
        
        return [
            'loan' => $bestLoan,
            'account' => $bestAccount,
            'deposit' => $bestDeposit,
            'avgLoanRRSO' => $avgLoanRRSO,
            'avgAccountFee' => $avgAccountFee,
            'avgDepositRate' => $avgDepositRate
        ];
    }
    
    /**
     * Pobieranie unikalnych banków partnerskich
     */
    private function getBankPartners(): array
    {
        // Pobieranie danych z cache
        $loans = $this->cacheService->getData('loans');
        $accounts = $this->cacheService->getData('accounts');
        $deposits = $this->cacheService->getData('deposits');
        
        // Agregacja unikalnych banków
        $allBanks = [];
        
        // Kredyty gotówkowe
        foreach ($loans as $loan) {
            $allBanks[] = [
                'name' => $loan['bank_name'],
                'logo_url' => $loan['logo_url_format'] ?? $loan['logo_url'] ?? null,
                'initials' => $this->getInitials($loan['bank_name'])
            ];
        }
        
        // Konta osobiste
        foreach ($accounts as $account) {
            $allBanks[] = [
                'name' => $account['bank_name'],
                'logo_url' => $account['logo_url_format'] ?? $account['logo_url'] ?? null,
                'initials' => $this->getInitials($account['bank_name'])
            ];
        }
        
        // Lokaty
        foreach ($deposits as $deposit) {
            $allBanks[] = [
                'name' => $deposit['bank_name'],
                'logo_url' => $deposit['logo_url_format'] ?? $deposit['logo_url'] ?? null,
                'initials' => $this->getInitials($deposit['bank_name'])
            ];
        }
        
        // Usuwanie duplikatów na podstawie nazwy banku
        $uniqueBanks = [];
        $seenNames = [];
        
        foreach ($allBanks as $bank) {
            $name = $bank['name'];
            if (!in_array($name, $seenNames)) {
                $uniqueBanks[] = $bank;
                $seenNames[] = $name;
            }
        }
        
        // Ograniczenie do 15 banków
        return array_slice($uniqueBanks, 0, 15);
    }
    
    /**
     * Generowanie inicjałów z nazwy banku
     */
    private function getInitials(string $bankName): string
    {
        $words = explode(' ', $bankName);
        $initials = '';
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        return substr($initials, 0, 2);
    }
    
    /**
     * Generowanie inicjałów z tytułu
     */
    private function generateInitials(string $title): string
    {
        $words = explode(' ', $title);
        $initials = '';
        
        foreach ($words as $word) {
            $firstChar = mb_substr(trim($word), 0, 1);
            if (mb_strlen($firstChar) > 0) {
                $initials .= mb_strtoupper($firstChar);
            }
        }
        
        return mb_substr($initials, 0, 4);
    }
    
    /**
     * Pobieranie domyślnych kolorów dla kategorii
     */
    private function getDefaultColors(string $category): array
    {
        $colorSchemes = [
            'kredyty' => [
                'from' => 'from-red-500',
                'to' => 'to-red-600',
                'text' => 'text-red-800',
                'bg' => 'bg-red-100'
            ],
            'hipoteki' => [
                'from' => 'from-purple-500',
                'to' => 'to-purple-600',
                'text' => 'text-purple-800',
                'bg' => 'bg-purple-100'
            ],
            'oszczędzanie' => [
                'from' => 'from-green-500',
                'to' => 'to-green-600',
                'text' => 'text-green-800',
                'bg' => 'bg-green-100'
            ],
            'bankowość' => [
                'from' => 'from-blue-500',
                'to' => 'to-blue-600',
                'text' => 'text-blue-800',
                'bg' => 'bg-blue-100'
            ],
            'firmy' => [
                'from' => 'from-indigo-500',
                'to' => 'to-indigo-600',
                'text' => 'text-indigo-800',
                'bg' => 'bg-indigo-100'
            ]
        ];
        
        return $colorSchemes[$category] ?? [
            'from' => 'from-gray-500',
            'to' => 'to-gray-600',
            'text' => 'text-gray-800',
            'bg' => 'bg-gray-100'
        ];
    }
} 