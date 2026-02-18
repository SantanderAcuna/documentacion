# Configuración de Producción para Escalabilidad

## Variables de Entorno Recomendadas

### Base de Datos - MySQL 8.0+ (Producción)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cms_santamarta
DB_USERNAME=cms_user
DB_PASSWORD=CAMBIAR_PASSWORD_SEGURO

# Charset y Collation (4FN con soporte Unicode completo)
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

# Opciones de performance
DB_STRICT_MODE=true
DB_ENGINE=InnoDB
```

### Redis - Caché y Queues (Alta Performance)
```env
# Caché
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=0

# Queue
QUEUE_CONNECTION=redis
REDIS_QUEUE_DB=1
REDIS_QUEUE=default
```

### Session - Redis para Múltiples Servidores
```env
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_CONNECTION=session
REDIS_SESSION_DB=2
```

### Broadcasting - Real-time (Opcional)
```env
BROADCAST_DRIVER=redis
BROADCAST_CONNECTION=broadcasting
REDIS_BROADCAST_DB=3
```

### Filesystem - Almacenamiento Escalable
```env
# Local para desarrollo
FILESYSTEM_DISK=local

# S3 para producción con CDN
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=cms-santamarta-media
# AWS_URL=https://s3.amazonaws.com
# AWS_CDN_URL=https://cdn.santamarta.gov.co
```

### Mail - Queued para Performance
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@santamarta.gov.co"
MAIL_FROM_NAME="${APP_NAME}"

# Queue mail jobs
MAIL_QUEUE_CONNECTION=redis
MAIL_QUEUE=mail
```

### Application - Optimización
```env
APP_NAME="CMS Gubernamental Santa Marta"
APP_ENV=production
APP_KEY=base64:GENERAR_CON_php_artisan_key:generate
APP_DEBUG=false
APP_TIMEZONE=America/Bogota
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_CO

# URL
APP_URL=https://cms.santamarta.gov.co

# Asset optimization
ASSET_URL=https://cdn.santamarta.gov.co

# Maintenance mode
APP_MAINTENANCE_DRIVER=cache
APP_MAINTENANCE_STORE=redis
```

### Logging - Monitoreo de Performance
```env
LOG_CHANNEL=stack
LOG_STACK=daily,slack
LOG_LEVEL=warning
LOG_DAILY_DAYS=14

# Slow query logging
LOG_SLOW_QUERIES=true
LOG_SLOW_QUERY_THRESHOLD=1000  # milliseconds
```

### Performance Optimization
```env
# Compiled class optimization
OPTIMIZE_CLEAR_CACHE_ON_DEPLOY=true

# Opcache (PHP)
OPCACHE_ENABLE=1
OPCACHE_MEMORY_CONSUMPTION=256
OPCACHE_MAX_ACCELERATED_FILES=20000

# Queue workers
QUEUE_WORKERS=4
QUEUE_TIMEOUT=60
QUEUE_RETRY_AFTER=90
QUEUE_MAX_JOBS=1000
QUEUE_MAX_TIME=3600
```

### Security
```env
# HTTPS enforcement
FORCE_HTTPS=true
SECURE_COOKIES=true

# Rate limiting
THROTTLE_REQUESTS=60
THROTTLE_DECAY_MINUTES=1

# CORS
CORS_ALLOWED_ORIGINS=https://santamarta.gov.co,https://www.santamarta.gov.co
```

### Monitoring & APM (Opcional)
```env
# New Relic
NEW_RELIC_ENABLED=false
NEW_RELIC_APP_NAME=CMS_Santa_Marta
NEW_RELIC_LICENSE_KEY=

# Sentry Error Tracking
SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=0.1
```

## Comandos de Optimización de Producción

```bash
# 1. Optimizar autoloader de Composer
composer install --optimize-autoloader --no-dev

# 2. Cachear configuración
php artisan config:cache

# 3. Cachear rutas
php artisan route:cache

# 4. Cachear vistas
php artisan view:cache

# 5. Cachear eventos
php artisan event:cache

# 6. Optimizar application
php artisan optimize

# 7. Storage link para archivos públicos
php artisan storage:link

# 8. Migrar base de datos
php artisan migrate --force

# 9. Iniciar queue workers
php artisan queue:work redis --sleep=3 --tries=3 --max-jobs=1000

# 10. Iniciar scheduler (cron)
* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
```

## Monitoreo Continuo

### Laravel Horizon para Queues
```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

### Laravel Telescope para Debugging (Solo desarrollo)
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

## Configuración de Servidor Web

### Nginx (Recomendado)
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name cms.santamarta.gov.co;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name cms.santamarta.gov.co;
    root /var/www/cms/public;

    # SSL
    ssl_certificate /etc/ssl/certs/cms.santamarta.gov.co.crt;
    ssl_certificate_key /etc/ssl/private/cms.santamarta.gov.co.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    
    # Performance
    client_max_body_size 100M;
    client_body_buffer_size 128k;
    
    # Gzip
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css text/xml text/javascript 
               application/x-javascript application/xml+rss 
               application/json application/javascript;
    
    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
    
    # Laravel
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
        fastcgi_read_timeout 300;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### PHP-FPM Optimization
```ini
; /etc/php/8.3/fpm/pool.d/www.conf
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.3-fpm.sock
listen.owner = www-data
listen.group = www-data

; Performance tuning
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500

; Memory
php_admin_value[memory_limit] = 256M
```

## Health Checks

### Database Connection
```php
// routes/web.php
Route::get('/health/db', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['status' => 'ok', 'database' => 'connected']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'database' => 'disconnected'], 500);
    }
});
```

### Redis Connection
```php
Route::get('/health/redis', function () {
    try {
        Redis::connection()->ping();
        return response()->json(['status' => 'ok', 'redis' => 'connected']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'redis' => 'disconnected'], 500);
    }
});
```

### Queue Status
```php
Route::get('/health/queue', function () {
    $failed = DB::table('trabajos_fallidos')->count();
    $pending = DB::table('trabajos')->count();
    
    return response()->json([
        'status' => 'ok',
        'failed_jobs' => $failed,
        'pending_jobs' => $pending
    ]);
});
```

## Backup Strategy

```bash
# Database backup diario
0 2 * * * /usr/bin/mysqldump -u cms_user -p cms_santamarta | gzip > /backups/db/cms_$(date +\%Y\%m\%d).sql.gz

# Storage backup semanal
0 3 * * 0 tar -czf /backups/storage/storage_$(date +\%Y\%m\%d).tar.gz /var/www/cms/storage

# Retener backups por 30 días
find /backups/db/ -name "*.sql.gz" -mtime +30 -delete
find /backups/storage/ -name "*.tar.gz" -mtime +30 -delete
```
