#!/bin/bash

# Kaszflow - Setup Script
# Skrypt do uruchamiania projektu lokalnie

echo "🚀 Uruchamianie projektu Kaszflow..."

# Sprawdzenie wymagań
echo "📋 Sprawdzanie wymagań..."

# Sprawdzenie PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP nie jest zainstalowany. Zainstaluj PHP 8.0+"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✅ PHP $PHP_VERSION"

# Sprawdzenie Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer nie jest zainstalowany. Zainstaluj Composer"
    exit 1
fi

echo "✅ Composer"

# Sprawdzenie Node.js
if ! command -v node &> /dev/null; then
    echo "❌ Node.js nie jest zainstalowany. Zainstaluj Node.js 18+"
    exit 1
fi

NODE_VERSION=$(node --version)
echo "✅ Node.js $NODE_VERSION"

# Sprawdzenie npm
if ! command -v npm &> /dev/null; then
    echo "❌ npm nie jest zainstalowany"
    exit 1
fi

echo "✅ npm"

echo ""
echo "📦 Instalacja zależności..."

# Instalacja zależności PHP
echo "Instalowanie zależności PHP..."
composer install --no-dev --optimize-autoloader

# Instalacja zależności Node.js
echo "Instalowanie zależności Node.js..."
npm install

# Budowanie frontend
echo "Budowanie frontend..."
npm run build

# Tworzenie pliku .env
if [ ! -f .env ]; then
    echo "Tworzenie pliku .env..."
    cp env.example .env
    echo "⚠️  Edytuj plik .env i ustaw odpowiednie wartości"
fi

# Tworzenie katalogów
echo "Tworzenie katalogów..."
mkdir -p storage/logs storage/cache public/assets/images

# Ustawienie uprawnień
echo "Ustawianie uprawnień..."
chmod -R 755 storage
chmod -R 755 public

# Sprawdzenie bazy danych
echo ""
echo "🗄️  Konfiguracja bazy danych..."

# Sprawdzenie MySQL
if command -v mysql &> /dev/null; then
    echo "✅ MySQL jest dostępny"
    echo "📝 Utwórz bazę danych 'kaszflow' jeśli jeszcze nie istnieje"
else
    echo "⚠️  MySQL nie jest zainstalowany. Zainstaluj MySQL lub użyj innej bazy danych"
fi

# Sprawdzenie Redis (opcjonalnie)
if command -v redis-server &> /dev/null; then
    echo "✅ Redis jest dostępny"
else
    echo "ℹ️  Redis nie jest zainstalowany (opcjonalne dla cache)"
fi

echo ""
echo "🎉 Instalacja zakończona!"
echo ""
echo "📝 Następne kroki:"
echo "1. Edytuj plik .env i ustaw odpowiednie wartości"
echo "2. Utwórz bazę danych MySQL 'kaszflow'"
echo "3. Uruchom serwer deweloperski:"
echo "   php -S localhost:8000 -t public"
echo ""
echo "🌐 Strona będzie dostępna pod adresem: http://localhost:8000"
echo ""
echo "📚 Dokumentacja: README.md" 