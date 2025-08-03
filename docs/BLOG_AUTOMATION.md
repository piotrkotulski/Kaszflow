# System Automatyzacji Blog Kaszflow

## Przegląd

System automatyzacji blog Kaszflow automatycznie generuje wysokiej jakości artykuły finansowe na podstawie aktualnych trendów rynkowych, zmian stóp procentowych NBP i analizy RSS feedów z wiodących portali finansowych.

## Funkcje

### 🤖 Automatyczne generowanie treści
- **Źródła danych**: NBP API, RSS feeds (Bankier.pl, Money.pl), ogłoszenia rządowe
- **Tematy**: Trendujące tematy finansowe, zmiany stóp procentowych, sezonowe tematy
- **Jakość**: Optymalizacja SEO, schema markup, meta tagi
- **Częstotliwość**: Konfigurowalna (codziennie, co tydzień, miesięcznie)

### 📊 Panel administracyjny
- **Dashboard**: Statystyki, ostatnie artykuły, zaplanowane zadania
- **Generowanie**: Ręczne generowanie artykułów
- **Planowanie**: Automatyczne generowanie o określonych porach
- **Zarządzanie**: Edycja, publikacja, usuwanie artykułów

### 🎯 Optymalizacja SEO
- **Meta tagi**: Automatyczne generowanie title i description
- **Schema markup**: Strukturalne dane dla wyszukiwarek
- **Słowa kluczowe**: Optymalizacja pod popularne frazy finansowe
- **Linkowanie**: Automatyczne linki wewnętrzne

## Architektura

### Kontrolery
```
src/Controllers/
├── BlogAutomationController.php    # Panel admin, zarządzanie
└── HomeController.php              # Wyświetlanie bloga
```

### Serwisy
```
src/Services/
└── BlogAutomationService.php      # Logika automatyzacji
```

### Routing
```
/blog/automation/admin              # Panel administratora
/blog/automation/settings           # Ustawienia
/blog/automation/generate           # Generowanie artykułu
/blog/automation/schedule           # Planowanie
/blog/automation/publish            # Publikacja
/blog/automation/edit               # Edycja
/blog/automation/delete             # Usuwanie
/blog/automation/test-api           # Test API
```

## 🔧 Konfiguracja

### Wymagane API Keys

#### OpenAI API
```bash
OPENAI_API_KEY=sk-your-openai-api-key-here
```

#### Claude AI API (alternatywny silnik)
```bash
CLAUDE_API_KEY=sk-ant-your-claude-api-key-here
```

**Uwaga**: Możesz używać jednego lub obu silników AI. System automatycznie wybierze dostępny silnik lub pozwoli na ręczny wybór w panelu administracyjnym.

### 🚀 Silniki AI

System obsługuje dwa silniki AI do generowania treści:

#### OpenAI GPT-4
- **Model**: `gpt-4`
- **Zalety**: Doskonała znajomość kontekstu finansowego, kreatywność
- **Ograniczenia**: Koszty API, limit rate

#### Claude AI (Anthropic)
- **Model**: `claude-3-sonnet-20240229`
- **Zalety**: Bardzo dobra analiza, bezpieczeństwo, niższe koszty
- **Ograniczenia**: Mniej kreatywności niż GPT-4

#### Wybór silnika
- **Automatyczny**: System wybiera dostępny silnik
- **Ręczny**: Wybór w panelu administracyjnym
- **Fallback**: Jeśli jeden silnik nie działa, automatycznie przełącza na drugi

### 📊 Ustawienia w panelu administracyjnym

1. **Konfiguracja API Keys**
   - OpenAI API Key
   - Claude AI API Key
   - Testowanie połączeń

2. **Wybór silnika AI**
   - OpenAI GPT-4 (zalecane)
   - Claude AI (Anthropic)
   - Automatyczny wybór

3. **Parametry generowania**
   - Częstotliwość generowania
   - Długość artykułów
   - Kategorie tematyczne
   - Ustawienia SEO

### 2. Źródła danych
```php
// BlogAutomationService.php
private $trendingSources = [
    'nbp_rates' => 'https://api.nbp.pl/api/exchangerates/tables/A/?format=json',
    'bankier_rss' => 'https://www.bankier.pl/rss.xml',
    'money_rss' => 'https://www.money.pl/rss.xml',
    'government_announcements' => 'https://www.gov.pl/web/finanse'
];
```

### 3. Słowa kluczowe finansowe
```php
private $financialKeywords = [
    'kredyt gotówkowy', 'kredyt hipoteczny', 'konto osobiste', 'lokata bankowa',
    'oprocentowanie', 'RRSO', 'prowizja bankowa', 'rata kredytu',
    'wakacje kredytowe', 'kredyt frankowy', 'WIBOR', 'NBP stopy procentowe',
    // ... więcej słów kluczowych
];
```

## Proces generowania artykułu

