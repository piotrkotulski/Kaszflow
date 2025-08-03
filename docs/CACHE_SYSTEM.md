# System Cache Kaszflow

## Przegląd

System cache Kaszflow automatycznie pobiera i przechowuje dane z API produktów finansowych, aby przyspieszyć ładowanie strony i zmniejszyć obciążenie API.

## Funkcje

### 🔄 Automatyczna aktualizacja
- **Częstotliwość**: Codziennie o 3:00
- **Typy danych**: Kredyty, konta, lokaty, kredyty hipoteczne
- **Czas ważności**: 24 godziny

### 📊 Zarządzanie cache
- Panel administracyjny: `/cache/admin`
- Wymuszenie aktualizacji: `/cache/refresh`
- Czyszczenie starego cache: `/cache/clean`

### 🗂️ Struktura plików
```
data/cache/
├── loans.json          # Kredyty gotówkowe
├── accounts.json       # Konta osobiste
├── deposits.json       # Lokaty
└── mortgages.json      # Kredyty hipoteczne
```

## Konfiguracja

### 1. Instalacja crona
```bash
# Uruchom skrypt konfiguracyjny
./scripts/setup_cron.sh
```

### 2. Ręczna aktualizacja
```bash
# Aktualizacja wszystkich danych
php scripts/update_cache.php

# Aktualizacja konkretnego typu
php scripts/update_cache.php --type=loans
```

### 3. Sprawdzenie statusu
```bash
# Sprawdź pliki cache
ls -la data/cache/

# Sprawdź logi aktualizacji
tail -f logs/cache_update.log
```

## API Endpoints

### Cache Admin
- `GET /cache/admin` - Panel zarządzania cache
- `GET /cache/refresh` - Wymuszenie aktualizacji
- `GET /cache/clean` - Czyszczenie starego cache

## Monitoring

### Logi
- **Lokalizacja**: `logs/cache_update.log`
- **Format**: Timestamp + status aktualizacji
- **Rotacja**: Automatyczna (system cron)

### Statystyki
- Liczba produktów w cache
- Czas ostatniej aktualizacji
- Rozmiar plików cache
- Status ważności cache

## Troubleshooting

### Problem: Błąd 401 Unauthorized
```bash
# Sprawdź tokeny API w .env
cat .env | grep API_TOKEN

# Przetestuj połączenie
php scripts/update_cache.php
```

### Problem: Brak danych w cache
```bash
# Wymuś aktualizację
php scripts/update_cache.php

# Sprawdź logi
tail -f logs/cache_update.log
```

### Problem: Stary cache
```bash
# Wyczyść stary cache
php -r "
require_once 'vendor/autoload.php';
use Kaszflow\Services\CacheService;
\$cache = new CacheService();
echo 'Usunięto: ' . \$cache->cleanOldCache() . ' plików';
"
```

## Optymalizacja

### Wydajność
- Cache jest ładowany tylko gdy wygasł (24h)
- Dane są kompresowane w JSON
- Automatyczne czyszczenie starych plików

### Bezpieczeństwo
- Tokeny API są przechowywane w .env
- Pliki cache są chronione przed publicznym dostępem
- Logi nie zawierają wrażliwych danych

## Rozszerzenia

### Dodanie nowego typu danych
1. Dodaj metodę w `FinancialApiService`
2. Zaktualizuj `CacheService::fetchFromApi()`
3. Dodaj routing w `CacheController`
4. Zaktualizuj skrypt crona

### Zmiana częstotliwości aktualizacji
```bash
# Edytuj crontab
crontab -e

# Przykład: co 6 godzin
0 */6 * * * cd /path/to/kaszflow && php scripts/update_cache.php
```

## Wsparcie

W przypadku problemów z systemem cache:
1. Sprawdź logi: `logs/cache_update.log`
2. Sprawdź uprawnienia: `ls -la data/cache/`
3. Przetestuj API: `php scripts/update_cache.php`
4. Skontaktuj się z administratorem 