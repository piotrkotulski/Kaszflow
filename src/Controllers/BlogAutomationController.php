<?php

namespace Kaszflow\Controllers;

use Kaszflow\Services\BlogAutomationService;
use Kaszflow\Core\Request;
use Kaszflow\Core\Response;

class BlogAutomationController
{
    private $blogAutomationService;
    
    public function __construct()
    {
        $this->blogAutomationService = new BlogAutomationService();
    }
    
    /**
     * Panel administratora automatyzacji bloga
     */
    public function adminPanel(Request $request): Response
    {
        $recentArticles = $this->getRecentArticles();
        $statistics = $this->getStatistics();
        $scheduledJobs = $this->getScheduledJobs();
        
        $content = $this->renderView('blog/automation/admin', [
            'recent_articles' => $recentArticles,
            'statistics' => $statistics,
            'scheduled_jobs' => $scheduledJobs,
            'pageTitle' => 'Panel Automatyzacji Blog - Kaszflow',
            'pageDescription' => 'Zarządzanie automatycznym generowaniem artykułów blogowych'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Generowanie artykułu na żądanie
     */
    public function generateArticle(Request $request): Response
    {
        try {
            $engine = $request->post('engine', 'openai');
            
            // Sprawdź czy nie ma za dużo artykułów (zabezpieczenie przed nadmiarem)
            if (!$this->shouldGenerateArticle()) {
                $response = new Response();
                $response->json([
                    'success' => false,
                    'message' => 'Za dużo artykułów w systemie. Poczekaj przed wygenerowaniem kolejnego.'
                ]);
                $response->setStatusCode(400);
                return $response;
            }
            
            $result = $this->blogAutomationService->generateArticle($engine);
            
            if ($result['success']) {
                // Przesuń następne uruchomienie zaplanowanych zadań
                $this->updateScheduledJobsAfterGeneration();
                
                $response = new Response();
                $response->json([
                    'success' => true,
                    'message' => 'Artykuł został wygenerowany pomyślnie',
                    'article_id' => $result['article_id'],
                    'title' => $result['title'],
                    'engine' => $result['engine']
                ]);
                return $response;
            } else {
                $response = new Response();
                $response->json([
                    'success' => false,
                    'message' => 'Błąd podczas generowania artykułu: ' . $result['error']
                ]);
                $response->setStatusCode(500);
                return $response;
            }
            
        } catch (\Exception $e) {
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Wystąpił błąd: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
    }
    
    /**
     * Planowanie automatycznego generowania
     */
    public function scheduleGeneration(Request $request): Response
    {
        $frequency = $request->post('frequency', 'daily');
        $time = $request->post('time', '09:00');
        
        // Debug
        error_log("Schedule request - frequency: " . $frequency . ", time: " . $time);
        
        try {
            $this->scheduleJob($frequency, $time);
            
            $response = new Response();
            $response->json([
                'success' => true,
                'message' => 'Automatyczne generowanie zostało zaplanowane',
                'debug' => [
                    'frequency' => $frequency,
                    'time' => $time
                ]
            ]);
            return $response;
            
        } catch (\Exception $e) {
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Błąd podczas planowania: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
    }
    
    /**
     * Pobieranie ostatnich artykułów
     */
    private function getRecentArticles()
    {
        // Tu implementacja pobierania z bazy danych
        return [
            [
                'id' => 'article_1',
                'title' => 'Jak wybrać najlepszy kredyt gotówkowy',
                'status' => 'published',
                'created_at' => '2024-01-15 10:30:00',
                'keywords' => 'kredyt gotówkowy, RRSO, porównanie'
            ],
            [
                'id' => 'article_2',
                'title' => 'Najlepsze konta osobiste 2024',
                'status' => 'draft',
                'created_at' => '2024-01-14 14:20:00',
                'keywords' => 'konto osobiste, bankowość, opłaty'
            ],
            [
                'id' => 'article_3',
                'title' => 'Gdzie lokować pieniądze w czasach inflacji',
                'status' => 'published',
                'created_at' => '2024-01-13 09:15:00',
                'keywords' => 'lokata, inflacja, oszczędzanie'
            ]
        ];
    }
    
    /**
     * Pobieranie statystyk
     */
    private function getStatistics()
    {
        $database = new \Kaszflow\Services\Database();
        
        // Pobierz rzeczywiste statystyki z bazy danych
        $totalArticles = $database->select("SELECT COUNT(*) as count FROM articles");
        $publishedThisMonth = $database->select("SELECT COUNT(*) as count FROM articles WHERE status = 'published' AND created_at >= date('now', 'start of month')");
        $draftArticles = $database->select("SELECT COUNT(*) as count FROM articles WHERE status = 'draft'");
        
        // Pobierz statystyki wyświetleń (jeśli tabela istnieje)
        $totalViews = 0;
        $averageViewsPerArticle = 0;
        
        try {
            $viewsResult = $database->select("SELECT COUNT(*) as count FROM page_views");
            if (!empty($viewsResult)) {
                $totalViews = $viewsResult[0]['count'];
            }
            
            if ($totalArticles[0]['count'] > 0) {
                $averageViewsPerArticle = round($totalViews / $totalArticles[0]['count']);
            }
        } catch (\Exception $e) {
            // Tabela page_views może nie istnieć
            $totalViews = 0;
            $averageViewsPerArticle = 0;
        }
        
        // Pobierz następne uruchomienie z zaplanowanych zadań
        $scheduledJobs = $this->getScheduledJobs();
        $nextGeneration = date('Y-m-d H:i:s', strtotime('+1 hour')); // domyślne
        
        if (!empty($scheduledJobs)) {
            // Weź najbliższe uruchomienie
            $nextRun = null;
            foreach ($scheduledJobs as $job) {
                if ($job['next_run'] && (!$nextRun || $job['next_run'] < $nextRun)) {
                    $nextRun = $job['next_run'];
                }
            }
            if ($nextRun) {
                $nextGeneration = $nextRun;
            }
        }
        
        return [
            'total_articles' => $totalArticles[0]['count'] ?? 0,
            'published_this_month' => $publishedThisMonth[0]['count'] ?? 0,
            'draft_articles' => $draftArticles[0]['count'] ?? 0,
            'total_views' => $totalViews,
            'average_views_per_article' => $averageViewsPerArticle,
            'avg_generation_time' => '45 sekund',
            'success_rate' => '92%',
            'next_generation' => $nextGeneration
        ];
    }
    
    /**
     * Pobieranie zaplanowanych zadań
     */
    private function getScheduledJobs()
    {
        $jobsFile = __DIR__ . '/../../data/scheduled_jobs.json';
        
        if (!file_exists($jobsFile)) {
            return [];
        }
        
        $jobs = json_decode(file_get_contents($jobsFile), true) ?? [];
        
        // Filtruj tylko aktywne zadania
        return array_filter($jobs, function($job) {
            return $job['status'] === 'active';
        });
    }
    
    /**
     * Planowanie zadania
     */
    private function scheduleJob($frequency, $time)
    {
        // Oblicz następne uruchomienie na podstawie częstotliwości
        $nextRun = $this->calculateNextRun($frequency, $time);
        
        $job = [
            'id' => 'job_' . time(),
            'name' => $this->getJobName($frequency),
            'frequency' => $frequency,
            'time' => $time,
            'created_at' => date('Y-m-d H:i:s'),
            'last_run' => null,
            'next_run' => $nextRun,
            'status' => 'active'
        ];
        
        // Zapisz do pliku (tymczasowe rozwiązanie)
        $jobsFile = __DIR__ . '/../../data/scheduled_jobs.json';
        $jobs = [];
        
        if (file_exists($jobsFile)) {
            $jobs = json_decode(file_get_contents($jobsFile), true) ?? [];
        }
        
        // Usuń wszystkie stare zadania i dodaj nowe
        $jobs = [];
        
        $jobs[] = $job;
        file_put_contents($jobsFile, json_encode($jobs, JSON_PRETTY_PRINT));
    }
    
    /**
     * Oblicz następne uruchomienie
     */
    private function calculateNextRun($frequency, $time)
    {
        $now = new \DateTime();
        $nextRun = new \DateTime();
        
        // Ustaw czas
        list($hour, $minute) = explode(':', $time);
        $nextRun->setTime($hour, $minute, 0);
        
        // Jeśli czas już minął dzisiaj, przesuń na jutro
        if ($nextRun <= $now) {
            $nextRun->add(new \DateInterval('P1D'));
        }
        
        // Dostosuj do częstotliwości
        switch ($frequency) {
            case 'daily':
                // Codziennie - już ustawione
                break;
            case 'weekly':
                // Raz w tygodniu - następny poniedziałek
                $daysUntilMonday = (8 - $nextRun->format('N')) % 7;
                if ($daysUntilMonday > 0) {
                    $nextRun->add(new \DateInterval('P' . $daysUntilMonday . 'D'));
                }
                break;
            case 'twice_weekly':
                // 2 razy w tygodniu - poniedziałek i czwartek
                $currentDay = $nextRun->format('N'); // 1=poniedziałek, 7=niedziela
                if ($currentDay < 1) { // Przed poniedziałkiem
                    $nextRun->add(new \DateInterval('P' . (1 - $currentDay) . 'D'));
                } elseif ($currentDay > 1 && $currentDay < 4) { // Między poniedziałkiem a czwartkiem
                    $nextRun->add(new \DateInterval('P' . (4 - $currentDay) . 'D'));
                } elseif ($currentDay > 4) { // Po czwartku
                    $nextRun->add(new \DateInterval('P' . (8 - $currentDay) . 'D'));
                }
                break;
        }
        
        return $nextRun->format('Y-m-d H:i:s');
    }
    
    /**
     * Pobierz nazwę zadania
     */
    private function getJobName($frequency)
    {
        switch ($frequency) {
            case 'daily':
                return 'Codzienne generowanie artykułów';
            case 'weekly':
                return 'Cotygodniowe generowanie artykułów';
            case 'twice_weekly':
                return 'Dwa razy w tygodniu - generowanie artykułów';
            default:
                return 'Automatyczne generowanie artykułów';
        }
    }
    
    /**
     * Sprawdź czy powinien wygenerować artykuł (zabezpieczenie przed nadmiarem)
     */
    private function shouldGenerateArticle()
    {
        $database = new \Kaszflow\Services\Database();
        
        // Sprawdź ile artykułów zostało wygenerowanych w ostatnich 24 godzinach
        $recentArticles = $database->select(
            "SELECT COUNT(*) as count FROM articles WHERE created_at >= datetime('now', '-1 day')"
        );
        
        $count = $recentArticles[0]['count'] ?? 0;
        
        // Maksymalnie 10 artykułów dziennie (zwiększone z 3)
        return $count < 10;
    }
    
    /**
     * Przesuń następne uruchomienie zaplanowanych zadań po wygenerowaniu artykułu
     */
    private function updateScheduledJobsAfterGeneration()
    {
        $jobsFile = __DIR__ . '/../../data/scheduled_jobs.json';
        
        if (!file_exists($jobsFile)) {
            return;
        }
        
        $jobs = json_decode(file_get_contents($jobsFile), true) ?? [];
        $now = date('Y-m-d H:i:s');
        
        foreach ($jobs as &$job) {
            if ($job['status'] === 'active' && $job['next_run'] <= $now) {
                // Przesuń następne uruchomienie
                $job['last_run'] = $now;
                $job['next_run'] = $this->calculateNextRun($job['frequency'], $job['time']);
            }
        }
        
        file_put_contents($jobsFile, json_encode($jobs, JSON_PRETTY_PRINT));
    }
    
    /**
     * Publikowanie artykułu
     */
    public function publishDraft(Request $request): Response
    {
        $articleId = $request->post('article_id');
        
        try {
            $this->publishArticle($articleId);
            
            $response = new Response();
            $response->json([
                'success' => true,
                'message' => 'Artykuł został opublikowany'
            ]);
            return $response;
            
        } catch (\Exception $e) {
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Błąd podczas publikowania: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
    }
    
    /**
     * Publikowanie artykułu w bazie danych
     */
    private function publishArticle($articleId)
    {
        $database = new \Kaszflow\Services\Database();
        
        $result = $database->update(
            'articles',
            [
                'status' => 'published',
                'published_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            'id = :id',
            ['id' => $articleId]
        );
        
        if (!$result) {
            throw new \Exception('Nie udało się opublikować artykułu');
        }
    }
    
    /**
     * Edycja artykułu
     */
    public function editArticle(Request $request): Response
    {
        $articleId = $request->post('article_id');
        $title = $request->post('title');
        $content = $request->post('content');
        $metaDescription = $request->post('meta_description');
        $category = $request->post('category');
        
        try {
            $this->updateArticle($articleId, $title, $content, $metaDescription, $category);
            
            $response = new Response();
            $response->json([
                'success' => true,
                'message' => 'Artykuł został zaktualizowany'
            ]);
            return $response;
            
        } catch (\Exception $e) {
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Błąd podczas edycji: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
    }
    
    /**
     * Aktualizacja artykułu w bazie danych
     */
    private function updateArticle($articleId, $title, $content, $metaDescription, $category)
    {
        $database = new \Kaszflow\Services\Database();
        
        // Generuj nowy slug na podstawie tytułu
        $slug = $this->generateSlug($title);
        
        $result = $database->update(
            'articles',
            [
                'title' => $title,
                'content' => $content,
                'meta_description' => $metaDescription,
                'category' => $category,
                'slug' => $slug,
                'updated_at' => date('Y-m-d H:i:s')
            ],
            'id = :id',
            ['id' => $articleId]
        );
        
        if (!$result) {
            throw new \Exception('Nie udało się zaktualizować artykułu');
        }
    }
    
    /**
     * Generowanie slug z tytułu
     */
    private function generateSlug($title): string
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
    
    /**
     * Usuwanie artykułu
     */
    public function deleteArticle(Request $request): Response
    {
        $articleId = $request->post('article_id');
        
        try {
            $this->removeArticle($articleId);
            
            $response = new Response();
            $response->json([
                'success' => true,
                'message' => 'Artykuł został usunięty'
            ]);
            return $response;
            
        } catch (\Exception $e) {
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Błąd podczas usuwania: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
    }
    
    /**
     * Usuwanie artykułu z bazy danych
     */
    private function removeArticle($articleId)
    {
        $database = new \Kaszflow\Services\Database();
        
        $result = $database->delete('articles', 'id = :id', ['id' => $articleId]);
        
        if (!$result) {
            throw new \Exception('Nie udało się usunąć artykułu');
        }
    }
    
    /**
     * Strona ustawień automatyzacji
     */
    public function settingsPage(Request $request): Response
    {
        $database = new \Kaszflow\Services\Database();
        
        // Pobierz aktualne ustawienia
        $settings = $database->select("SELECT setting_key, setting_value FROM automation_settings");
        $config = [];
        foreach ($settings as $setting) {
            $value = $setting['setting_value'];
            
            // Konwertuj wartości boolean
            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            }
            
            // Konwertuj JSON arrays
            if (in_array($setting['setting_key'], ['target_categories', 'tags', 'keywords'])) {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    $value = $decoded;
                }
            }
            
            $config[$setting['setting_key']] = $value;
        }
        
        $content = $this->renderView('blog/automation/settings', [
            'settings' => $config,
            'pageTitle' => 'Ustawienia Automatyzacji Blog - Kaszflow',
            'pageDescription' => 'Konfiguracja systemu automatycznego generowania artykułów'
        ]);
        
        return new Response($content);
    }

    /**
     * Ustawienia automatyzacji (API)
     */
    public function settings(Request $request): Response
    {
        $database = new \Kaszflow\Services\Database();
        
        // Pobierz aktualne ustawienia
        $settings = $database->select("SELECT setting_key, setting_value FROM automation_settings");
        $config = [];
        foreach ($settings as $setting) {
            $config[$setting['setting_key']] = $setting['setting_value'];
        }
        
        $response = new Response();
        $response->json([
            'success' => true,
            'settings' => $config
        ]);
        return $response;
    }
    
    /**
     * Zapisz ustawienia automatyzacji
     */
    public function saveSettings(Request $request): Response
    {
        try {
            $database = new \Kaszflow\Services\Database();
            
            $settings = [
                'openai_api_key' => $request->post('openai_api_key'),
                'claude_api_key' => $request->post('claude_api_key'),
                'auto_generation_enabled' => $request->post('auto_generation_enabled'),
                'generation_frequency' => $request->post('generation_frequency'),
                'generation_time' => $request->post('generation_time'),
                'default_engine' => $request->post('default_engine'),
                'max_articles_per_day' => $request->post('max_articles_per_day'),
                'content_length' => $request->post('content_length'),
                'seo_optimization' => $request->post('seo_optimization'),
                'auto_publish' => $request->post('auto_publish'),
                'custom_topics' => $request->post('custom_topics'),
                'custom_keywords' => $request->post('custom_keywords'),
                'affiliate_links' => $request->post('affiliate_links'),
                'topic_blacklist' => $request->post('topic_blacklist'),
                'min_article_length' => $request->post('min_article_length'),
                'max_articles_per_topic' => $request->post('max_articles_per_topic'),
                'openai_model' => $request->post('openai_model'),
                'temperature' => $request->post('temperature'),
                'max_tokens' => $request->post('max_tokens'),
                'default_meta_title' => $request->post('default_meta_title'),
                'default_meta_description' => $request->post('default_meta_description'),
                'target_categories' => $request->post('target_categories')
            ];
            
            foreach ($settings as $key => $value) {
                if ($value !== null) {
                    // Dla checkboxów i array, konwertuj na odpowiedni format
                    if (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($key === 'auto_publish' || $key === 'auto_generation_enabled' || $key === 'seo_optimization') {
                        $value = $value ? 'true' : 'false';
                    }
                    
                    $database->execute(
                        "INSERT OR REPLACE INTO automation_settings (setting_key, setting_value, updated_at) VALUES (?, ?, ?)",
                        [$key, $value, date('Y-m-d H:i:s')]
                    );
                }
            }
            
            $response = new Response();
            $response->json([
                'success' => true,
                'message' => 'Ustawienia zostały zapisane'
            ]);
            return $response;
            
        } catch (\Exception $e) {
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Błąd podczas zapisywania ustawień: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
    }
    
    /**
     * Ładowanie ustawień
     */
    private function loadSettings()
    {
        $settingsFile = __DIR__ . '/../../data/blog_automation_settings.json';
        
        if (file_exists($settingsFile)) {
            return json_decode(file_get_contents($settingsFile), true) ?? [];
        }
        
        return [
            'openai_api_key' => '',
            'claude_api_key' => '',
            'ai_engine' => 'openai',
            'generation_frequency' => 'daily',
            'auto_publish' => false,
            'article_length' => 'medium',
            'target_categories' => ['kredyty', 'bankowość', 'oszczędzanie'],
            'seo_settings' => [
                'meta_title_template' => '{title} - Kaszflow',
                'meta_description_length' => 160
            ],
            'ai_settings' => [
                'model' => 'gpt-4',
                'temperature' => 0.7,
                'max_tokens' => 2000
            ]
        ];
    }

    /**
     * Test połączenia z Claude AI API
     */
    public function testClaudeAPI(Request $request): Response
    {
        try {
            $apiKey = $request->post('api_key');
            
            if (empty($apiKey)) {
                $response = new Response();
                $response->json([
                    'success' => false,
                    'message' => 'Brak klucza API'
                ]);
                $response->setStatusCode(400);
                return $response;
            }
            
            // Test połączenia z Claude AI
            $testPrompt = "Napisz krótkie zdanie testowe.";
            $data = [
                'model' => 'claude-3-sonnet-20240229',
                'max_tokens' => 50,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $testPrompt
                    ]
                ]
            ];
            
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => [
                        'Content-Type: application/json',
                        'x-api-key: ' . $apiKey,
                        'anthropic-version: 2023-06-01'
                    ],
                    'content' => json_encode($data),
                    'timeout' => 30
                ]
            ]);
            
            $response = file_get_contents('https://api.anthropic.com/v1/messages', false, $context);
            
            if ($response === false) {
                $responseObj = new Response();
                $responseObj->json([
                    'success' => false,
                    'message' => 'Błąd połączenia z API Claude AI'
                ]);
                $responseObj->setStatusCode(500);
                return $responseObj;
            }
            
            $result = json_decode($response, true);
            
            if (isset($result['error'])) {
                $responseObj = new Response();
                $responseObj->json([
                    'success' => false,
                    'message' => 'Błąd API Claude AI: ' . $result['error']['message']
                ]);
                $responseObj->setStatusCode(500);
                return $responseObj;
            }
            
            $responseObj = new Response();
            $responseObj->json([
                'success' => true,
                'message' => 'Połączenie z Claude AI udane'
            ]);
            return $responseObj;
            
        } catch (\Exception $e) {
            $responseObj = new Response();
            $responseObj->json([
                'success' => false,
                'message' => 'Wystąpił błąd: ' . $e->getMessage()
            ]);
            $responseObj->setStatusCode(500);
            return $responseObj;
        }
    }

    /**
     * Test połączenia z OpenAI API
     */
    public function testApiConnection(Request $request): Response
    {
        try {
            $apiKey = $request->post('api_key');
            
            if (empty($apiKey)) {
                $response = new Response();
                $response->json([
                    'success' => false,
                    'message' => 'Brak klucza API'
                ]);
                $response->setStatusCode(400);
                return $response;
            }
            
            // Test połączenia z OpenAI
            $testPrompt = "Napisz krótkie zdanie testowe.";
            $data = [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Jesteś ekspertem od finansów osobistych w Polsce.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $testPrompt
                    ]
                ],
                'max_tokens' => 50,
                'temperature' => 0.7
            ];
            
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $apiKey
                    ],
                    'content' => json_encode($data),
                    'timeout' => 30
                ]
            ]);
            
