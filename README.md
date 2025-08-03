# 🏦 Kaszflow - Porównywarka Produktów Finansowych

## 📋 Przegląd projektu

Kaszflow to zaawansowana porównywarka produktów finansowych, która łączy w sobie:
- **Porównywarki produktów** (kredyty, konta, lokaty, hipoteki)
- **Automatyczny system blogów** z AI
- **SEO-optimized** treści
- **Responsive design** z Tailwind CSS

## 🚀 Funkcjonalności

### 📊 Porównywarki produktów
- **Kredyty gotówkowe** - porównanie RRSO, rat, prowizji
- **Kredyty hipoteczne** - analiza całkowitych kosztów, WIBOR
- **Konta osobiste** - opłaty, premie, dodatkowe usługi
- **Lokaty bankowe** - oprocentowanie, okresy, kwoty odsetek

### 🤖 Automatyzacja blogów
- **AI-powered** generowanie treści (OpenAI GPT-4)
- **Trend analysis** - monitoring stóp NBP, RSS feeds
- **SEO optimization** - automatyczne meta tagi, schema markup
- **Scheduling** - automatyczne generowanie według harmonogramu

### 🎨 UI/UX
- **Modern design** z Tailwind CSS
- **Responsive layout** - mobile-first approach
- **Interactive elements** - hover effects, animations
- **Accessibility** - WCAG compliant

## 🛠️ Technologie

### Backend
- **PHP 8.0+** - główny język programowania
- **Custom MVC** - własny framework
- **API Integration** - OpenAI, NBP, RSS feeds
- **Caching** - file-based cache system

### Frontend
- **Tailwind CSS** - utility-first CSS framework
- **JavaScript** - vanilla JS + jQuery
- **Responsive Design** - mobile, tablet, desktop
- **Modern UI** - gradients, shadows, animations

### Integracje
- **OpenAI API** - generowanie treści AI
- **NBP API** - stopy procentowe, kursy walut
- **RSS Feeds** - Bankier.pl, Money.pl
- **Financial APIs** - produkty bankowe

## 📁 Struktura projektu

```
Kaszflow/
├── src/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── ComparisonController.php
│   │   └── BlogAutomationController.php
│   ├── Services/
│   │   ├── ApiService.php
│   │   └── BlogAutomationService.php
│   └── Core/
│       ├── Request.php
│       ├── Response.php
│       └── Router.php
├── resources/views/
│   ├── home.php
│   ├── o-nas.php
│   ├── comparison/
│   │   ├── loans.php
│   │   ├── mortgages.php
│   │   ├── accounts.php
│   │   └── deposits.php
│   └── blog/automation/
│       ├── admin.php
│       └── settings.php
├── public/
│   ├── assets/
│   │   ├── images/
│   │   └── css/
│   └── index.php
├── config/
│   ├── blog_automation_settings.json
│   └── scheduled_jobs.json
├── cache/
├── routes/
│   └── web.php
└── docs/
    ├── BLOG_AUTOMATION_GUIDE.md
    └── PROJECT_SUMMARY.md
```

## ⚙️ Instalacja

### 1. Wymagania systemowe
```bash
# PHP 8.0+
# Composer
# OpenAI API Key
# Web server (Apache/Nginx)
```

### 2. Klonowanie repozytorium
```bash
git clone https://github.com/piotrkotulski/Kaszflow.git
cd Kaszflow
```

### 3. Konfiguracja środowiska
```bash
# Skopiuj plik .env.example
cp .env.example .env

# Edytuj zmienne środowiskowe
OPENAI_API_KEY=sk-your-openai-api-key-here
API_KEYS_LOANS=your-loans-api-key
API_KEYS_MORTGAGES=your-mortgages-api-key
API_KEYS_ACCOUNTS=your-accounts-api-key
API_KEYS_DEPOSITS=your-deposits-api-key

# Utwórz katalogi
mkdir -p cache config public/assets/images/team
chmod 755 cache config
```

### 4. Instalacja zależności
```bash
composer install
```

