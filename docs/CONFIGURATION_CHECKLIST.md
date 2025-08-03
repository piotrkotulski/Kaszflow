# Lista Konfiguracji Kaszflow

## ✅ Co zostało zaimplementowane

### 🗂️ System Cache
- ✅ **CacheService** - Automatyczne pobieranie i przechowywanie danych z API
- ✅ **CacheController** - Panel administracyjny cache
- ✅ **Skrypt aktualizacji** - `scripts/update_cache.php`
- ✅ **Cron job** - Automatyczna aktualizacja codziennie o 3:00
- ✅ **Panel admin** - `http://localhost:8000/cache/admin`
- ✅ **Logi** - `logs/cache_update.log`

### 🤖 System Blog Automation
- ✅ **BlogAutomationService** - Automatyczne generowanie artykułów
- ✅ **BlogAutomationController** - Panel administracyjny bloga
- ✅ **Źródła danych** - NBP API, RSS feeds, ogłoszenia rządowe
- ✅ **Optymalizacja SEO** - Meta tagi, schema markup
- ✅ **Silniki AI** - OpenAI GPT-4 + Claude AI (Anthropic)
- ✅ **Panel admin** - `http://localhost:8000/blog/automation/admin`
- ✅ **Ustawienia** - `http://localhost:8000/blog/automation/settings`
- ✅ **Blog** - `http://localhost:8000/blog`

### 📊 Porównywarka produktów
- ✅ **FinancialApiService** - Integracja z API produktów finansowych
- ✅ **ComparisonController** - Strony porównywarki
- ✅ **Wszystkie typy produktów** - Kredyty, konta, lokaty, hipoteki
- ✅ **Sortowanie i filtrowanie** - Według różnych kryteriów

### 🏠 Strona główna
- ✅ **Hero section** - Z porównaniami najlepszych ofert
- ✅ **Najlepsze oferty** - 3 karty dla każdego typu produktu
- ✅ **Bank partners** - Slick z logami banków
- ✅ **O nas** - Strona z informacjami o firmie

### 📚 Blog
- ✅ **Lista artykułów** - `http://localhost:8000/blog`
- ✅ **Pojedynczy artykuł** - `http://localhost:8000/blog/{slug}`
- ✅ **Widoki bloga** - Index, post, show

## ⚙️ Co trzeba skonfigurować

### 1. Zmienne środowiskowe (.env)

#### API Keys dla produktów finansowych
```bash
# Dodaj do .env
API_TOKEN_LOANS=your_loans_api_token
API_TOKEN_MORTGAGES=your_mortgages_api_token
API_TOKEN_ACCOUNTS=your_accounts_api_token
API_TOKEN_DEPOSITS=your_deposits_api_token
```

#### OpenAI API dla bloga
```bash
# Dodaj do .env
OPENAI_API_KEY=your_openai_api_key_here
```

#### Ustawienia cache
```bash
# Dodaj do .env
CACHE_DIR=data/cache
CACHE_EXPIRY=86400
```

### 2. Katalogi i uprawnienia
```bash
# Utwórz katalogi
mkdir -p data/cache
mkdir -p logs
chmod 755 data/cache logs
```

### 3. Cron job dla cache
```bash
# Uruchom skrypt konfiguracyjny
./scripts/setup_cron.sh

# Sprawdź czy cron został dodany
crontab -l | grep update_cache
```

### 4. Testowanie komponentów

#### Test cache
```bash
# Ręczna aktualizacja
php scripts/update_cache.php

# Sprawdź panel admin
open http://localhost:8000/cache/admin
```

#### Test blog automation
```bash
# Sprawdź panel admin
open http://localhost:8000/blog/automation/admin

# Test API (jeśli masz OpenAI key)
curl -X POST http://localhost:8000/blog/automation/test-api
```

#### Test porównywarki
```bash
# Sprawdź wszystkie strony
curl -s http://localhost:8000/kredyty-gotowkowe
curl -s http://localhost:8000/konta-osobiste
curl -s http://localhost:8000/lokaty
```

### 5. Monitoring i logi

#### Sprawdź logi
```bash
# Cache updates
tail -f logs/cache_update.log

# Blog automation (jeśli istnieje)
tail -f logs/blog_automation.log

# Application errors
tail -f logs/error.log
```