            $response = file_get_contents('https://api.openai.com/v1/chat/completions', false, $context);
            
            if ($response === false) {
                $responseObj = new Response();
                $responseObj->json([
                    'success' => false,
                    'message' => 'Błąd połączenia z API OpenAI'
                ]);
                $responseObj->setStatusCode(500);
                return $responseObj;
            }
            
            $result = json_decode($response, true);
            
            if (isset($result['error'])) {
                $responseObj = new Response();
                $responseObj->json([
                    'success' => false,
                    'message' => 'Błąd API OpenAI: ' . $result['error']['message']
                ]);
                $responseObj->setStatusCode(500);
                return $responseObj;
            }
            
            $responseObj = new Response();
            $responseObj->json([
                'success' => true,
                'message' => 'Połączenie z OpenAI udane'
            ]);
            return $responseObj;
            
        } catch (\Exception $e) {
            $responseObj = new Response();
            $responseObj->json([
                'success' => false,
                'message' => 'Wystąpił błąd: ' . $e->getMessage()
            ]);
            $responseObj->setStatusCode(500);
            return $responseObj;
        }
    }

    /**
     * Ustawianie klucza API
     */
    public function setApiKey(Request $request): Response
    {
        try {
            $keyType = $request->post('key_type');
            $apiKey = $request->post('api_key');
            
            if (!in_array($keyType, ['openai', 'claude'])) {
                throw new \Exception('Nieprawidłowy typ klucza API');
            }
            
            $database = new \Kaszflow\Services\Database();
            
            // Zapisz klucz w bazie danych
            $settingKey = $keyType . '_api_key';
            $database->update(
                'automation_settings',
                ['setting_value' => $apiKey],
                'setting_key = :key',
                ['key' => $settingKey]
            );
            
            // Aktualizuj zmienną środowiskową
            $_ENV[strtoupper($keyType) . '_API_KEY'] = $apiKey;
            
            $response = new Response();
            $response->json([
                'success' => true,
                'message' => 'Klucz API został ustawiony pomyślnie'
            ]);
            return $response;
            
        } catch (\Exception $e) {
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Błąd podczas ustawiania klucza API: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
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
     * Listowanie artykułów
     */
    public function listArticles(Request $request): Response
    {
        try {
            $articles = $this->loadArticles();
            
            $response = new Response();
            $response->json([
                'success' => true,
                'articles' => $articles
            ]);
            return $response;
            
        } catch (\Exception $e) {
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Błąd podczas ładowania artykułów: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
    }
    
    /**
     * Ładowanie artykułów z bazy danych
     */
    private function loadArticles(): array
    {
        $database = new \Kaszflow\Services\Database();
        
        $query = "SELECT * FROM articles ORDER BY created_at DESC";
        $articles = $database->select($query);
        
        // Konwersja danych z bazy na format JSON
        $formattedArticles = [];
        foreach ($articles as $article) {
            $formattedArticles[$article['id']] = [
                'id' => $article['id'],
                'title' => $article['title'],
                'slug' => $article['slug'],
                'content' => $article['content'],
                'meta_description' => $article['meta_description'],
                'meta_title' => $article['meta_title'],
                'tags' => json_decode($article['tags'], true) ?: [],
                'keywords' => json_decode($article['keywords'], true) ?: [],
                'schema_markup' => $article['schema_markup'],
                'status' => $article['status'],
                'engine' => $article['engine'],
                'created_at' => $article['created_at'],
                'updated_at' => $article['updated_at'],
                'published_at' => $article['published_at']
            ];
        }
        
        return $formattedArticles;
    }

    /**
     * Pobierz sugestie tematów na dany tydzień
     */
    public function getSuggestions(Request $request): Response
    {
        try {
            $suggestions = $this->blogAutomationService->getWeeklySuggestions();
            
            $response = new Response();
            $response->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
            return $response;
            
        } catch (\Exception $e) {
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Błąd podczas generowania sugestii: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
    }

    /**
     * Generuj artykuł z sugestii
     */
    public function generateFromSuggestion(Request $request): Response
    {
        $topic = $request->post('topic');
        $category = $request->post('category');
        $keywords = $request->post('keywords');
        
        // Debug
        error_log("Generate from suggestion - topic: " . $topic . ", category: " . $category . ", keywords: " . $keywords);
        
        try {
            $topicArray = [
                'topic' => $topic,
                'category' => $category,
                'keywords' => explode(',', $keywords)
            ];
            
            error_log("Calling generateArticleFromTopic with: " . json_encode($topicArray));
            $result = $this->blogAutomationService->generateArticleFromTopic($topicArray);
            error_log("generateArticleFromTopic result: " . json_encode($result));
            
            // Jeśli artykuł został wygenerowany pomyślnie, usuń sugestię z listy
            if ($result['success']) {
                $this->removeSuggestionFromList($topic);
            }
            
            $response = new Response();
            $response->json($result);
            return $response;
            
        } catch (\Exception $e) {
            error_log("Exception in generateFromSuggestion: " . $e->getMessage());
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Błąd podczas generowania artykułu: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
    }
    
    /**
     * Usuń sugestię z listy po wygenerowaniu artykułu
     */
    private function removeSuggestionFromList(string $topic)
    {
        // Zapisz wygenerowane sugestie do pliku, aby nie pojawiały się ponownie
        $suggestionsFile = __DIR__ . '/../../data/generated_suggestions.json';
        $generatedSuggestions = [];
        
        if (file_exists($suggestionsFile)) {
            $generatedSuggestions = json_decode(file_get_contents($suggestionsFile), true) ?? [];
        }
        
        $generatedSuggestions[] = [
            'topic' => $topic,
            'generated_at' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($suggestionsFile, json_encode($generatedSuggestions, JSON_PRETTY_PRINT));
    }
    
    /**
     * Zregeneruj obrazki dla istniejących artykułów
     */
    public function regenerateImages(Request $request): Response
    {
        try {
            // Debug: sprawdź ile artykułów jest w bazie
            $database = new \Kaszflow\Services\Database();
            $totalArticles = $database->select("SELECT COUNT(*) as count FROM articles");
            error_log("Total articles in database: " . $totalArticles[0]['count']);
            
            error_log("Controller: About to call regenerateImagesForExistingArticles");
            $results = $this->blogAutomationService->regenerateImagesForExistingArticles();
            error_log("Controller: regenerateImagesForExistingArticles returned " . count($results) . " results");
            
            $response = new Response();
            $response->json([
                'success' => true,
                'message' => 'Zregenerowano obrazki dla ' . count($results) . ' artykułów',
                'results' => $results,
                'debug' => [
                    'total_articles' => $totalArticles[0]['count'],
                    'processed_articles' => count($results),
                    'test_message' => 'Function was called successfully',
                    'test_info' => $this->blogAutomationService->testInfo ?? 'No test info available'
                ]
            ]);
            return $response;
            
        } catch (\Exception $e) {
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => 'Błąd podczas regenerowania obrazków: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500);
            return $response;
        }
    }
} 