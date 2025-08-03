<?php

namespace Kaszflow\Services;

/**
 * Serwis automatyzacji bloga
 */
class BlogAutomationService
{
    private $openaiApiKey;
    private $claudeApiKey;
    private $trendingSources;
    private $financialKeywords;
    private $database;
    
    public function __construct()
    {
        $this->database = new Database();
        
        // Ładowanie kluczy API z bazy danych
        $this->loadApiKeys();
        
        $this->initTrendingSources();
        $this->initFinancialKeywords();
    }
    
    /**
     * Ładowanie kluczy API z bazy danych
     */
    private function loadApiKeys(): void
    {
        $openaiKey = $this->database->first(
            "SELECT setting_value FROM automation_settings WHERE setting_key = 'openai_api_key'"
        );
        $claudeKey = $this->database->first(
            "SELECT setting_value FROM automation_settings WHERE setting_key = 'claude_api_key'"
        );
        
        $this->openaiApiKey = $openaiKey['setting_value'] ?? '';
        $this->claudeApiKey = $claudeKey['setting_value'] ?? '';
        
        error_log("DEBUG: OpenAI API key loaded: " . (empty($this->openaiApiKey) ? 'EMPTY' : 'SET (' . strlen($this->openaiApiKey) . ' chars)'));
        error_log("DEBUG: Claude API key loaded: " . (empty($this->claudeApiKey) ? 'EMPTY' : 'SET (' . strlen($this->claudeApiKey) . ' chars)'));
    }
    
    /**
     * Inicjalizacja źródeł trendów
     */
    private function initTrendingSources()
    {
        $this->trendingSources = [
            'nbp_rates' => 'https://api.nbp.pl/api/exchangerates/tables/A/?format=json',
            'nbp_inflation' => 'https://api.nbp.pl/api/cenyzlota/last/30/?format=json',
            'bankier_rss' => 'https://www.bankier.pl/rss.xml',
            'money_rss' => 'https://www.money.pl/rss.xml',
            'gov_finance' => 'https://www.gov.pl/web/finanse',
            'gov_economy' => 'https://www.gov.pl/web/gospodarka',
            'google_trends' => 'https://trends.google.com/trends/api/dailytrends?hl=pl&tz=-60&geo=PL&ns=15',
            'weather' => 'https://api.open-meteo.com/v1/forecast?latitude=52.23&longitude=21.01&current=temperature_2m&timezone=Europe/Warsaw'
        ];
    }
    
    /**
     * Inicjalizacja słów kluczowych finansowych
     */
    private function initFinancialKeywords()
    {
        $this->financialKeywords = [
            'kredyt gotówkowy', 'kredyt hipoteczny', 'konto osobiste', 'lokata bankowa',
            'oprocentowanie', 'RRSO', 'prowizja bankowa', 'rata kredytu',
            'wakacje kredytowe', 'kredyt frankowy', 'WIBOR', 'NBP stopy procentowe',
            'Fundusz Gwarancyjny', 'BIK', 'KRD', 'split payment', 'podatek Belki',
            'ulga mieszkaniowa', 'Mieszkanie Plus', 'fintech', 'bankowość mobilna',
            'BLIK', 'PSD2', 'open banking', 'kryptowaluty', 'inwestowanie', 'ETF',
            'obligacje skarbowe', 'inflacja', 'drożyzna', 'ceny energii', 'kredyt 2%',
            'Bezpieczny Kredyt', 'ulga termomodernizacyjna', 'bon energetyczny', 'dodatek osłonowy'
        ];
    }
    
    /**
     * Główna metoda generowania artykułu
     */
    public function generateArticle(string $engine = 'openai'): array
    {
        try {
            // 1. Sprawdź własne tematy z ustawień (najwyższy priorytet)
            $customTopics = $this->loadCustomTopics();
            if (!empty($customTopics)) {
                // Użyj pierwszego dostępnego własnego tematu
                $selectedTopic = $customTopics[0];
            } else {
                // 2. Analiza trendujących tematów (tylko jeśli brak własnych)
                $trendingTopics = $this->analyzeTrendingTopics();
                
                // 3. Wybór tematu
                $selectedTopic = $this->selectTopic($trendingTopics);
            }
            
            // 4. Generowanie treści
            $content = $this->generateContent($selectedTopic, $engine);
            
            // 5. Parsowanie wygenerowanej treści
            $parsedContent = $this->parseGeneratedContent($content, $selectedTopic);
            
            // 6. Optymalizacja SEO
            $optimizedContent = $this->optimizeSEO($parsedContent);
            
            // 7. Zapisywanie artykułu
            $articleId = $this->saveArticle($optimizedContent);
            
            return [
                'success' => true,
                'article_id' => $articleId,
                'title' => $optimizedContent['title'],
                'content' => $optimizedContent['content'],
                'engine' => $engine
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Analiza trendujących tematów
     */
    private function analyzeTrendingTopics(): array
    {
        $topics = [];
        
        // 1. Sprawdzenie stóp procentowych NBP
        $nbpData = $this->fetchNbpRates();
        if ($nbpData && $this->hasRateChanged($nbpData)) {
            $topics[] = [
                'topic' => 'Zmiany stóp procentowych NBP',
                'priority' => 10,
                'data' => $nbpData,
                'keywords' => ['stopy procentowe', 'NBP', 'oprocentowanie kredytów', 'oprocentowanie lokat']
            ];
        }
        
        // 2. Analiza sezonowych tematów
        $seasonalTopics = $this->getSeasonalTopics();
        $topics = array_merge($topics, $seasonalTopics);
        
        // 3. Analiza RSS feeds
        $rssTopics = $this->analyzeRssFeeds();
        $topics = array_merge($topics, $rssTopics);
        
        // 4. Jeśli brak trendów, użyj evergreen content
        if (empty($topics)) {
            $topics[] = $this->getEvergreenTopic();
        }
        
        return $topics;
    }
    
    /**
     * Pobieranie danych z API NBP
     */
    private function fetchNbpRates(): ?array
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Kaszflow/1.0'
                ]
            ]);
            
            $response = file_get_contents($this->trendingSources['nbp_rates'], false, $context);
            
            if ($response === false) {
                return null;
            }
            
