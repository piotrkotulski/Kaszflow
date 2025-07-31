#!/bin/bash

# Kaszflow - OVH Deployment Script
# Skrypt do deployu na serwer OVH

# Konfiguracja
SERVER_HOST="your-server.com"
SERVER_USER="your-username"
PROJECT_NAME="kaszflow"
DOMAIN="kaszflow.pl"

echo "🚀 Deploy projektu Kaszflow na OVH..."

# Sprawdzenie połączenia z serwerem
echo "📡 Sprawdzanie połączenia z serwerem..."
if ! ssh -o ConnectTimeout=10 $SERVER_USER@$SERVER_HOST "echo 'Połączenie OK'" 2>/dev/null; then
    echo "❌ Nie można połączyć się z serwerem. Sprawdź konfigurację SSH."
    exit 1
fi

echo "✅ Połączenie z serwerem OK"

# Backup obecnej wersji
echo "💾 Tworzenie backupu..."
ssh $SERVER_USER@$SERVER_HOST "cd /var/www && if [ -d $PROJECT_NAME ]; then tar -czf ${PROJECT_NAME}_backup_$(date +%Y%m%d_%H%M%S).tar.gz $PROJECT_NAME; fi"

# Upload plików
echo "📤 Upload plików..."
rsync -avz --exclude='.git' --exclude='node_modules' --exclude='vendor' --exclude='storage/logs/*' --exclude='storage/cache/*' ./ $SERVER_USER@$SERVER_HOST:/var/www/$PROJECT_NAME/

# Instalacja zależności na serwerze
echo "📦 Instalacja zależności na serwerze..."
ssh $SERVER_USER@$SERVER_HOST "cd /var/www/$PROJECT_NAME && composer install --no-dev --optimize-autoloader"

# Budowanie frontend na serwerze
echo "🔨 Budowanie frontend na serwerze..."
ssh $SERVER_USER@$SERVER_HOST "cd /var/www/$PROJECT_NAME && npm install && npm run build"

# Ustawienie uprawnień
echo "🔐 Ustawianie uprawnień..."
ssh $SERVER_USER@$SERVER_HOST "cd /var/www/$PROJECT_NAME && chmod -R 755 storage public && chown -R www-data:www-data storage"

# Konfiguracja Nginx
echo "🌐 Konfiguracja Nginx..."
cat > /tmp/kaszflow-nginx << EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root /var/www/$PROJECT_NAME/public;
    index index.php index.html;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Handle PHP files
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Handle static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security: deny access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ /(vendor|storage|config|database|tests) {
        deny all;
    }

    # Main location block
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # Error pages
    error_page 404 /404.html;
    error_page 500 502 503 504 /50x.html;
}
EOF

# Upload konfiguracji Nginx
scp /tmp/kaszflow-nginx $SERVER_USER@$SERVER_HOST:/tmp/kaszflow-nginx

# Aktywacja konfiguracji Nginx
ssh $SERVER_USER@$SERVER_HOST "sudo mv /tmp/kaszflow-nginx /etc/nginx/sites-available/$PROJECT_NAME && sudo ln -sf /etc/nginx/sites-available/$PROJECT_NAME /etc/nginx/sites-enabled/ && sudo nginx -t && sudo systemctl reload nginx"

# Konfiguracja SSL/HTTPS
echo "🔒 Konfiguracja SSL/HTTPS..."
ssh $SERVER_USER@$SERVER_HOST "sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN"

# Konfiguracja PHP-FPM
echo "⚙️  Konfiguracja PHP-FPM..."
ssh $SERVER_USER@$SERVER_HOST "sudo systemctl restart php8.1-fpm"

# Konfiguracja cron jobs
echo "⏰ Konfiguracja cron jobs..."
ssh $SERVER_USER@$SERVER_HOST "crontab -l 2>/dev/null | { cat; echo '0 2 * * * cd /var/www/$PROJECT_NAME && php scripts/cleanup.php'; echo '0 3 * * * cd /var/www/$PROJECT_NAME && php scripts/backup.php'; } | crontab -"

# Sprawdzenie statusu
echo "🔍 Sprawdzanie statusu..."
ssh $SERVER_USER@$SERVER_HOST "sudo systemctl status nginx php8.1-fpm --no-pager"

# Test strony
echo "🧪 Test strony..."
if curl -s -o /dev/null -w "%{http_code}" "https://$DOMAIN" | grep -q "200"; then
    echo "✅ Strona działa poprawnie!"
else
    echo "⚠️  Strona może nie działać poprawnie. Sprawdź logi."
fi

echo ""
echo "🎉 Deploy zakończony!"
echo ""
echo "📝 Informacje:"
echo "🌐 Strona: https://$DOMAIN"
echo "📧 Email: admin@$DOMAIN"
echo "📚 Logi: /var/log/nginx/ /var/log/php8.1-fpm.log"
echo ""
echo "🔧 Przydatne komendy:"
echo "sudo systemctl restart nginx"
echo "sudo systemctl restart php8.1-fpm"
echo "sudo certbot renew"
echo "tail -f /var/log/nginx/error.log" 