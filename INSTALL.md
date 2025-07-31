# Instrukcje instalacji i uruchamiania Kaszflow

## 🚀 Szybki start

### Wymagania systemowe

- **PHP** 8.0 lub nowszy
- **Node.js** 18 lub nowszy
- **Composer** (menedżer zależności PHP)
- **MySQL** 5.7+ lub **PostgreSQL** 12+
- **Redis** (opcjonalnie, dla cache)

### Krok 1: Klonowanie repozytorium

```bash
git clone https://github.com/your-username/kaszflow.git
cd kaszflow
```

### Krok 2: Instalacja zależności

```bash
# Instalacja zależności PHP
composer install

# Instalacja zależności Node.js
npm install
```

### Krok 3: Konfiguracja środowiska

```bash
# Kopiowanie pliku konfiguracyjnego
cp env.example .env

# Edycja pliku .env
nano .env
```

Edytuj plik `.env` i ustaw odpowiednie wartości:

```env
# API Configuration
API_BASE_URL=https://api.systempartnerski.pl
API_TOKEN=your_api_token_here
API_TIMEOUT=30

# Database
DB_HOST=localhost
DB_NAME=kaszflow
DB_USER=root
DB_PASS=your_password

# OpenAI (dla automatycznego bloga)
OPENAI_API_KEY=your_openai_api_key_here

# Analytics
GOOGLE_ANALYTICS_ID=GA_MEASUREMENT_ID
FACEBOOK_PIXEL_ID=your_facebook_pixel_id

# Email
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your_email@gmail.com
SMTP_PASS=your_app_password

# Aplikacja
APP_NAME=Kaszflow
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### Krok 4: Konfiguracja bazy danych

```bash
# Utworzenie bazy danych MySQL
mysql -u root -p
CREATE DATABASE kaszflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Lub dla PostgreSQL
createdb kaszflow
```

### Krok 5: Budowanie frontend

```bash
# Budowanie CSS i JavaScript
npm run build
```

### Krok 6: Uruchomienie serwera deweloperskiego

```bash
# Uruchomienie serwera PHP
php -S localhost:8000 -t public
```

Strona będzie dostępna pod adresem: **http://localhost:8000**

## 🔧 Konfiguracja zaawansowana

### Konfiguracja Nginx (produkcja)

```nginx
server {
    listen 80;
    server_name kaszflow.pl www.kaszflow.pl;
    root /var/www/kaszflow/public;
    index index.php index.html;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### Konfiguracja SSL/HTTPS

```bash
# Instalacja Certbot
sudo apt install certbot python3-certbot-nginx

# Konfiguracja SSL
sudo certbot --nginx -d kaszflow.pl -d www.kaszflow.pl
```

### Konfiguracja Redis (opcjonalnie)

```bash
# Instalacja Redis
sudo apt install redis-server

# Konfiguracja w .env
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=
```

## 🛠️ Skrypty pomocnicze

### Automatyczna instalacja

```bash
# Uruchomienie skryptu instalacyjnego
chmod +x scripts/setup.sh
./scripts/setup.sh
```

### Deploy na OVH

```bash
# Edycja konfiguracji deployu
nano scripts/deploy-ovh.sh

# Uruchomienie deployu
chmod +x scripts/deploy-ovh.sh
./scripts/deploy-ovh.sh
```

## 📊 Struktura projektu

```
kaszflow/
├── public/                 # Pliki publiczne
│   ├── index.php          # Główny plik wejściowy
│   └── assets/            # Zasoby statyczne
├── src/                    # Kod źródłowy PHP
│   ├── Controllers/        # Kontrolery
│   ├── Models/            # Modele danych
│   ├── Services/          # Serwisy biznesowe
│   └── Utils/             # Narzędzia
├── resources/             # Zasoby frontend
│   ├── js/               # JavaScript
│   ├── css/              # Style
│   └── views/            # Szablony
├── config/               # Konfiguracja
├── storage/              # Pliki tymczasowe
│   ├── logs/             # Logi
│   └── cache/            # Cache
├── database/             # Migracje i seedery
└── tests/                # Testy
```

## 🔍 Rozwiązywanie problemów

### Problem: Błąd "Class not found"

```bash
# Regeneracja autoloadera
composer dump-autoload
```

### Problem: Błąd bazy danych

```bash
# Sprawdzenie połączenia
php -r "try { new PDO('mysql:host=localhost;dbname=kaszflow', 'root', 'password'); echo 'OK'; } catch(Exception \$e) { echo 'Error: ' . \$e->getMessage(); }"
```

### Problem: Błąd uprawnień

```bash
# Ustawienie uprawnień
chmod -R 755 storage public
chown -R www-data:www-data storage
```

### Problem: Błąd API

```bash
# Sprawdzenie klucza API
curl -H "X-Auth-Token: YOUR_TOKEN" https://api.systempartnerski.pl/publishers/financial-products-api/getdata
```

## 📈 Monitoring i logi

### Logi aplikacji

```bash
# Wyświetlanie logów
tail -f storage/logs/app.log

# Czyszczenie logów
echo "" > storage/logs/app.log
```

### Monitoring wydajności

```bash
# Sprawdzenie użycia pamięci
free -h

# Sprawdzenie dysku
df -h

# Sprawdzenie procesów PHP
ps aux | grep php
```

## 🔒 Bezpieczeństwo

### Najlepsze praktyki

1. **Zawsze używaj HTTPS w produkcji**
2. **Regularnie aktualizuj zależności**
3. **Używaj silnych haseł**
4. **Regularnie twórz backup bazy danych**
5. **Monitoruj logi pod kątem nieprawidłowości**

### Backup bazy danych

```bash
# Backup MySQL
mysqldump -u root -p kaszflow > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup PostgreSQL
pg_dump kaszflow > backup_$(date +%Y%m%d_%H%M%S).sql
```

## 📞 Wsparcie

- **Dokumentacja**: README.md
- **Issues**: GitHub Issues
- **Email**: kontakt@kaszflow.pl

## 🚀 Deploy na produkcję

### Przygotowanie serwera

```bash
# Aktualizacja systemu
sudo apt update && sudo apt upgrade -y

# Instalacja wymaganych pakietów
sudo apt install nginx php8.1-fpm php8.1-mysql php8.1-curl php8.1-mbstring php8.1-xml php8.1-zip mysql-server composer nodejs npm git
```

### Deploy aplikacji

```bash
# Klonowanie repozytorium
cd /var/www
git clone https://github.com/your-username/kaszflow.git

# Instalacja zależności
cd kaszflow
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Ustawienie uprawnień
chmod -R 755 storage public
chown -R www-data:www-data storage
```

### Konfiguracja Nginx

```bash
# Kopiowanie konfiguracji
sudo cp /var/www/kaszflow/nginx.conf /etc/nginx/sites-available/kaszflow

# Aktywacja
sudo ln -s /etc/nginx/sites-available/kaszflow /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### SSL/HTTPS

```bash
# Instalacja Certbot
sudo apt install certbot python3-certbot-nginx

# Konfiguracja SSL
sudo certbot --nginx -d kaszflow.pl -d www.kaszflow.pl
```

## 🎉 Gotowe!

Twoja aplikacja Kaszflow jest teraz gotowa do użycia!

- **Lokalnie**: http://localhost:8000
- **Produkcja**: https://kaszflow.pl

Pamiętaj o regularnym monitorowaniu i aktualizacji aplikacji! 