### 5. Konfiguracja serwera
```apache
# Apache (.htaccess)
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## 🎯 Użycie

### Porównywarki produktów
- **Kredyty gotówkowe**: `/kredyty-gotowkowe`
- **Kredyty hipoteczne**: `/kredyty-hipoteczne`
- **Konta osobiste**: `/konta-osobiste`
- **Lokaty bankowe**: `/lokaty-bankowe`

### Panel automatyzacji blogów
- **Panel główny**: `/blog/automation/admin`
- **Ustawienia**: `/blog/automation/settings`

### Strony statyczne
- **Strona główna**: `/`
- **O nas**: `/o-nas`

## 🤖 Automatyzacja blogów

### Konfiguracja
1. Przejdź do `/blog/automation/settings`
2. Wprowadź klucz API OpenAI
3. Skonfiguruj częstotliwość generowania
4. Wybierz kategorie docelowe

### Generowanie treści
- **Ręczne**: Kliknij "Wygeneruj artykuł teraz" w panelu
- **Automatyczne**: System generuje według harmonogramu
- **Trend-based**: Analiza aktualnych trendów finansowych

### Źródła danych
- **NBP API** - stopy procentowe, kursy walut
- **RSS Feeds** - Bankier.pl, Money.pl
- **Sezonowe tematy** - PIT, wakacje, Black Friday
- **Evergreen content** - poradniki finansowe

## 📊 SEO i optymalizacja

### Automatyczne SEO
- **Meta tagi** - automatyczne generowanie title i description
- **Schema markup** - structured data dla wyszukiwarek
- **Słowa kluczowe** - polskie terminy finansowe
- **Internal linking** - linki do porównywarek

### Optymalizacja treści
- **Nagłówki H2, H3** - struktura hierarchiczna
- **Listy i paragrafy** - czytelność
- **Call-to-action** - zachęta do skorzystania z porównywarek
- **Responsive images** - optymalizacja dla urządzeń mobilnych

## 🎨 Design system

### Kolory
- **Primary**: Blue (#3B82F6)
- **Secondary**: Gray (#6B7280)
- **Success**: Green (#10B981)
- **Warning**: Yellow (#F59E0B)
- **Error**: Red (#EF4444)

### Typografia
- **Headings**: Inter, sans-serif
- **Body**: Inter, sans-serif
- **Weights**: 400, 500, 600, 700

### Komponenty
- **Cards** - białe tło, shadow, rounded corners
- **Buttons** - primary, secondary, outline variants
- **Forms** - consistent styling, validation states
- **Navigation** - sticky header, mobile menu

## 📈 Monitorowanie

### Statystyki
- **Artykuły** - liczba wygenerowanych i opublikowanych
- **Wyświetlenia** - traffic z różnych źródeł
- **Konwersje** - kliknięcia w porównywarki
- **SEO** - pozycje w Google, organic traffic

### Logi
- **Error logs** - błędy API, generowania treści
- **Access logs** - ruch na stronie
- **Performance** - czas ładowania, API calls

## 🔧 Rozwiązywanie problemów

### Błędy API
```bash
# Sprawdź logi błędów
tail -f error_log

# Test połączenia OpenAI
curl -X POST /blog/automation/test-api
```

### Problemy z generowaniem
1. Sprawdź klucz API OpenAI
2. Zweryfikuj ustawienia w panelu
3. Sprawdź dostępność źródeł danych
4. Przetestuj ręczne generowanie

### Problemy z porównywarkami
1. Sprawdź klucze API produktów
2. Zweryfikuj format danych
3. Sprawdź cache system
4. Przetestuj połączenie z API

## 🗺️ Roadmapa

### Faza 1: Podstawowa funkcjonalność ✅
- [x] Porównywarki produktów
- [x] Automatyzacja blogów
- [x] Panel administracyjny
- [x] Responsive design

### Faza 2: Rozszerzenia 🔄
- [ ] Integracja z Google Analytics
- [ ] A/B testing treści
- [ ] Personalizacja użytkowników
- [ ] Social media integration

### Faza 3: Zaawansowane funkcje 📋
- [ ] Machine learning dla rekomendacji
- [ ] Chatbot finansowy
- [ ] Mobile app
- [ ] API dla partnerów

## 🤝 Współpraca

### Zgłaszanie błędów
1. Sprawdź istniejące issues
2. Utwórz nowy issue z opisem problemu
3. Dołącz logi błędów i kroki reprodukcji

### Pull requests
1. Fork repozytorium
2. Utwórz feature branch
3. Commit zmiany z opisem
4. Utwórz pull request

### Kontakt
- **Email**: piotr.kotulski1986@gmail.com
- **GitHub**: https://github.com/piotrkotulski/Kaszflow

## 📄 Licencja

Projekt jest własnością Kaszflow. Wszelkie prawa zastrzeżone.

## 🙏 Podziękowania

- **OpenAI** - za API do generowania treści
- **NBP** - za dane finansowe
- **Tailwind CSS** - za framework CSS
- **Bankier.pl, Money.pl** - za RSS feeds

---

**Wersja:** 1.0.0  
**Ostatnia aktualizacja:** Styczeń 2024  
**Autor:** Piotr Kotulski  
**Organizacja:** Kaszflow 