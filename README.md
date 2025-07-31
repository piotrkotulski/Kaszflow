o # Kaszflow - Serwis Porównywania Produktów Finansowych

Nowoczesny serwis internetowy do porównywania produktów finansowych z automatycznym generowaniem treści i zaawansowaną analityką.

## 🚀 Funkcje

- **Porównywarka produktów finansowych** (kredyty, konta, lokaty, hipoteki)
- **Automatyczne generowanie treści** z wykorzystaniem AI
- **System śledzenia i analityki** 
- **Personalizacja i rekomendacje**
- **Newsletter i email marketing**
- **Program partnerski**

## 🛠️ Technologie

### Backend
- PHP 8.0+
- Laravel/Symfony (do wyboru)
- MySQL/PostgreSQL
- Redis (cache)
- Elasticsearch (wyszukiwanie)

### Frontend
- Vue.js 3
- Tailwind CSS
- Vite
- Chart.js (wykresy)
- Headless UI

### DevOps
- Docker
- Nginx
- SSL/HTTPS
- CDN

## 📦 Instalacja

### Wymagania
- PHP 8.0+
- Node.js 18+
- Composer
- Docker (opcjonalnie)

### Krok 1: Klonowanie repozytorium
```bash
git clone https://github.com/your-username/kaszflow.git
cd kaszflow
```

### Krok 2: Instalacja zależności PHP
```bash
composer install
```

### Krok 3: Instalacja zależności Node.js
```bash
npm install
```

### Krok 4: Konfiguracja środowiska
```bash
cp .env.example .env
# Edytuj .env z odpowiednimi danymi
```

### Krok 5: Budowanie frontend
```bash
npm run build
```

### Krok 6: Uruchomienie serwera deweloperskiego
```bash
php -S localhost:8000 -t public
```

## 🔧 Konfiguracja

### Zmienne środowiskowe (.env)
```env
# API Configuration
API_BASE_URL=https://api.systempartnerski.pl
API_TOKEN=your_api_token
API_TIMEOUT=30

# Database
DB_HOST=localhost
DB_NAME=kaszflow
DB_USER=root
DB_PASS=

# OpenAI (dla automatycznego bloga)
OPENAI_API_KEY=your_openai_key

# Analytics
GOOGLE_ANALYTICS_ID=GA_MEASUREMENT_ID
FACEBOOK_PIXEL_ID=your_pixel_id

# Email
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your_email
SMTP_PASS=your_password
```

## 🚀 Deployment na OVH

### Krok 1: Przygotowanie serwera
```bash
# Połączenie z serwerem OVH
ssh user@your-server.com

# Aktualizacja systemu
sudo apt update && sudo apt upgrade -y

# Instalacja wymaganych pakietów
sudo apt install nginx php8.1-fpm php8.1-mysql php8.1-curl php8.1-mbstring php8.1-xml php8.1-zip mysql-server composer nodejs npm git
```

### Krok 2: Konfiguracja Nginx
```bash
sudo nano /etc/nginx/sites-available/kaszflow
```

```nginx
server {
    listen 80;
    server_name kaszflow.pl www.kaszflow.pl;
    root /var/www/kaszflow/public;
    index index.php index.html;

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

### Krok 3: Aktywacja konfiguracji
```bash
sudo ln -s /etc/nginx/sites-available/kaszflow /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Krok 4: SSL/HTTPS
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d kaszflow.pl -d www.kaszflow.pl
```

## 📊 Struktura projektu

```
kaszflow/
├── public/                 # Pliki publiczne
├── src/                    # Kod źródłowy PHP
│   ├── Controllers/        # Kontrolery
│   ├── Models/            # Modele danych
│   ├── Services/          # Serwisy biznesowe
│   └── Utils/             # Narzędzia
├── resources/             # Zasoby frontend
│   ├── js/               # JavaScript/Vue
│   ├── css/              # Style
│   └── views/            # Szablony
├── database/             # Migracje i seedery
├── config/               # Konfiguracja
├── storage/              # Pliki tymczasowe
└── tests/                # Testy
```

## 🔍 API Endpoints

### Produkty finansowe
- `GET /api/products/loans` - Kredyty gotówkowe
- `GET /api/products/mortgages` - Kredyty hipoteczne
- `GET /api/products/accounts` - Konta osobiste
- `GET /api/products/savings` - Konta oszczędnościowe
- `GET /api/products/deposits` - Lokaty

### Blog
- `GET /api/blog/posts` - Lista artykułów
- `POST /api/blog/generate` - Generowanie artykułu

### Analytics
- `POST /api/analytics/track` - Śledzenie kliknięć
- `GET /api/analytics/stats` - Statystyki

## 🤝 Contributing

1. Fork projektu
2. Utwórz branch (`git checkout -b feature/amazing-feature`)
3. Commit zmian (`git commit -m 'Add amazing feature'`)
4. Push do branch (`git push origin feature/amazing-feature`)
5. Otwórz Pull Request

## 📝 Licencja

MIT License - zobacz plik [LICENSE](LICENSE) dla szczegółów.

## 📞 Kontakt

- Email: kontakt@kaszflow.pl
- Website: https://kaszflow.pl 