### 1. Analiza trendów
```php
private function analyzeTrendingTopics(): array
{
    // Sprawdzenie stóp NBP
    $nbpData = $this->fetchNbpRates();
    
    // Analiza RSS feeds
    $rssTopics = $this->analyzeRssFeeds();
    
    // Tematy sezonowe
    $seasonalTopics = $this->getSeasonalTopics();
    
    return array_merge($nbpData, $rssTopics, $seasonalTopics);
}
```

### 2. Wybór tematu
```php
private function selectTopic(array $topics): array
{
    // Sortowanie po priorytecie
    usort($topics, function($a, $b) {
        return $b['priority'] <=> $a['priority'];
    });
    
    return $topics[0];
}
```

### 3. Generowanie treści
```php
private function generateContent(array $topic): string
{
    $prompt = $this->buildContentPrompt($topic);
    return $this->callOpenAI($prompt);
}
```

### 4. Optymalizacja SEO
```php
private function optimizeSEO(array $content): array
{
    return [
        'title' => $this->generateMetaTitle($content['title']),
        'meta_description' => $this->optimizeMetaDescription($content['description']),
        'schema_markup' => $this->generateSchemaMarkup($content),
        'content' => $this->optimizeContent($content['content'])
    ];
}
```

## API Endpoints

#### Panel administracyjny
- `GET /blog/automation/admin` - Panel główny automatyzacji bloga
- `GET /blog/automation/settings` - Ustawienia systemu

#### Generowanie treści
- `POST /blog/automation/generate` - Generowanie artykułu (parametry: `engine`)
- `POST /blog/automation/schedule` - Planowanie automatycznego generowania

#### Testowanie API
- `POST /blog/automation/test-api` - Test połączenia OpenAI API
- `POST /blog/automation/test-claude` - Test połączenia Claude AI API

#### Zarządzanie artykułami
- `POST /blog/automation/publish` - Publikacja szkicu
- `POST /blog/automation/edit` - Edycja artykułu
- `POST /blog/automation/delete` - Usuwanie artykułu

## Monitoring i logi

### Statystyki
- Liczba wygenerowanych artykułów
- Średni czas generowania
- Wykorzystanie API (OpenAI, NBP)
- Popularność artykułów

### Logi
- Błędy generowania
- Sukcesy publikacji
- Wykorzystanie zasobów
- Alerty o problemach

## Optymalizacja wydajności

### Cache
- Cache dla danych NBP (1 godzina)
- Cache dla RSS feeds (30 minut)
- Cache dla wygenerowanych artykułów (24 godziny)

### Rate Limiting
- OpenAI API: 3 requests/minute
- NBP API: 10 requests/minute
- RSS feeds: 5 requests/minute

### Queue System
- Asynchroniczne generowanie artykułów
- Queue dla długotrwałych operacji
- Retry mechanism dla błędów

## Bezpieczeństwo

### API Keys
- OpenAI API key w .env
- Walidacja kluczy przed użyciem
- Rotacja kluczy co 30 dni

### Walidacja danych
- Sanityzacja treści RSS
- Walidacja odpowiedzi API
- Escape HTML w treści

### Monitoring
- Alerty o nieprawidłowym użyciu API
- Logi błędów bezpieczeństwa
- Monitoring wydatków OpenAI

## Troubleshooting

### Problem: Błąd OpenAI API
```bash
# Sprawdź klucz API
echo $OPENAI_API_KEY

# Test połączenia
curl -X POST /blog/automation/test-api
```

### Problem: Brak danych NBP
```bash
# Sprawdź API NBP
curl https://api.nbp.pl/api/exchangerates/tables/A/

# Sprawdź cache
ls -la data/cache/nbp_*
```

### Problem: Wolne generowanie
```bash
# Sprawdź logi
tail -f logs/blog_automation.log

# Sprawdź wykorzystanie API
curl -X GET /blog/automation/admin
```

## Rozszerzenia

### Dodanie nowego źródła danych
1. Dodaj URL w `$trendingSources`
2. Zaimplementuj metodę fetch w `BlogAutomationService`
3. Dodaj walidację danych
4. Zaktualizuj cache

### Dodanie nowego typu artykułu
1. Dodaj template w `buildContentPrompt()`
2. Zaimplementuj logikę w `generateContent()`
3. Dodaj optymalizację SEO
4. Zaktualizuj routing

### Integracja z bazą danych
1. Stwórz modele Article, Category, Tag
2. Zaimplementuj metody CRUD
3. Dodaj relacje między modelami
4. Zaktualizuj `saveArticle()`

## Wsparcie

W przypadku problemów z systemem automatyzacji bloga:
1. Sprawdź logi: `logs/blog_automation.log`
2. Sprawdź API keys w `.env`
3. Przetestuj połączenia: `/blog/automation/test-api`
4. Sprawdź panel admin: `/blog/automation/admin`
5. Skontaktuj się z administratorem 