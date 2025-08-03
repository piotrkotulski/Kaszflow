#!/bin/bash

# Skrypt do konfiguracji crona dla automatycznej aktualizacji cache Kaszflow

echo "=== Konfiguracja crona dla Kaszflow ==="

# Ścieżka do skryptu aktualizacji
SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/update_cache.php"
PROJECT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "Ścieżka do skryptu: $SCRIPT_PATH"
echo "Ścieżka do projektu: $PROJECT_PATH"

# Sprawdź czy skrypt istnieje
if [ ! -f "$SCRIPT_PATH" ]; then
    echo "❌ Błąd: Skrypt update_cache.php nie istnieje!"
    exit 1
fi

# Sprawdź czy katalog cache istnieje
CACHE_DIR="$PROJECT_PATH/data/cache"
if [ ! -d "$CACHE_DIR" ]; then
    echo "📁 Tworzenie katalogu cache..."
    mkdir -p "$CACHE_DIR"
    chmod 755 "$CACHE_DIR"
fi

# Sprawdź uprawnienia do zapisu
if [ ! -w "$CACHE_DIR" ]; then
    echo "❌ Błąd: Brak uprawnień do zapisu w katalogu cache!"
    exit 1
fi

# Utwórz wpis crona (aktualizacja codziennie o 3:00)
CRON_JOB="0 3 * * * cd $PROJECT_PATH && php $SCRIPT_PATH >> $PROJECT_PATH/logs/cache_update.log 2>&1"

echo ""
echo "📋 Proponowany wpis crona:"
echo "$CRON_JOB"
echo ""

# Zapytaj użytkownika czy dodać wpis do crona
read -p "Czy chcesz dodać ten wpis do crona? (y/n): " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    # Sprawdź czy wpis już istnieje
    if crontab -l 2>/dev/null | grep -q "update_cache.php"; then
        echo "⚠️  Wpis crona już istnieje!"
        echo "Aktualne wpisy crona:"
        crontab -l 2>/dev/null | grep "update_cache.php"
    else
        # Dodaj wpis do crona
        (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
        echo "✅ Wpis crona został dodany!"
    fi
else
    echo "ℹ️  Wpis crona nie został dodany."
    echo "Możesz dodać go ręcznie używając komendy:"
    echo "crontab -e"
    echo "i dodając linię:"
    echo "$CRON_JOB"
fi

echo ""
echo "=== Instrukcje ==="
echo "1. Cache będzie aktualizowany codziennie o 3:00"
echo "2. Logi będą zapisywane w: $PROJECT_PATH/logs/cache_update.log"
echo "3. Możesz sprawdzić status cache w: http://localhost:8000/cache/admin"
echo "4. Możesz wymusić aktualizację w: http://localhost:8000/cache/refresh"
echo ""
echo "✅ Konfiguracja zakończona!" 