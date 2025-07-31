<?php

namespace Kaszflow\Controllers;

use Kaszflow\Core\Request;
use Kaszflow\Core\Response;
use Kaszflow\Services\Cache;

class BlogController
{
    private $cache;
    
    public function __construct()
    {
        $this->cache = new Cache();
    }
    
    /**
     * Lista wszystkich postów bloga
     */
    public function index(Request $request): Response
    {
        // Pobieranie postów z cache
        $posts = $this->cache->remember('blog_posts', function() {
            return [
                [
                    'id' => 1,
                    'title' => 'Jak wybrać najlepszy kredyt gotówkowy?',
                    'slug' => 'jak-wybrac-najlepszy-kredyt-gotowkowy',
                    'excerpt' => 'Poznaj kluczowe czynniki, które warto wziąć pod uwagę przy wyborze kredytu gotówkowego.',
                    'content' => 'Treść artykułu o wyborze kredytu gotówkowego...',
                    'published_at' => '2024-01-15',
                    'author' => 'Ekspert Kaszflow',
                    'image' => '/assets/images/blog/kredyt-gotowkowy.jpg'
                ],
                [
                    'id' => 2,
                    'title' => 'Konta osobiste 2024 - przegląd najlepszych ofert',
                    'slug' => 'konta-osobiste-2024-przeglad-najlepszych-ofert',
                    'excerpt' => 'Sprawdź, które banki oferują najlepsze konta osobiste w 2024 roku.',
                    'content' => 'Treść artykułu o kontach osobistych...',
                    'published_at' => '2024-01-10',
                    'author' => 'Ekspert Kaszflow',
                    'image' => '/assets/images/blog/konta-osobiste.jpg'
                ],
                [
                    'id' => 3,
                    'title' => 'Lokaty terminowe - czy warto inwestować?',
                    'slug' => 'lokaty-terminowe-czy-warto-inwestowac',
                    'excerpt' => 'Analiza opłacalności lokat terminowych w obecnych warunkach rynkowych.',
                    'content' => 'Treść artykułu o lokatach terminowych...',
                    'published_at' => '2024-01-05',
                    'author' => 'Ekspert Kaszflow',
                    'image' => '/assets/images/blog/lokaty-terminowe.jpg'
                ]
            ];
        }, 3600); // Cache na 1 godzinę
        
        $content = $this->renderView('blog/index', [
            'posts' => $posts,
            'pageTitle' => 'Blog - Kaszflow',
            'pageDescription' => 'Artykuły o finansach osobistych, kredytach, kontach i lokatach.'
        ]);
        
        return new Response($content);
    }
    