#### Sprawdź pliki cache
```bash
# Sprawdź czy dane są pobierane
ls -la data/cache/
cat data/cache/loans.json | head -20
```

## 🚨 Potencjalne problemy

### 1. Błędy API
- **Problem**: 401 Unauthorized
- **Rozwiązanie**: Sprawdź tokeny API w .env
- **Test**: `curl -H "Authorization: Bearer $API_TOKEN_LOANS" "https://api.systempartnerski.pl/publishers/financial-products-api/getdata?product_type=kredyty_gotowkowe"`

### 2. Brak danych w cache
- **Problem**: Puste pliki cache
- **Rozwiązanie**: Wymuś aktualizację `php scripts/update_cache.php`
- **Sprawdź**: Logi w `logs/cache_update.log`

### 3. Blog nie generuje artykułów
- **Problem**: Brak klucza OpenAI
- **Rozwiązanie**: Dodaj `OPENAI_API_KEY=your_key` do .env
- **Test**: Panel admin bloga

### 4. Cron nie działa
- **Problem**: Brak wpisu crona
- **Rozwiązanie**: Uruchom `./scripts/setup_cron.sh`
- **Sprawdź**: `crontab -l`

## 📋 Checklist konfiguracji

### Podstawowa konfiguracja
- [ ] Skopiuj `env.example` do `.env`
- [ ] Dodaj tokeny API dla produktów finansowych
- [ ] Dodaj klucz OpenAI (opcjonalnie)
- [ ] Utwórz katalogi `data/cache` i `logs`
- [ ] Ustaw uprawnienia `chmod 755 data/cache logs`

### System Cache
- [ ] Uruchom `./scripts/setup_cron.sh`
- [ ] Sprawdź `crontab -l | grep update_cache`
- [ ] Przetestuj `php scripts/update_cache.php`
- [ ] Sprawdź panel admin `http://localhost:8000/cache/admin`

### System Blog
- [ ] Sprawdź panel admin `http://localhost:8000/blog/automation/admin`
- [ ] Przetestuj generowanie artykułu (jeśli masz OpenAI key)
- [ ] Sprawdź blog `http://localhost:8000/blog`

### Porównywarka
- [ ] Sprawdź kredyty `http://localhost:8000/kredyty-gotowkowe`
- [ ] Sprawdź konta `http://localhost:8000/konta-osobiste`
- [ ] Sprawdź lokaty `http://localhost:8000/lokaty`
- [ ] Sprawdź hipoteki `http://localhost:8000/kredyty-hipoteczne`

### Monitoring
- [ ] Sprawdź logi cache `tail -f logs/cache_update.log`
- [ ] Sprawdź pliki cache `ls -la data/cache/`
- [ ] Sprawdź status crona `crontab -l`

## 📚 Dokumentacja

### Dostępne dokumenty
- **Setup Guide**: `docs/SETUP_GUIDE.md` - Kompletny przewodnik instalacji
- **Cache System**: `docs/CACHE_SYSTEM.md` - Dokumentacja systemu cache
- **Blog Automation**: `docs/BLOG_AUTOMATION.md` - Dokumentacja automatyzacji bloga

### Przydatne komendy
```bash
# Sprawdź status wszystkich komponentów
curl -s http://localhost:8000/cache/admin && echo "Cache OK"
curl -s http://localhost:8000/blog/automation/admin && echo "Blog OK"
curl -s http://localhost:8000/ && echo "Home OK"

# Backup systemu
tar -czf kaszflow_backup_$(date +%Y%m%d_%H%M%S).tar.gz \
    --exclude=data/cache --exclude=logs .

# Restart serwera
php -S localhost:8000 -t public
```

## 🎯 Status systemu

### ✅ Gotowe do użycia
- System cache z automatyczną aktualizacją
- Panel administracyjny cache
- Porównywarka produktów finansowych
- Strona główna z najlepszymi ofertami
- Blog z listą artykułów

### ⚠️ Wymaga konfiguracji
- Tokeny API dla produktów finansowych
- Klucz OpenAI dla automatyzacji bloga (opcjonalnie)
- Cron job dla automatycznej aktualizacji cache

### 🔧 W trakcie rozwoju
- Integracja z bazą danych
- Zaawansowane funkcje blog automation
- Dodatkowe źródła danych
- System użytkowników i autoryzacji 