            return json_decode($response, true);
            
        } catch (\Exception $e) {
            error_log("NBP API error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Sprawdzenie czy zmieniły się stopy procentowe
     */
    private function hasRateChanged(array $currentData): bool
    {
        $cacheFile = __DIR__ . '/../../data/last_nbp_rates.json';
        $lastData = null;
        
        if (file_exists($cacheFile)) {
            $lastData = json_decode(file_get_contents($cacheFile), true);
        }
        
        if ($lastData === null || $lastData !== $currentData) {
            file_put_contents($cacheFile, json_encode($currentData));
            return true;
        }
        
        return false;
    }
    
    /**
     * Pobieranie tematów sezonowych
     */
    private function getSeasonalTopics(): array
    {
        $currentMonth = (int)date('n');
        $topics = [];
        
        switch ($currentMonth) {
            case 1: // Styczeń
                $topics[] = [
                    'topic' => 'Noworoczne postanowienia finansowe',
                    'priority' => 8,
                    'keywords' => ['oszczędzanie', 'budżet domowy', 'cele finansowe', 'planowanie finansowe']
                ];
                break;
            case 3: // Marzec
                $topics[] = [
                    'topic' => 'Rozliczenie PIT - ulgi i zwroty',
                    'priority' => 9,
                    'keywords' => ['PIT', 'rozliczenie podatkowe', 'ulga mieszkaniowa', 'zwrot podatku']
                ];
                break;
            case 6: // Czerwiec
                $topics[] = [
                    'topic' => 'Wakacyjne kredyty i pożyczki',
                    'priority' => 7,
                    'keywords' => ['kredyt wakacyjny', 'pożyczka na wakacje', 'finansowanie wypoczynku']
                ];
                break;
            case 9: // Wrzesień
                $topics[] = [
                    'topic' => 'Finansowanie roku szkolnego',
                    'priority' => 8,
                    'keywords' => ['kredyt studencki', 'finansowanie edukacji', 'wyprawka szkolna']
                ];
                break;
            case 11: // Listopad
                $topics[] = [
                    'topic' => 'Black Friday - jak mądrze finansować zakupy',
                    'priority' => 7,
                    'keywords' => ['Black Friday', 'zakupy ratalne', 'karty kredytowe', 'finansowanie zakupów']
                ];
                break;
        }
        
        return $topics;
    }
    
    /**
     * Analiza RSS feeds
     */
    private function analyzeRssFeeds(): array
    {
        $topics = [];
        
        foreach (['bankier_rss', 'money_rss'] as $source) {
            $rssData = $this->fetchRssFeed($this->trendingSources[$source]);
            if ($rssData) {
                $analyzedTopics = $this->analyzeRssContent($rssData);
                $topics = array_merge($topics, $analyzedTopics);
            }
        }
        
        return $topics;
    }
    
    /**
     * Pobieranie danych RSS
     */
    private function fetchRssFeed(string $url): ?array
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Kaszflow/1.0'
                ]
            ]);
            
            // Suppress warnings for failed requests
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                return null;
            }
            
            $xml = @simplexml_load_string($response);
            return $xml ? json_decode(json_encode($xml), true) : null;
            
        } catch (\Exception $e) {
            // Log error but don't throw
            return null;
        }
    }
    
    /**
     * Analiza treści RSS
     */
    private function analyzeRssContent(array $rssData): array
    {
        $topics = [];
        
        if (isset($rssData['channel']['item'])) {
            $items = is_array($rssData['channel']['item']) ? $rssData['channel']['item'] : [$rssData['channel']['item']];
            
            foreach ($items as $item) {
                $title = $item['title'] ?? '';
                $description = $item['description'] ?? '';
                $content = $title . ' ' . $description;
                
                if ($this->isFinancialTopic($content)) {
                    $keywords = $this->extractKeywords($content);
                    $topics[] = [
                        'topic' => $title,
                        'priority' => 6,
                        'keywords' => $keywords,
                        'source' => 'rss'
                    ];
                }
            }
        }
        
        return $topics;
    }
    
    /**
     * Sprawdzenie czy temat jest finansowy
     */
    private function isFinancialTopic(string $text): bool
    {
        $text = mb_strtolower($text);
        
        foreach ($this->financialKeywords as $keyword) {
            if (mb_strpos($text, mb_strtolower($keyword)) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Ekstrakcja słów kluczowych
     */
    private function extractKeywords(string $text): array
    {
        $text = mb_strtolower($text);
        $keywords = [];
        
        foreach ($this->financialKeywords as $keyword) {
            if (mb_strpos($text, mb_strtolower($keyword)) !== false) {
                $keywords[] = $keyword;
            }
        }
        
        return array_slice($keywords, 0, 5); // Maksymalnie 5 słów kluczowych
    }
    
    /**
     * Pobieranie evergreen topics
     */
    private function getEvergreenTopic(): array
    {
        $topics = [
            // Kredyty i pożyczki
            [
                'topic' => 'RRSO - Co to jest i jak obliczyć rzeczywisty koszt kredytu?',
                'keywords' => ['rrso', 'kredyt', 'koszt', 'oprocentowanie', 'marża', 'wibor'],
                'category' => 'kredyty'
            ],
            [
                'topic' => 'Refinansowanie kredytu hipotecznego - czy warto przenieść kredyt?',
                'keywords' => ['refinansowanie', 'kredyt hipoteczny', 'przeniesienie', 'oszczędności'],
                'category' => 'hipoteki'
            ],
            [
                'topic' => 'Bezpieczny Kredyt 2% - Twój kompletny przewodnik',
                'keywords' => ['bezpieczny kredyt', 'kredyt 2%', 'mieszkanie', 'hipoteka'],
                'category' => 'hipoteki'
            ],
            [
                'topic' => 'Jak przygotować się do kredytu hipotecznego - praktyczny poradnik',
                'keywords' => ['kredyt hipoteczny', 'przygotowanie', 'zdolność kredytowa', 'wkład własny'],
                'category' => 'hipoteki'
            ],
            
            // Oszczędzanie i inwestowanie
            [
                'topic' => 'Fundusz inwestycyjny - jak wybrać ten najlepszy?',
                'keywords' => ['fundusz inwestycyjny', 'inwestowanie', 'portfel', 'ryzyko'],
                'category' => 'oszczędzanie'
            ],
            [
                'topic' => 'Gdzie lokować pieniądze w czasach inflacji?',
                'keywords' => ['lokata', 'inflacja', 'oszczędzanie', 'podatek belki'],
                'category' => 'oszczędzanie'
            ],
            [
                'topic' => 'ETF vs fundusze inwestycyjne - co wybrać?',
                'keywords' => ['etf', 'fundusz', 'inwestowanie', 'portfel'],
                'category' => 'oszczędzanie'
            ],
            
            // Bankowość
            [
                'topic' => 'Split Payment: Innowacyjny sposób płatności w Polsce',
                'keywords' => ['split payment', 'płatności', 'vat', 'firma'],
                'category' => 'bankowość'
            ],
            [
                'topic' => 'Najlepsze konta osobiste 2024 - ranking',
                'keywords' => ['konto osobiste', 'bank', 'opłaty', 'premia'],
                'category' => 'bankowość'
            ],
            
            // Produkty firmowe
            [
                'topic' => 'Konto firmowe - jak wybrać najlepsze dla Twojej firmy?',
                'keywords' => ['konto firmowe', 'firma', 'biznes', 'rachunek'],
                'category' => 'firmy'
            ],
            [
                'topic' => 'Kredyt obrotowy dla firm - kompletny przewodnik',
                'keywords' => ['kredyt obrotowy', 'firma', 'finansowanie', 'biznes'],
                'category' => 'firmy'
            ],
            [
                'topic' => 'Leasing dla firm - operacyjny czy finansowy?',
                'keywords' => ['leasing', 'firma', 'operacyjny', 'finansowy'],
                'category' => 'firmy'
            ],
            [
                'topic' => 'Faktoring - nowoczesne finansowanie firm',
                'keywords' => ['faktoring', 'firma', 'finansowanie', 'faktury'],
                'category' => 'firmy'
            ]
        ];
        
        return $topics[array_rand($topics)];
    }
    
    /**
     * Wybór tematu z listy
     */
    private function selectTopic(array $topics): array
    {
        // Załaduj własne tematy z ustawień
        $customTopics = $this->loadCustomTopics();
        if (!empty($customTopics)) {
            // Użyj pierwszego dostępnego własnego tematu (bez sprawdzania duplikatów)
            foreach ($customTopics as $customTopic) {
                // Dla własnych tematów nie sprawdzamy duplikatów - użytkownik ma pełną kontrolę
                return $customTopic;
            }
        }

        // Załaduj czarną listę tematów
        $topicBlacklist = $this->loadTopicBlacklist();

        // Filtruj tematy, które nie są duplikatami i są aktualne
        $filteredTopics = [];
        
        foreach ($topics as $topic) {
            // Sprawdź czy temat nie jest duplikatem
            if ($this->checkDuplicateTopic($topic['topic'])) {
                continue;
            }
            
            // Sprawdź czy temat jest nadal aktualny
            if (!$this->isTopicStillRelevant($topic['topic'])) {
                continue;
            }

            // Sprawdź czy temat nie znajduje się na czarnej liście
            if (in_array($topic['topic'], $topicBlacklist)) {
                continue;
            }
            
            $filteredTopics[] = $topic;
        }
        
        // Jeśli wszystkie tematy są duplikatami, spróbuj znaleźć unikalny evergreen topic
        if (empty($filteredTopics)) {
            $evergreenTopics = [
                [
                    'topic' => 'Karta kredytowa - czy warto ją mieć?',
                    'keywords' => ['karta kredytowa', 'limit', 'oprocentowanie', 'bankowość'],
                    'category' => 'bankowość'
                ],
                [
                    'topic' => 'Lokata strukturyzowana - czy to dobra inwestycja?',
                    'keywords' => ['lokata strukturyzowana', 'inwestowanie', 'ryzyko'],
                    'category' => 'oszczędzanie'
                ],
                [
                    'topic' => 'Kredyt konsolidacyjny - jak połączyć zobowiązania',
                    'keywords' => ['kredyt konsolidacyjny', 'zobowiązania', 'długi'],
                    'category' => 'kredyty'
                ],
                [
                    'topic' => 'Ubezpieczenie kredytu hipotecznego - czy jest obowiązkowe?',
                    'keywords' => ['ubezpieczenie kredytu', 'kredyt hipoteczny', 'polisa'],
                    'category' => 'hipoteki'
                ],
                [
                    'topic' => 'Kredyt na samochód - gotówkowy czy leasing?',
                    'keywords' => ['kredyt samochodowy', 'leasing', 'samochód'],
                    'category' => 'kredyty'
                ]
            ];
            
            foreach ($evergreenTopics as $topic) {
                if (!$this->checkDuplicateTopic($topic['topic']) && 
                    $this->isTopicStillRelevant($topic['topic'])) {
                    return $topic;
                }
            }
            
            // Jeśli wszystkie tematy są duplikatami, zwróć pierwszy z oryginalnej listy
            return $topics[0];
        }
        
        // Wybierz losowy temat z przefiltrowanej listy
        return $filteredTopics[array_rand($filteredTopics)];
    }
    
    /**
     * Generowanie treści artykułu
     */
    private function generateContent(array $topic, string $engine = 'openai'): string
    {
        $prompt = $this->buildContentPrompt($topic);
        
        error_log("DEBUG: Generated prompt length: " . strlen($prompt) . " characters");
        if (strlen($prompt) > 4000) {
            error_log("DEBUG: WARNING - Prompt is very long, may cause API issues");
        }
        
        switch ($engine) {
            case 'claude':
                if (empty($this->claudeApiKey)) {
                    // Fallback dla braku klucza Claude
                    return $this->generateFallbackContent($topic);
                }
                $response = $this->callClaudeAI($prompt);
                break;
                
            case 'openai':
            default:
                if (empty($this->openaiApiKey)) {
                    // Fallback dla braku klucza OpenAI
                    return $this->generateFallbackContent($topic);
                }
                $response = $this->callOpenAI($prompt);
                break;
        }
        
        if (empty($response)) {
            error_log("DEBUG: API call returned empty response, using fallback content");
            return $this->generateFallbackContent($topic);
        }
        
        return $response;
    }
    
    /**
     * Generowanie treści fallback gdy brak API
     */
    private function generateFallbackContent(array $topic): string
    {
        $keywords = implode(', ', $topic['keywords']);
        $title = $topic['topic'];
        
        $content = "TYTUŁ: {$title}
META_OPIS: Dowiedz się więcej o {$title} - praktyczny przewodnik z poradami ekspertów
TREŚĆ: <h1>{$title}</h1>

<p>W dzisiejszych czasach znajomość tematu {$title} jest kluczowa dla każdego, kto chce świadomie zarządzać swoimi finansami. W tym artykule przedstawimy najważniejsze aspekty tego zagadnienia.</p>

<h2>Dlaczego to ważne?</h2>
<p>Zrozumienie {$title} pozwala na podejmowanie lepszych decyzji finansowych. Warto zwrócić uwagę na kilka kluczowych elementów:</p>
<ul>
<li>Praktyczne zastosowania w życiu codziennym</li>
<li>Korzyści długoterminowe</li>
<li>Możliwe ryzyka i jak ich unikać</li>
</ul>

<h2>Praktyczne wskazówki</h2>
<p>Oto kilka praktycznych porad dotyczących {$title}:</p>
<ol>
<li>Zawsze czytaj dokładnie wszystkie dokumenty</li>
<li>Porównuj różne oferty przed podjęciem decyzji</li>
<li>Konsultuj się z ekspertami w razie wątpliwości</li>
</ol>

<h2>Podsumowanie</h2>
<p>Pamiętaj, że wiedza to potęga. Im lepiej zrozumiesz {$title}, tym lepsze decyzje będziesz podejmować.</p>

<p><strong>Skorzystaj z naszej porównywarki, aby znaleźć najlepsze oferty!</strong></p>
TAGI: {$keywords}";
        
        return $content;
    }
    
    /**
     * Budowanie promptu dla generowania treści
     */
    private function buildContentPrompt(array $topic): string
    {
        // Załaduj własne słowa kluczowe i linki afiliacyjne
        $customKeywords = $this->loadCustomKeywords();
        $affiliateLinks = $this->loadAffiliateLinks();
        
        $keywords = implode(', ', $topic['keywords']);
        if (!empty($customKeywords)) {
            $keywords .= ', ' . implode(', ', $customKeywords);
        }
        
        $marketData = $this->getCurrentMarketData();

        $marketInfo = "AKTUALNE DANE RYNKOWE (sierpień 2025):\n";
        $marketInfo .= "- WIBOR 3M: {$marketData['wibor_3m']}\n";
        $marketInfo .= "- WIBOR 6M: {$marketData['wibor_6m']}\n";
        $marketInfo .= "- WIBOR 12M: {$marketData['wibor_12m']}\n";
        $marketInfo .= "- Marża bankowa (najniższa): {$marketData['marza_min']}\n";
        $marketInfo .= "- Oprocentowanie stałe: {$marketData['oprocentowanie_stale']}\n";
        $marketInfo .= "- Lokata (maksymalna): {$marketData['lokata_max']}\n";
        $marketInfo .= "- Inflacja: {$marketData['inflacja']}\n";
        $marketInfo .= "- Stopa referencyjna NBP: {$marketData['stopa_referencyjna']}\n";

        // Dodaj linki afiliacyjne do promptu
        $affiliateInfo = "";
        if (!empty($affiliateLinks)) {
            $affiliateInfo = "\nLINKI AFILIACYJNE DO UŻYCIA W ARTYKULE:\n";
            foreach ($affiliateLinks as $keyword => $link) {
                $affiliateInfo .= "- {$keyword}: {$link}\n";
            }
            $affiliateInfo .= "\nUżyj tych linków naturalnie w tekście, gdy temat jest odpowiedni. Nie spamuj linkami!\n";
        }

        return "Napisz ekspercki, bardzo szczegółowy artykuł na temat: '{$topic['topic']}' w języku polskim.

{$marketInfo}{$affiliateInfo}

WYMAGANIA:
- Minimum 3000 słów (bardzo szczegółowy artykuł)
- Wstęp (300-400 słów)
- 8-10 sekcji z nagłówkami <h2>
- Podsekcje z nagłówkami <h3>
- Praktyczne przykłady z konkretnymi liczbami i kalkulacjami
- Tabela porównawcza (obowiązkowo)
- Sekcja FAQ (minimum 5 pytań i odpowiedzi)
- Podsumowanie z call-to-action
- Wypunktowania, pogrubienia, kursywy, cytaty
- Linki do źródeł (jeśli możliwe)
- Linki afiliacyjne (jeśli pasują do tematu)
- Aktualne dane rynkowe z powyższych informacji
- Polskie realia i przepisy

STRUKTURA ARTYKUŁU:
1. Wstęp z wprowadzeniem do tematu
2. Sekcja \"Dlaczego to ważne?\" z wyjaśnieniem znaczenia
3. Sekcja \"Aktualne dane rynkowe\" z konkretnymi liczbami
4. Sekcja \"Porównanie ofert\" z tabelą
5. Sekcja \"Praktyczne wskazówki\" z listą kroków
6. Sekcja \"Często zadawane pytania\" (FAQ)
7. Sekcja \"Podsumowanie\" z call-to-action

FORMATOWANIE HTML (OBOWIĄZKOWE):
- <h2> dla głównych sekcji
- <h3> dla podsekcji
- <p> dla paragrafów
- <ul> dla list wypunktowanych
- <ol> dla list numerowanych
- <strong> dla kluczowych informacji
- <em> dla podkreśleń
- <table> dla tabel porównawczych
- <blockquote> dla cytatów

SŁOWA KLUCZOWE: {$keywords}

Format odpowiedzi:
TYTUŁ: [chwytliwy tytuł z pytaniem lub konkretną wartością]
META_OPIS: [opis meta do 160 znaków]
TREŚĆ: [BARDZO SZCZEGÓŁOWA treść w HTML, minimum 3000 słów, z sekcjami, tabelą, FAQ, wypunktowaniami, nagłówkami, meta-opisem i tagami]
TAGI: [5-7 tagów oddzielonych przecinkami]

Pamiętaj: Artykuł ma być profesjonalny, praktyczny, bardzo szczegółowy i sformatowany w HTML!";
    }
    
    /**
     * Wywołanie OpenAI API
     */
    private function callOpenAI(string $prompt): ?string
    {
        if (empty($this->openaiApiKey)) {
            error_log("DEBUG: OpenAI API key is empty");
            return null;
        }

        $data = [
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Jesteś ekspertem finansowym i copywriterem specjalizującym się w pisaniu wysokiej jakości artykułów o finansach osobistych w języku polskim.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 4000
        ];

        $headers = [
            'Authorization: Bearer ' . $this->openaiApiKey,
            'Content-Type: application/json'
        ];

        error_log("DEBUG: Making OpenAI API call with prompt length: " . strlen($prompt));
        error_log("DEBUG: Prompt preview: " . substr($prompt, 0, 200) . "...");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = @curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        error_log("DEBUG: OpenAI API response code: " . $httpCode);
        if ($error) {
            error_log("DEBUG: OpenAI API curl error: " . $error);
        }
        if ($response) {
            error_log("DEBUG: OpenAI API response preview: " . substr($response, 0, 200) . "...");
        }

        if ($response === false || $httpCode !== 200) {
            error_log("DEBUG: OpenAI API call failed - response: " . $response);
            return null;
        }

        $result = json_decode($response, true);
        
        if (isset($result['choices'][0]['message']['content'])) {
            $content = $result['choices'][0]['message']['content'];
            error_log("DEBUG: OpenAI API call successful, content length: " . strlen($content));
            return $content;
        }

        error_log("DEBUG: OpenAI API response parsing failed: " . $response);
        return null;
    }
    
    /**
     * Wywołanie Claude AI API
     */
    private function callClaudeAI(string $prompt): ?string
    {
        $data = [
            'model' => 'claude-3-sonnet-20240229',
            'max_tokens' => 2000,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ];
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'x-api-key: ' . $this->claudeApiKey,
                    'anthropic-version: 2023-06-01'
                ],
                'content' => json_encode($data),
                'timeout' => 60
            ]
        ]);
        
        // Suppress warnings for failed requests
        $response = @file_get_contents('https://api.anthropic.com/v1/messages', false, $context);
        
        if ($response === false) {
            return null;
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
            throw new \Exception('Claude AI API error: ' . $result['error']['message']);
        }
        
        return $result['content'][0]['text'] ?? null;
    }
    
    /**
     * Parsowanie wygenerowanej treści
     */
    private function parseGeneratedContent(string $content, array $topic): array
    {
        $parsed = [];
        
        // Debug - zapisz surową odpowiedź
        error_log("Raw AI response: " . substr($content, 0, 500) . "...");
        
        // Wyciągnięcie poszczególnych sekcji z różnymi wzorcami
        $title = $this->extractSection($content, ['TYTUŁ:', 'TITLE:', 'TYTUŁ', 'TITLE']);
        $metaDescription = $this->extractSection($content, ['META_OPIS:', 'META DESCRIPTION:', 'META_OPIS', 'META DESCRIPTION']);
        $mainContent = $this->extractSection($content, ['TREŚĆ:', 'CONTENT:', 'TREŚĆ', 'CONTENT']);
        $tags = $this->extractSection($content, ['TAGI:', 'TAGS:', 'TAGI', 'TAGS']);
        
        // Jeśli nie znaleziono sekcji, spróbuj wyciągnąć z całej treści
        if (empty($mainContent)) {
            $mainContent = $content;
        }
        
        // Jeśli nie ma tytułu, wygeneruj z tematu
        if (empty($title)) {
            $title = $topic['topic'];
        }
        
        // Jeśli nie ma meta opisu, wygeneruj z treści
        if (empty($metaDescription)) {
            $metaDescription = $this->generateMetaDescription($mainContent);
        }
        
        // Jeśli nie ma tagów, wygeneruj z słów kluczowych
        if (empty($tags)) {
            $tags = implode(', ', $topic['keywords']);
        }
        
        $parsed['title'] = trim($title);
        $parsed['meta_description'] = trim($metaDescription);
        $parsed['content'] = trim($mainContent);
        $parsed['tags'] = array_map('trim', explode(',', $tags));
        $parsed['keywords'] = $topic['keywords'];
        
        error_log("Parsed content - title: " . $parsed['title'] . ", content length: " . strlen($parsed['content']));
        
        return $parsed;
    }
    
    /**
     * Wyciągnij sekcję z treści
     */
    private function extractSection(string $content, array $patterns): string
    {
        foreach ($patterns as $pattern) {
            if (preg_match('/' . preg_quote($pattern, '/') . '\s*(.+?)(?=\n[A-Z][A-ZĄĆĘŁŃÓŚŹŻ][^:]*:|$)/s', $content, $matches)) {
                return trim($matches[1]);
            }
        }
        return '';
    }
    
    /**
     * Wygeneruj meta opis z treści
     */
    private function generateMetaDescription(string $content): string
    {
        // Usuń tagi HTML
        $text = strip_tags($content);
        
        // Weź pierwsze 150 znaków
        $description = substr($text, 0, 150);
        
        // Upewnij się, że kończy się na zdaniu
        if (preg_match('/^(.+[.!?])\s/', $description, $matches)) {
            $description = $matches[1];
        }
        
        return $description . '...';
    }
    
    /**
     * Optymalizacja SEO
     */
    private function optimizeSEO(array $content): array
    {
        // Generowanie meta title
        $content['meta_title'] = $this->generateMetaTitle($content['title']);
        
        // Optymalizacja meta description
        $content['meta_description'] = $this->optimizeMetaDescription($content['meta_description']);
        
        // Generowanie schema markup
        $content['schema_markup'] = $this->generateSchemaMarkup($content);
        
        // Optymalizacja treści
        $content['content'] = $this->optimizeContent($content['content']);
        
        return $content;
    }
    
    /**
     * Generowanie meta title
     */
    private function generateMetaTitle(string $title): string
    {
        $title = trim($title);
        if (mb_strlen($title) > 60) {
            $title = mb_substr($title, 0, 57) . '...';
        }
        return $title . ' - Kaszflow';
    }
    
    /**
     * Optymalizacja meta description
     */
    private function optimizeMetaDescription(string $description): string
    {
        $description = trim($description);
        if (mb_strlen($description) > 160) {
            $description = mb_substr($description, 0, 157) . '...';
        }
        return $description;
    }
    
    /**
     * Generowanie schema markup
     */
    private function generateSchemaMarkup(array $content): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $content['title'],
            'description' => $content['meta_description'],
            'author' => [
                '@type' => 'Organization',
                'name' => 'Kaszflow'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Kaszflow',
                'url' => 'https://kaszflow.pl'
            ],
            'datePublished' => date('c'),
            'dateModified' => date('c')
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Optymalizacja treści
     */
    private function optimizeContent(string $content): string
    {
        // Sprawdź czy treść jest już w HTML
        if (strpos($content, '<h2>') === false && strpos($content, '<h3>') === false) {
            // Jeśli nie ma HTML, dodaj podstawowe formatowanie
            $content = $this->addBasicFormatting($content);
        }
        
        // Dodanie nagłówków H2, H3 z klasami Tailwind
        $content = preg_replace('/<h2>/', '<h2 class="text-2xl font-bold mb-6 text-gray-900 border-b border-gray-200 pb-2">', $content);
        $content = preg_replace('/<h3>/', '<h3 class="text-xl font-semibold mb-4 text-gray-800 mt-6">', $content);
        
        // Dodanie stylów do paragrafów
        $content = preg_replace('/<p>/', '<p class="mb-6 leading-relaxed text-gray-700 text-base">', $content);
        
        // Dodanie list z lepszymi stylami
        $content = preg_replace('/<ul>/', '<ul class="list-disc list-inside mb-6 space-y-3 text-gray-700 ml-4">', $content);
        $content = preg_replace('/<ol>/', '<ol class="list-decimal list-inside mb-6 space-y-3 text-gray-700 ml-4">', $content);
        
        // Dodanie stylów do tabel
        $content = preg_replace('/<table>/', '<div class="overflow-x-auto mb-8"><table class="w-full border-collapse border border-gray-300 shadow-sm">', $content);
        $content = preg_replace('/<\/table>/', '</table></div>', $content);
        $content = preg_replace('/<th>/', '<th class="border border-gray-300 px-6 py-4 bg-gray-50 font-semibold text-left text-gray-900">', $content);
        $content = preg_replace('/<td>/', '<td class="border border-gray-300 px-6 py-4 text-gray-700">', $content);
        
        // Dodanie stylów do strong i em
        $content = preg_replace('/<strong>/', '<strong class="font-semibold text-gray-900">', $content);
        $content = preg_replace('/<em>/', '<em class="italic text-gray-600">', $content);
        
        // Dodanie stylów do blockquote
        $content = preg_replace('/<blockquote>/', '<blockquote class="border-l-4 border-blue-500 pl-6 italic text-gray-600 mb-6 bg-blue-50 py-4">', $content);
        
        // Dodanie stylów do FAQ
        $content = preg_replace('/<dl>/', '<dl class="space-y-4 mb-8">', $content);
        $content = preg_replace('/<dt>/', '<dt class="font-semibold text-gray-900 text-lg">', $content);
        $content = preg_replace('/<dd>/', '<dd class="text-gray-700 ml-4 mt-2">', $content);
        
        // Dodanie stylów do linków
        $content = preg_replace('/<a href="/', '<a href="', $content);
        $content = preg_replace('/<a>/', '<a class="text-blue-600 hover:text-blue-800 underline">', $content);
        
        return $content;
    }
    
    /**
     * Dodaj podstawowe formatowanie do tekstu bez HTML
     */
    private function addBasicFormatting(string $content): string
    {
        // Podziel na akapity
        $paragraphs = explode("\n\n", $content);
        $formattedContent = '';
        
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (empty($paragraph)) continue;
            
            // Sprawdź czy to nagłówek (zawiera ":" na końcu lub jest krótki)
            if (preg_match('/^[A-ZĄĆĘŁŃÓŚŹŻ][^.!?]*:$/u', $paragraph) || 
                (strlen($paragraph) < 100 && preg_match('/^[A-ZĄĆĘŁŃÓŚŹŻ]/u', $paragraph))) {
                $formattedContent .= '<h3 class="text-xl font-semibold mb-3 text-gray-800">' . $paragraph . '</h3>';
            } else {
                $formattedContent .= '<p class="mb-4 leading-relaxed text-gray-700">' . $paragraph . '</p>';
            }
        }
        
        return $formattedContent;
    }
    
    /**
     * Zapisz artykuł do bazy danych
     */
    private function saveArticle(array $content): string
    {
        // Generowanie slug z tytułu
        $slug = $this->generateSlug($content['title']);
        
        // Automatyczne kategoryzowanie
        $category = $this->categorizeArticle($content['title'], $content['keywords'] ?? []);
        
        // Generowanie grafiki
        $graphic = $this->generateArticleGraphic($content['title']);
        
        // Generowanie obrazka
        $imageUrl = $this->generateArticleImage($content['title'], $category);
        
        $data = [
            'title' => $content['title'],
            'slug' => $slug,
            'content' => $content['content'],
            'meta_description' => $content['meta_description'] ?? '',
            'meta_title' => $content['meta_title'] ?? $content['title'],
            'tags' => json_encode($content['tags'] ?? []),
            'keywords' => json_encode($content['keywords'] ?? []),
            'schema_markup' => $content['schema_markup'] ?? '',
            'category' => $category,
            'graphic_initials' => $graphic['initials'],
            'graphic_colors' => json_encode($graphic['colors']),
            'image_url' => $imageUrl,
            'status' => 'draft',
            'engine' => 'openai',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $articleId = $this->database->insert('articles', $data);
        
        return $articleId ?: 'article_' . time() . '_' . rand(1000, 9999);
    }
    
    /**
     * Generowanie slug z tytułu
     */
    private function generateSlug(string $title): string
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Cache - pobieranie
     */
    private function getCache(string $key)
    {
        $cacheFile = __DIR__ . '/../../data/cache/' . md5($key) . '.json';
        
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && isset($data['expires']) && $data['expires'] > time()) {
                return $data['value'];
            }
        }
        
        return null;
    }
    
    /**
     * Cache - zapisywanie
     */
    private function setCache(string $key, $value, int $ttl = 3600): void
    {
        $cacheDir = __DIR__ . '/../../data/cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        $cacheFile = $cacheDir . '/' . md5($key) . '.json';
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        
        file_put_contents($cacheFile, json_encode($data));
    }

    /**
     * Pobieranie aktualnych danych rynkowych
     */
    private function getCurrentMarketData(): array
    {
        $marketData = [
            'wibor_3m' => '5.75%',
            'wibor_6m' => '5.85%',
            'wibor_12m' => '5.95%',
            'marza_min' => '1.84%',
            'oprocentowanie_stale' => '5.9%',
            'lokata_max' => '7.3%',
            'inflacja' => '2.8%',
            'stopa_referencyjna' => '5.75%'
        ];
        
        // Próba pobrania aktualnych danych z NBP
        try {
            $nbpData = $this->fetchNbpRates();
            if ($nbpData) {
                $marketData['stopa_referencyjna'] = $nbpData['stopa_referencyjna'] ?? '5.75%';
            }
        } catch (\Exception $e) {
            // Użyj domyślnych danych
        }
        
        return $marketData;
    }

    /**
     * Automatyczne kategoryzowanie artykułu
     */
    private function categorizeArticle(string $title, array $keywords): string
    {
        $title = mb_strtolower($title);
        $keywords = array_map('mb_strtolower', $keywords);
        
        // Kategorie z kluczowymi słowami
        $categories = [
            'kredyty' => [
                'kredyt', 'pożyczka', 'zobowiązanie', 'spłata', 'rata', 'oprocentowanie',
                'marża', 'wibor', 'rrso', 'zdolność kredytowa', 'wkład własny', 'refinansowanie',
                'nadpłata', 'wakacje kredytowe', 'konsolidacja', 'kredyt gotówkowy', 'kredyt konsolidacyjny'
            ],
            'hipoteki' => [
                'kredyt hipoteczny', 'hipoteka', 'mieszkanie', 'nieruchomość', 'zakup mieszkania',
                'wkład własny', 'zdolność kredytowa', 'bezpieczny kredyt', 'kredyt 2%', 'ulga mieszkaniowa',
                'dom', 'nieruchomości', 'zakup domu'
            ],
            'oszczędzanie' => [
                'oszczędzanie', 'lokata', 'fundusz', 'inwestowanie', 'portfel', 'inflacja',
                'podatek belki', 'bon energetyczny', 'dodatek', 'wsparcie', 'etf', 'akcje',
                'obligacje', 'lokata strukturyzowana', 'fundusz inwestycyjny', 'oszczędności',
                'konto oszczędnościowe', 'depozyt'
            ],
            'bankowość' => [
                'konto', 'bank', 'przelew', 'blik', 'split payment', 'fintech', 'bankowość',
                'karta kredytowa', 'limit kredytowy', 'opłaty bankowe', 'konto osobiste',
                'karta debetowa', 'bankowość internetowa'
            ],
            'firmy' => [
                'firma', 'biznes', 'konto firmowe', 'finansowanie firmy', 'leasing',
                'kredyt firmowy', 'podatki', 'vat', 'rachunek firmowy', 'kredyt obrotowy',
                'kredyt inwestycyjny', 'leasing operacyjny', 'leasing finansowy',
                'faktoring', 'forfaiting', 'kredyt kupiecki', 'przedsiębiorstwo'
            ],
            'ubezpieczenia' => [
                'ubezpieczenie', 'polisa', 'oc', 'ac', 'nnw', 'życie', 'zdrowie', 'majątek',
                'shooter protect', 'ochrona', 'ryzyko', 'składka', 'odszkodowanie',
                'ubezpieczenie komunikacyjne', 'ubezpieczenie mieszkania', 'ubezpieczenie podróżne'
            ]
        ];
        
        // Sprawdź dopasowania
        $scores = [];
        foreach ($categories as $category => $words) {
            $score = 0;
            
            // Sprawdź tytuł
            foreach ($words as $word) {
                if (mb_strpos($title, $word) !== false) {
                    $score += 3; // Tytuł ma większą wagę
                }
            }
            
            // Sprawdź słowa kluczowe
            foreach ($keywords as $keyword) {
                foreach ($words as $word) {
                    if (mb_strpos($keyword, $word) !== false || mb_strpos($word, $keyword) !== false) {
                        $score += 2;
                    }
                }
            }
            
            if ($score > 0) {
                $scores[$category] = $score;
            }
        }
        
        // Wybierz kategorię z najwyższym wynikiem
        if (!empty($scores)) {
            arsort($scores);
            return array_key_first($scores);
        }
        
        // Jeśli nie ma dopasowania, spróbuj bardziej ogólne kategorie
        if (mb_strpos($title, 'finans') !== false || mb_strpos($title, 'pieniądz') !== false) {
            return 'oszczędzanie'; // Finanse ogólne -> oszczędzanie
        }
        
        // Domyślna kategoria
        return 'bankowość';
    }

    /**
     * Sprawdzanie duplikatów tematów
     */
    private function checkDuplicateTopic(string $topic): bool
    {
        $database = new \Kaszflow\Services\Database();
        
        // Wyciągnij kluczowe słowa z tematu
        $topicLower = mb_strtolower($topic);
        $words = explode(' ', $topicLower);
        
        // Filtruj słowa (usuń krótkie słowa i słowa stop)
        $stopWords = ['co', 'to', 'jest', 'jak', 'dla', 'twoj', 'twoja', 'przewodnik', 'kompletny', 'praktyczny', 'poradnik', 'najlepszy', 'nowy', '2025', '2024'];
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return mb_strlen($word) > 3 && !in_array($word, $stopWords);
        });
        
        // Sprawdź czy podobny temat już istnieje (tylko dla bardzo podobnych tematów)
        $topicWords = array_slice($keywords, 0, 3); // Weź tylko pierwsze 3 słowa kluczowe
        
        foreach ($topicWords as $keyword) {
            $query = "SELECT COUNT(*) as count FROM articles 
                      WHERE status = 'published' 
                      AND created_at >= datetime('now', '-30 days')
                      AND LOWER(title) LIKE :keyword";
            
            $result = $database->first($query, ['keyword' => '%' . $keyword . '%']);
            
            if (($result['count'] ?? 0) > 2) { // Tylko jeśli więcej niż 2 artykuły z tym słowem w ostatnich 30 dniach
                return true; // Znaleziono duplikat
            }
        }
        
        return false;
    }
    
    /**
     * Sprawdzanie czy temat jest aktualny (nie ma znaczących zmian)
     */
    private function isTopicStillRelevant(string $topic): bool
    {
        // Lista tematów, które wymagają aktualizacji ze względu na zmiany regulacyjne
        $topicsRequiringUpdate = [
            'bezpieczny kredyt 2%' => '2024-01-01', // Sprawdź czy program nadal działa
            'wakacje kredytowe' => '2024-01-01', // Sprawdź czy moratorium nadal obowiązuje
            'bon energetyczny' => '2024-01-01', // Sprawdź czy bon nadal dostępny
            'ulga mieszkaniowa' => '2024-01-01', // Sprawdź czy ulga nadal obowiązuje
            'wibor' => '2024-01-01', // Sprawdź czy nie zmieniono na WIRON
            'rrso' => '2024-01-01', // Sprawdź czy nie zmieniono metodologii
        ];
        
        $topicLower = mb_strtolower($topic);
        
        foreach ($topicsRequiringUpdate as $keyword => $lastUpdate) {
            if (mb_strpos($topicLower, $keyword) !== false) {
                // Sprawdź czy minął rok od ostatniej aktualizacji
                $lastUpdateDate = new \DateTime($lastUpdate);
                $now = new \DateTime();
                $diff = $now->diff($lastUpdateDate);
                
                return $diff->y >= 1; // Można pisać ponownie po roku
            }
        }
        
        return true; // Temat nie wymaga aktualizacji
    }

    /**
     * Generowanie grafiki artykułu na podstawie tytułu
     */
    private function generateArticleGraphic(string $title): array
    {
        // Wyciągnij inicjały z tytułu (pierwsze litery każdego słowa)
        $words = explode(' ', $title);
        $initials = '';
        
        foreach ($words as $word) {
            $firstChar = mb_substr(trim($word), 0, 1);
            if (mb_strlen($firstChar) > 0) {
                $initials .= mb_strtoupper($firstChar);
            }
        }
        
        // Ogranicz do maksymalnie 4 inicjałów
        $initials = mb_substr($initials, 0, 4);
        
        // Jeśli brak inicjałów, użyj pierwszych liter tytułu
        if (empty($initials)) {
            $initials = mb_strtoupper(mb_substr($title, 0, 2));
        }
        
        // Wybierz kolor na podstawie kategorii
        $category = $this->categorizeArticle($title, []);
        $colors = $this->getCategoryColors($category);
        
        return [
            'initials' => $initials,
            'colors' => $colors,
            'category' => $category
        ];
    }
    
    /**
     * Generowanie obrazka artykułu za pomocą OpenAI DALL-E
     */
    private function generateArticleImage(string $title, string $category): ?string
    {
        if (empty($this->openaiApiKey)) {
            error_log("OpenAI API key is empty");
            return null;
        }
        
        // Przygotuj prompt dla obrazka
        $prompt = $this->buildImagePrompt($title, $category);
        
        $data = [
            'model' => 'dall-e-3',
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1024x1024',
            'quality' => 'standard',
            'style' => 'natural'
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->openaiApiKey,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/images/generations');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = @curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("cURL error: " . $error);
            return null;
        }
        
        if ($httpCode === 200 && $response) {
            $result = json_decode($response, true);
            if (isset($result['data'][0]['url'])) {
                $imageUrl = $result['data'][0]['url'];
                error_log("Generated image URL: " . $imageUrl);
                
                // Pobierz i zapisz obrazek lokalnie
                return $this->downloadAndSaveImage($imageUrl, $title);
            } else {
                error_log("No image URL in response: " . $response);
            }
        } else {
            error_log("HTTP error: " . $httpCode . ", Response: " . $response);
        }
        
        // Jeśli nie udało się wygenerować obrazka, zwróć null
        error_log("Failed to generate image for title: " . $title);
        return null;
    }
    
    /**
     * Pobierz i zapisz obrazek lokalnie
     */
    private function downloadAndSaveImage(string $imageUrl, string $title): ?string
    {
        // Utwórz katalog dla obrazków jeśli nie istnieje
        $imagesDir = 'assets/images/blog';
        if (!is_dir($imagesDir)) {
            mkdir($imagesDir, 0755, true);
        }
        
        // Generuj unikalną nazwę pliku - używaj PNG zamiast WebP
        $slug = $this->generateSlug($title);
        $filename = $slug . '_' . time() . '.png';
        $filepath = $imagesDir . '/' . $filename;
        
        error_log("Attempting to download image from: " . $imageUrl);
        error_log("Saving to: " . $filepath);
        
        // Pobierz obrazek
        $imageData = @file_get_contents($imageUrl);
        if ($imageData === false) {
            error_log("Failed to download image from: " . $imageUrl);
            return null;
        }
        
        error_log("Downloaded " . strlen($imageData) . " bytes");
        
        // Zapisz obrazek
        if (file_put_contents($filepath, $imageData) === false) {
            error_log("Failed to save image to: " . $filepath);
            return null;
        }
        
        error_log("Successfully saved image to: " . $filepath);
        
        // Zwróć ścieżkę względną do obrazka
        return '/assets/images/blog/' . $filename;
    }
    
    /**
     * Zregeneruj obrazki dla istniejących artykułów
     */
    public function regenerateImagesForExistingArticles(): array
    {
        error_log("=== REGENERATE IMAGES FUNCTION STARTED ===");
        
        $database = new \Kaszflow\Services\Database();
        
        // Pobierz wszystkie artykuły
        $articles = $database->select(
            "SELECT id, title, category, image_url FROM articles ORDER BY created_at DESC"
        );
        
        error_log("Found " . count($articles) . " articles to process");
        
        // Test: sprawdź pierwszy artykuł
        if (!empty($articles)) {
            $firstArticle = $articles[0];
            $testFilepath = 'assets/images/blog/' . basename($firstArticle['image_url']);
            $fileExists = file_exists($testFilepath) ? 'YES' : 'NO';
            error_log("First article test - ID: {$firstArticle['id']}, image_url: {$firstArticle['image_url']}, file_exists: {$fileExists}");
            
            // Zwróć informacje o teście w odpowiedzi
            $this->testInfo = [
                'first_article_id' => $firstArticle['id'],
                'first_article_image_url' => $firstArticle['image_url'],
                'test_filepath' => $testFilepath,
                'file_exists' => $fileExists,
                'current_dir' => getcwd(),
                'absolute_path' => realpath($testFilepath) ?: 'NOT_FOUND',
                'file_exists_direct' => file_exists($testFilepath) ? 'YES' : 'NO'
            ];
        }
        
        $results = [];
        
        foreach ($articles as $article) {
            try {
                $needsRegeneration = false;
                
                // Debug: wyświetl informacje o artykule
                error_log("Processing article {$article['id']}: {$article['title']} - image_url: {$article['image_url']}");
                
                // Sprawdź czy artykuł potrzebuje regeneracji obrazka
                if (empty($article['image_url'])) {
                    $needsRegeneration = true;
                    error_log("Article {$article['id']} needs regeneration: empty image_url");
                } elseif (strpos($article['image_url'], 'https://oaidalleapiprodscus.blob.core.windows.net') !== false) {
                    // URL DALL-E - wygasły
                    $needsRegeneration = true;
                    error_log("Article {$article['id']} needs regeneration: DALL-E URL");
                } elseif (strpos($article['image_url'], '/assets/images/blog/') !== false) {
                    // Lokalny plik - sprawdź czy istnieje
                    // Aplikacja działa z katalogu public/, więc ścieżka powinna być względna
                    $filepath = 'assets/images/blog/' . basename($article['image_url']);
                    error_log("Checking file: {$filepath}");
                    if (!file_exists($filepath)) {
                        $needsRegeneration = true;
                        error_log("Article {$article['id']} needs regeneration: file missing: {$filepath}");
                    } else {
                        error_log("Article {$article['id']} has existing file: {$filepath}");
                    }
                }
                
                if (!$needsRegeneration) {
                    error_log("Article {$article['id']} skipped - no regeneration needed");
                    continue; // Pomiń artykuły z działającymi obrazkami
                }
                
                // Generuj nowy obrazek
                $newImageUrl = $this->generateArticleImage($article['title'], $article['category']);
                
                if ($newImageUrl) {
                    // Zaktualizuj artykuł w bazie danych
                    $database->update(
                        'articles',
                        ['image_url' => $newImageUrl],
                        'id = :id',
                        ['id' => $article['id']]
                    );
                    
                    $results[] = [
                        'id' => $article['id'],
                        'title' => $article['title'],
                        'success' => true,
                        'new_image_url' => $newImageUrl
                    ];
                } else {
                    $results[] = [
                        'id' => $article['id'],
                        'title' => $article['title'],
                        'success' => false,
                        'error' => 'Failed to generate image'
                    ];
                }
                
                // Dodaj małe opóźnienie między generowaniem obrazków
                sleep(1);
                
            } catch (\Exception $e) {
                $results[] = [
                    'id' => $article['id'],
                    'title' => $article['title'],
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
    
    /**
     * Budowanie promptu dla obrazka
     */
    private function buildImagePrompt(string $title, string $category): string
    {
        $categoryPrompts = [
            'kredyty' => 'simple loan, minimal credit',
            'hipoteki' => 'simple house, minimal home',
            'oszczędzanie' => 'simple savings, minimal piggy bank',
            'bankowość' => 'simple bank, minimal account',
            'firmy' => 'simple business, minimal office',
            'ubezpieczenia' => 'simple insurance, minimal protection',
            'finanse' => 'simple finance, minimal money'
        ];
        
        $basePrompt = $categoryPrompts[$category] ?? 'simple finance, minimal document';
        
        // Dodaj elementy z tytułu - proste i minimalne
        $titleLower = mb_strtolower($title);
        $additionalElements = [];
        
        if (mb_strpos($titleLower, 'kredyt') !== false) {
            $additionalElements[] = 'simple loan, minimal calculator';
        }
        if (mb_strpos($titleLower, 'hipotek') !== false) {
            $additionalElements[] = 'simple house, minimal keys';
        }
        if (mb_strpos($titleLower, 'fundusz') !== false) {
            $additionalElements[] = 'simple chart, minimal growth';
        }
        if (mb_strpos($titleLower, 'lokata') !== false) {
            $additionalElements[] = 'simple piggy bank, minimal coins';
        }
        if (mb_strpos($titleLower, 'konto') !== false) {
            $additionalElements[] = 'simple account, minimal banking';
        }
        if (mb_strpos($titleLower, 'leasing') !== false) {
            $additionalElements[] = 'simple car, minimal contract';
        }
        if (mb_strpos($titleLower, 'rrso') !== false) {
            $additionalElements[] = 'simple calculation, minimal formula';
        }
        if (mb_strpos($titleLower, 'split payment') !== false) {
            $additionalElements[] = 'simple payment, minimal digital';
        }
        if (mb_strpos($titleLower, 'bezpieczny kredyt') !== false) {
            $additionalElements[] = 'simple house, minimal government';
        }
        if (mb_strpos($titleLower, 'bon energetyczny') !== false) {
            $additionalElements[] = 'simple energy, minimal savings';
        }
        if (mb_strpos($titleLower, 'refinansowanie') !== false) {
            $additionalElements[] = 'simple refinancing, minimal debt';
        }
        if (mb_strpos($titleLower, 'faktoring') !== false) {
            $additionalElements[] = 'simple invoice, minimal business';
        }
        
        $elements = array_merge([$basePrompt], $additionalElements);
        $prompt = implode(', ', $elements);
        
        return "Very simple, minimalist illustration of: $prompt. Style: extremely clean, minimal geometric shapes, no complex details, no text, abstract representation, professional financial theme, modern minimalist design. The image should be very simple and not cluttered.";
    }
    
    /**
     * Pobieranie kolorów dla kategorii
     */
    private function getCategoryColors(string $category): array
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
            ],
            'ubezpieczenia' => [
                'from' => 'from-orange-500',
                'to' => 'to-orange-600',
                'text' => 'text-orange-800',
                'bg' => 'bg-orange-100'
            ],
            'finanse' => [
                'from' => 'from-gray-500',
                'to' => 'to-gray-600',
                'text' => 'text-gray-800',
                'bg' => 'bg-gray-100'
            ]
        ];
        
        return $colorSchemes[$category] ?? $colorSchemes['bankowość'];
    }

    /**
     * Załaduj własne tematy z ustawień
     */
    private function loadCustomTopics(): array
    {
        $customTopics = $this->database->select(
            "SELECT setting_value FROM automation_settings WHERE setting_key = 'custom_topics'"
        );
        
        if (empty($customTopics) || empty($customTopics[0]['setting_value'])) {
            return [];
        }
        
        $topics = explode("\n", trim($customTopics[0]['setting_value']));
        $formattedTopics = [];
        
        foreach ($topics as $topic) {
            $topic = trim($topic);
            if (!empty($topic)) {
                $formattedTopics[] = [
                    'topic' => $topic,
                    'keywords' => $this->extractKeywordsFromTopic($topic),
                    'category' => $this->categorizeArticle($topic, [])
                ];
            }
        }
        
        return $formattedTopics;
    }

    /**
     * Załaduj własne słowa kluczowe z ustawień
     */
    private function loadCustomKeywords(): array
    {
        $customKeywords = $this->database->select(
            "SELECT setting_value FROM automation_settings WHERE setting_key = 'custom_keywords'"
        );
        
        error_log("DEBUG: Raw custom keywords from DB: " . ($customKeywords[0]['setting_value'] ?? 'EMPTY'));
        
        if (empty($customKeywords) || empty($customKeywords[0]['setting_value'])) {
            error_log("DEBUG: No custom keywords found in database");
            return [];
        }
        
        $keywords = explode("\n", trim($customKeywords[0]['setting_value']));
        $filteredKeywords = array_filter(array_map('trim', $keywords));
        
        error_log("DEBUG: Custom keywords loaded: " . count($filteredKeywords));
        error_log("DEBUG: Keywords: " . implode(', ', $filteredKeywords));
        
        return $filteredKeywords;
    }

    /**
     * Załaduj linki afiliacyjne z ustawień
     */
    private function loadAffiliateLinks(): array
    {
        $affiliateLinks = $this->database->select(
            "SELECT setting_value FROM automation_settings WHERE setting_key = 'affiliate_links'"
        );
        
        error_log("DEBUG: Raw affiliate links from DB: " . ($affiliateLinks[0]['setting_value'] ?? 'EMPTY'));
        
        if (empty($affiliateLinks) || empty($affiliateLinks[0]['setting_value'])) {
            error_log("DEBUG: No affiliate links found in database");
            return [];
        }
        
        $links = explode("\n", trim($affiliateLinks[0]['setting_value']));
        error_log("DEBUG: Exploded links count: " . count($links));
        
        $formattedLinks = [];
        
        foreach ($links as $link) {
            $link = trim($link);
            error_log("DEBUG: Processing link: " . $link);
            
            if (!empty($link) && strpos($link, '|') !== false) {
                $parts = explode('|', $link, 2);
                $keyword = trim($parts[0]);
                $url = trim($parts[1]);
                $formattedLinks[$keyword] = $url;
                error_log("DEBUG: Added affiliate link: {$keyword} => {$url}");
            } else {
                error_log("DEBUG: Skipped link (no pipe or empty): " . $link);
            }
        }
        
        error_log("DEBUG: Final formatted links count: " . count($formattedLinks));
        return $formattedLinks;
    }

    /**
     * Załaduj czarną listę tematów
     */
    private function loadTopicBlacklist(): array
    {
        $blacklist = $this->database->select(
            "SELECT setting_value FROM automation_settings WHERE setting_key = 'topic_blacklist'"
        );
        
        if (empty($blacklist) || empty($blacklist[0]['setting_value'])) {
            return [];
        }
        
        $topics = explode("\n", trim($blacklist[0]['setting_value']));
        return array_filter(array_map('trim', $topics));
    }

    /**
     * Wyciągnij słowa kluczowe z tematu
     */
    private function extractKeywordsFromTopic(string $topic): array
    {
        $keywords = [];
        $topicLower = mb_strtolower($topic);
        
        // Dodaj słowa kluczowe na podstawie tematu
        if (mb_strpos($topicLower, 'kredyt') !== false) {
            $keywords = array_merge($keywords, ['kredyt', 'pożyczka', 'finansowanie', 'bank', 'oprocentowanie']);
        }
        if (mb_strpos($topicLower, 'hipotek') !== false) {
            $keywords = array_merge($keywords, ['hipoteka', 'nieruchomość', 'dom', 'mieszkanie', 'kredyt hipoteczny']);
        }
        if (mb_strpos($topicLower, 'oszczędzanie') !== false || mb_strpos($topicLower, 'lokata') !== false) {
            $keywords = array_merge($keywords, ['oszczędzanie', 'lokata', 'inwestycje', 'fundusz', 'portfel']);
        }
        if (mb_strpos($topicLower, 'firma') !== false || mb_strpos($topicLower, 'biznes') !== false) {
            $keywords = array_merge($keywords, ['firma', 'biznes', 'przedsiębiorstwo', 'finanse firmowe']);
        }
        
        // Dodaj ogólne słowa kluczowe
        $keywords = array_merge($keywords, ['finanse', 'pieniądze', 'inwestycje', 'planowanie finansowe']);
        
        return array_unique($keywords);
    }

    /**
     * Generowanie sugestii tematów na podstawie analizy danych
     */
    public function generateTopicSuggestions(): array
    {
        $suggestions = [];
        
        // Analiza danych rynkowych
        $marketSuggestions = $this->analyzeMarketDataForSuggestions();
        $suggestions = array_merge($suggestions, $marketSuggestions);
        
        // Analiza RSS feedów
        $rssSuggestions = $this->analyzeRssForSuggestions();
        $suggestions = array_merge($suggestions, $rssSuggestions);
        
        // Analiza sezonowa
        $seasonalSuggestions = $this->analyzeSeasonalTrends();
        $suggestions = array_merge($suggestions, $seasonalSuggestions);
        
        // Analiza popularnych wyszukiwań
        $searchSuggestions = $this->analyzeSearchTrends();
        $suggestions = array_merge($suggestions, $searchSuggestions);
        
        // Sortowanie według priorytetu
        usort($suggestions, function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        
        // Ograniczenie do top 10 sugestii
        return array_slice($suggestions, 0, 10);
    }

    /**
     * Analiza danych rynkowych dla sugestii
     */
    private function analyzeMarketDataForSuggestions(): array
    {
        $suggestions = [];
        $marketData = $this->getCurrentMarketData();
        
        // Analiza WIBOR
        $wibor3m = (float)str_replace(',', '.', $marketData['wibor_3m']);
        $wibor6m = (float)str_replace(',', '.', $marketData['wibor_6m']);
        $wibor12m = (float)str_replace(',', '.', $marketData['wibor_12m']);
        
        if ($wibor3m > 5.5) {
            $suggestions[] = [
                'topic' => 'Wysokie stopy procentowe - jak chronić swoje finanse?',
                'category' => 'finanse osobiste',
                'priority' => 9,
                'reason' => 'WIBOR 3M: ' . $marketData['wibor_3m'] . '% - bardzo wysokie stopy',
                'keywords' => ['wibor', 'stopy procentowe', 'inflacja', 'oszczędzanie']
            ];
        }
        
        if ($wibor12m > 6.0) {
            $suggestions[] = [
                'topic' => 'Kredyty hipoteczne w dobie wysokich stóp - czy warto czekać?',
                'category' => 'hipoteki',
                'priority' => 8,
                'reason' => 'WIBOR 12M: ' . $marketData['wibor_12m'] . '% - wpływ na kredyty',
                'keywords' => ['kredyt hipoteczny', 'wibor', 'stopy procentowe', 'nieruchomości']
            ];
        }
        
        // Analiza inflacji
        $inflacja = (float)str_replace(',', '.', $marketData['inflacja']);
        if ($inflacja > 8.0) {
            $suggestions[] = [
                'topic' => 'Inflacja ' . $marketData['inflacja'] . '% - jak zabezpieczyć swoje oszczędności?',
                'category' => 'oszczędzanie',
                'priority' => 9,
                'reason' => 'Wysoka inflacja wymaga strategii ochrony oszczędności',
                'keywords' => ['inflacja', 'oszczędzanie', 'inwestycje', 'ochrona kapitału']
            ];
        }
        
        // Analiza lokat
        $lokataMax = (float)str_replace(',', '.', $marketData['lokata_max']);
        if ($lokataMax > 7.0) {
            $suggestions[] = [
                'topic' => 'Lokaty ' . $marketData['lokata_max'] . '% - czy to najlepsza opcja?',
                'category' => 'oszczędzanie',
                'priority' => 7,
                'reason' => 'Atrakcyjne oprocentowanie lokat',
                'keywords' => ['lokata', 'oszczędzanie', 'oprocentowanie', 'depozyty']
            ];
        }
        
        return $suggestions;
    }

    /**
     * Analiza RSS feedów dla sugestii
     */
    private function analyzeRssForSuggestions(): array
    {
        $suggestions = [];
        $rssData = $this->analyzeRssFeeds();
        
        foreach ($rssData as $source => $topics) {
            foreach ($topics as $topic) {
                $title = mb_strtolower($topic['title']);
                
                // Analiza tematów związanych z kredytami
                if (mb_strpos($title, 'kredyt') !== false || mb_strpos($title, 'pożyczka') !== false) {
                    $suggestions[] = [
                        'topic' => 'Aktualności kredytowe: ' . $topic['title'],
                        'category' => 'kredyty',
                        'priority' => 7,
                        'reason' => 'Trending w mediach: ' . $source,
                        'keywords' => ['kredyt', 'pożyczka', 'finansowanie', 'bank']
                    ];
                }
                
                // Analiza tematów związanych z bankowością
                if (mb_strpos($title, 'bank') !== false || mb_strpos($title, 'konto') !== false) {
                    $suggestions[] = [
                        'topic' => 'Nowości bankowe: ' . $topic['title'],
                        'category' => 'bankowość',
                        'priority' => 6,
                        'reason' => 'Aktualność w mediach: ' . $source,
                        'keywords' => ['bank', 'konto', 'bankowość', 'usługi finansowe']
                    ];
                }
                
                // Analiza tematów związanych z inwestycjami
                if (mb_strpos($title, 'fundusz') !== false || mb_strpos($title, 'inwestycje') !== false) {
                    $suggestions[] = [
                        'topic' => 'Trendy inwestycyjne: ' . $topic['title'],
                        'category' => 'oszczędzanie',
                        'priority' => 6,
                        'reason' => 'Popularny temat: ' . $source,
                        'keywords' => ['fundusz', 'inwestycje', 'portfel', 'rynki finansowe']
                    ];
                }
            }
        }
        
        return $suggestions;
    }

    /**
     * Analiza trendów sezonowych
     */
    private function analyzeSeasonalTrends(): array
    {
        $suggestions = [];
        $currentMonth = (int)date('n');
        $currentDay = (int)date('j');
        
        // Sezonowe tematy
        if ($currentMonth >= 3 && $currentMonth <= 5) {
            $suggestions[] = [
                'topic' => 'Wiosenne porządki finansowe - jak przygotować się do nowego roku?',
                'category' => 'finanse osobiste',
                'priority' => 6,
                'reason' => 'Sezon wiosenny - czas na przegląd finansów',
                'keywords' => ['planowanie finansowe', 'budżet', 'oszczędzanie', 'wiosna']
            ];
        }
        
        if ($currentMonth >= 9 && $currentMonth <= 11) {
            $suggestions[] = [
                'topic' => 'Jesienne inwestycje - jak przygotować się do zimy?',
                'category' => 'oszczędzanie',
                'priority' => 6,
                'reason' => 'Sezon jesienny - czas na zabezpieczenie finansowe',
                'keywords' => ['inwestycje', 'oszczędzanie', 'zabezpieczenie', 'jesień']
            ];
        }
        
        if ($currentMonth === 12) {
            $suggestions[] = [
                'topic' => 'Świąteczne wydatki - jak nie zrujnować budżetu?',
                'category' => 'finanse osobiste',
                'priority' => 8,
                'reason' => 'Sezon świąteczny - wysokie wydatki',
                'keywords' => ['budżet', 'wydatki', 'święta', 'oszczędzanie']
            ];
        }
        
        if ($currentMonth === 1) {
            $suggestions[] = [
                'topic' => 'Noworoczne postanowienia finansowe - jak je zrealizować?',
                'category' => 'finanse osobiste',
                'priority' => 7,
                'reason' => 'Początek roku - czas na nowe cele finansowe',
                'keywords' => ['cele finansowe', 'planowanie', 'nowy rok', 'oszczędzanie']
            ];
        }
        
        // Tematy związane z podatkami
        if ($currentMonth >= 2 && $currentMonth <= 4) {
            $suggestions[] = [
                'topic' => 'Rozliczenie PIT - jak maksymalnie wykorzystać ulgi?',
                'category' => 'finanse osobiste',
                'priority' => 8,
                'reason' => 'Sezon podatkowy',
                'keywords' => ['pit', 'podatki', 'ulgi', 'rozliczenie']
            ];
        }
        
        return $suggestions;
    }

    /**
     * Analiza trendów wyszukiwania
     */
    private function analyzeSearchTrends(): array
    {
        $suggestions = [];
        
        // Symulacja danych z Google Trends (w rzeczywistości można dodać API)
        $trendingTopics = [
            'kredyt hipoteczny' => 85,
            'lokata' => 78,
            'inflacja' => 92,
            'fundusz inwestycyjny' => 65,
            'konto osobiste' => 72,
            'karta kredytowa' => 68,
            'leasing' => 55,
            'faktoring' => 45
        ];
        
        foreach ($trendingTopics as $topic => $popularity) {
            if ($popularity > 70) {
                $suggestions[] = [
                    'topic' => 'Trending: ' . ucfirst($topic) . ' - co warto wiedzieć w 2025?',
                    'category' => $this->categorizeArticle($topic, []),
                    'priority' => $popularity / 10,
                    'reason' => 'Wysokie zainteresowanie w wyszukiwarkach',
                    'keywords' => explode(' ', $topic)
                ];
            }
        }
        
        return $suggestions;
    }

    /**
     * Pobierz sugestie tematów na dany tydzień
     */
    public function getWeeklySuggestions(): array
    {
        $suggestions = $this->generateTopicSuggestions();
        
        // Filtruj wygenerowane sugestie
        $suggestions = $this->filterGeneratedSuggestions($suggestions);
        
        // Dodaj informacje o tygodniu
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        
        return [
            'week' => [
                'start' => $weekStart,
                'end' => $weekEnd,
                'period' => date('d.m.Y', strtotime($weekStart)) . ' - ' . date('d.m.Y', strtotime($weekEnd))
            ],
            'suggestions' => $suggestions,
            'total' => count($suggestions),
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Filtruj wygenerowane sugestie
     */
    private function filterGeneratedSuggestions(array $suggestions): array
    {
        $generatedSuggestionsFile = __DIR__ . '/../../data/generated_suggestions.json';
        
        if (!file_exists($generatedSuggestionsFile)) {
            return $suggestions;
        }
        
        $generatedSuggestions = json_decode(file_get_contents($generatedSuggestionsFile), true) ?? [];
        $generatedTopics = array_column($generatedSuggestions, 'topic');
        
        return array_filter($suggestions, function($suggestion) use ($generatedTopics) {
            return !in_array($suggestion['topic'], $generatedTopics);
        });
    }

    /**
     * Generuj artykuł z konkretnego tematu
     */
    public function generateArticleFromTopic(array $topic, string $engine = 'openai'): array
    {
        try {
            // Sprawdź czy temat nie jest duplikatem
            if ($this->checkDuplicateTopic($topic['topic'])) {
                return [
                    'success' => false,
                    'error' => 'Temat już był poruszany w ostatnim czasie'
                ];
            }
            
            // Generuj treść
            $rawContent = $this->generateContent($topic, $engine);
            
            if (empty($rawContent)) {
                return [
                    'success' => false,
                    'error' => 'Nie udało się wygenerować treści'
                ];
            }
            
            // Parsuj wygenerowaną treść
            $content = $this->parseGeneratedContent($rawContent, $topic);
            
            if (empty($content)) {
                return [
                    'success' => false,
                    'error' => 'Nie udało się sparsować wygenerowanej treści'
                ];
            }
            
            // Zapisz artykuł
            $articleId = $this->saveArticle($content);
            
            return [
                'success' => true,
                'article_id' => $articleId,
                'title' => $content['title'],
                'engine' => $engine
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
} 