    /**
     * Pojedynczy post bloga
     */
    public function show(Request $request): Response
    {
        $slug = $request->getPathParam('slug', '');
        
        // Pobieranie konkretnego posta z cache
        $post = $this->cache->remember("blog_post_{$slug}", function() use ($slug) {
            // Tutaj w przyszłości będzie pobieranie z bazy danych
            $posts = [
                'jak-wybrac-najlepszy-kredyt-gotowkowy' => [
                    'id' => 1,
                    'title' => 'Jak wybrać najlepszy kredyt gotówkowy?',
                    'slug' => 'jak-wybrac-najlepszy-kredyt-gotowkowy',
                    'content' => '<h2>Wprowadzenie</h2><p>Wybór odpowiedniego kredytu gotówkowego to jedna z najważniejszych decyzji finansowych...</p><h2>Kluczowe czynniki</h2><p>Przy wyborze kredytu gotówkowego warto zwrócić uwagę na:</p><ul><li>RRSO (Rzeczywista Roczna Stopa Oprocentowania)</li><li>Wysokość raty</li><li>Okres kredytowania</li><li>Dodatkowe opłaty</li></ul><h2>Podsumowanie</h2><p>Dokładna analiza wszystkich kosztów to klucz do podjęcia właściwej decyzji...</p>',
                    'published_at' => '2024-01-15',
                    'author' => 'Ekspert Kaszflow',
                    'image' => '/assets/images/blog/kredyt-gotowkowy.jpg'
                ],
                'konta-osobiste-2024-przeglad-najlepszych-ofert' => [
                    'id' => 2,
                    'title' => 'Konta osobiste 2024 - przegląd najlepszych ofert',
                    'slug' => 'konta-osobiste-2024-przeglad-najlepszych-ofert',
                    'content' => '<h2>Najlepsze konta osobiste 2024</h2><p>Przegląd najkorzystniejszych ofert kont osobistych...</p>',
                    'published_at' => '2024-01-10',
                    'author' => 'Ekspert Kaszflow',
                    'image' => '/assets/images/blog/konta-osobiste.jpg'
                ],
                'lokaty-terminowe-czy-warto-inwestowac' => [
                    'id' => 3,
                    'title' => 'Lokaty terminowe - czy warto inwestować?',
                    'slug' => 'lokaty-terminowe-czy-warto-inwestowac',
                    'content' => '<h2>Analiza lokat terminowych</h2><p>Sprawdź, czy lokaty terminowe są opłacalne...</p>',
                    'published_at' => '2024-01-05',
                    'author' => 'Ekspert Kaszflow',
                    'image' => '/assets/images/blog/lokaty-terminowe.jpg'
                ]
            ];
            
            return $posts[$slug] ?? null;
        }, 3600);
        
        if (!$post) {
            return new Response('Post nie został znaleziony', 404);
        }
        
        $content = $this->renderView('blog/show', [
            'post' => $post,
            'pageTitle' => $post['title'] . ' - Blog Kaszflow',
            'pageDescription' => substr(strip_tags($post['content']), 0, 160) . '...'
        ]);
        
        return new Response($content);
    }
    
    /**
     * API - pobieranie postów
     */
    public function getPosts(Request $request): Response
    {
        $posts = $this->cache->remember('blog_posts', function() {
            return [
                [
                    'id' => 1,
                    'title' => 'Jak wybrać najlepszy kredyt gotówkowy?',
                    'slug' => 'jak-wybrac-najlepszy-kredyt-gotowkowy',
                    'excerpt' => 'Poznaj kluczowe czynniki, które warto wziąć pod uwagę przy wyborze kredytu gotówkowego.',
                    'published_at' => '2024-01-15'
                ],
                [
                    'id' => 2,
                    'title' => 'Konta osobiste 2024 - przegląd najlepszych ofert',
                    'slug' => 'konta-osobiste-2024-przeglad-najlepszych-ofert',
                    'excerpt' => 'Sprawdź, które banki oferują najlepsze konta osobiste w 2024 roku.',
                    'published_at' => '2024-01-10'
                ],
                [
                    'id' => 3,
                    'title' => 'Lokaty terminowe - czy warto inwestować?',
                    'slug' => 'lokaty-terminowe-czy-warto-inwestowac',
                    'excerpt' => 'Analiza opłacalności lokat terminowych w obecnych warunkach rynkowych.',
                    'published_at' => '2024-01-05'
                ]
            ];
        }, 3600);
        
        return new Response(json_encode($posts), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * API - generowanie posta
     */
    public function generatePost(Request $request): Response
    {
        $data = json_decode($request->getBody(), true);
        $topic = $data['topic'] ?? '';
        
        if (empty($topic)) {
            return new Response(json_encode(['error' => 'Topic is required']), 400, ['Content-Type' => 'application/json']);
        }
        
        // Tutaj w przyszłości będzie integracja z OpenAI
        $generatedPost = [
            'title' => "Artykuł o: {$topic}",
            'content' => "Treść wygenerowanego artykułu o {$topic}...",
            'excerpt' => "Krótkie podsumowanie artykułu o {$topic}",
            'published_at' => date('Y-m-d')
        ];
        
        return new Response(json_encode($generatedPost), 200, ['Content-Type' => 'application/json']);
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
} 