# Guía de Deployment - CMS Gubernamental Backend

> **Versión:** 1.0  
> **Entorno:** Ubuntu 24.04 LTS  
> **Servidor:** DigitalOcean Droplet  
> **Última actualización:** 17 de Febrero, 2026

---

## 📑 Tabla de Contenidos

1. [Requisitos del Servidor](#requisitos-del-servidor)
2. [Preparación del Servidor](#preparación-del-servidor)
3. [Instalación de Dependencias](#instalación-de-dependencias)
4. [Configuración de MySQL](#configuración-de-mysql)
5. [Configuración de Nginx](#configuración-de-nginx)
6. [Deployment de la Aplicación](#deployment-de-la-aplicación)
7. [Configuración SSL](#configuración-ssl)
8. [Configuración de Servicios](#configuración-de-servicios)
9. [Optimización](#optimización)
10. [Monitoreo](#monitoreo)
11. [Backup](#backup)
12. [Troubleshooting](#troubleshooting)

---

## 1. Requisitos del Servidor

### 1.1 Especificaciones Mínimas

```
Producción (Recomendado):
- CPU: 2 cores
- RAM: 4 GB
- Disco: 80 GB SSD
- Ancho de banda: 4 TB/mes

Desarrollo/Staging:
- CPU: 1 core
- RAM: 2 GB
- Disco: 40 GB SSD
- Ancho de banda: 2 TB/mes
```

### 1.2 Software Requerido

```
- Ubuntu 24.04 LTS
- PHP 8.3+
- MySQL 8.0+
- Nginx 1.24+
- Redis 7.0+
- Composer 2.6+
- Node.js 18+
- Git 2.40+
```

---

## 2. Preparación del Servidor

### 2.1 Acceso Inicial

```bash
# Conectar al servidor
ssh root@your-server-ip

# Actualizar sistema
apt update && apt upgrade -y

# Crear usuario para deployment
adduser deployer
usermod -aG sudo deployer

# Configurar SSH para deployer
su - deployer
mkdir ~/.ssh
chmod 700 ~/.ssh
```

### 2.2 Configuración de Firewall

```bash
# Habilitar UFW
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Verificar status
sudo ufw status
```

### 2.3 Configurar Timezone

```bash
# Configurar zona horaria Colombia
sudo timedatectl set-timezone America/Bogota

# Verificar
timedatectl
```

---

## 3. Instalación de Dependencias

### 3.1 PHP 8.3

```bash
# Agregar repositorio Ondřej PPA
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Instalar PHP 8.3 y extensiones
sudo apt install -y \
    php8.3 \
    php8.3-fpm \
    php8.3-cli \
    php8.3-common \
    php8.3-mysql \
    php8.3-zip \
    php8.3-gd \
    php8.3-mbstring \
    php8.3-curl \
    php8.3-xml \
    php8.3-bcmath \
    php8.3-redis \
    php8.3-intl

# Verificar instalación
php -v
```

### 3.2 Composer

```bash
# Descargar e instalar Composer
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Verificar
composer --version
```

### 3.3 Nginx

```bash
# Instalar Nginx
sudo apt install nginx -y

# Iniciar y habilitar
sudo systemctl start nginx
sudo systemctl enable nginx

# Verificar
sudo systemctl status nginx
```

### 3.4 MySQL

```bash
# Instalar MySQL
sudo apt install mysql-server -y

# Secure installation
sudo mysql_secure_installation

# Configurar
sudo mysql
```

### 3.5 Redis

```bash
# Instalar Redis
sudo apt install redis-server -y

# Configurar para producción
sudo nano /etc/redis/redis.conf
# Cambiar: supervised no → supervised systemd
# Cambiar: bind 127.0.0.1 ::1 (mantener solo localhost)

# Reiniciar
sudo systemctl restart redis
sudo systemctl enable redis

# Verificar
redis-cli ping
# Respuesta: PONG
```

### 3.6 Node.js (para asset compilation)

```bash
# Instalar Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Verificar
node --version
npm --version
```

---

## 4. Configuración de MySQL

### 4.1 Crear Base de Datos y Usuario

```sql
-- Conectar a MySQL
sudo mysql

-- Crear base de datos
CREATE DATABASE cms_gubernamental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario
CREATE USER 'cms_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';

-- Otorgar permisos
GRANT ALL PRIVILEGES ON cms_gubernamental.* TO 'cms_user'@'localhost';
FLUSH PRIVILEGES;

-- Verificar
SHOW DATABASES;
SELECT user, host FROM mysql.user;

-- Salir
EXIT;
```

### 4.2 Configuración de Seguridad

```bash
# Configurar MySQL para solo escuchar localhost
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf

# Asegurar que esté:
bind-address = 127.0.0.1

# Reiniciar MySQL
sudo systemctl restart mysql
```

---

## 5. Configuración de Nginx

### 5.1 Configuración del Server Block

```bash
# Crear archivo de configuración
sudo nano /etc/nginx/sites-available/cms-api
```

Contenido:

```nginx
server {
    listen 80;
    server_name api.alcaldia.gov.co;
    root /var/www/cms-gubernamental/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Logs
    access_log /var/log/nginx/cms-access.log;
    error_log /var/log/nginx/cms-error.log;
}
```

### 5.2 Habilitar Sitio

```bash
# Crear symlink
sudo ln -s /etc/nginx/sites-available/cms-api /etc/nginx/sites-enabled/

# Verificar configuración
sudo nginx -t

# Recargar Nginx
sudo systemctl reload nginx
```

---

## 6. Deployment de la Aplicación

### 6.1 Clonar Repositorio

```bash
# Crear directorio
sudo mkdir -p /var/www/cms-gubernamental
sudo chown deployer:deployer /var/www/cms-gubernamental

# Clonar
cd /var/www/cms-gubernamental
git clone https://github.com/SantanderAcuna/documentacion.git .

# Ir al backend
cd backend
```

### 6.2 Instalar Dependencias

```bash
# PHP dependencies
composer install --no-dev --optimize-autoloader

# Node dependencies (si es necesario)
npm install
npm run build
```

### 6.3 Configurar Environment

```bash
# Copiar .env
cp .env.example .env

# Editar configuración
nano .env
```

Configuración de `.env`:

```env
APP_NAME="CMS Gubernamental"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=America/Bogota
APP_URL=https://api.alcaldia.gov.co

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cms_gubernamental
DB_USERNAME=cms_user
DB_PASSWORD=STRONG_PASSWORD_HERE

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=admin.alcaldia.gov.co,www.alcaldia.gov.co
SESSION_DOMAIN=.alcaldia.gov.co
```

### 6.4 Generar APP_KEY

```bash
php artisan key:generate
```

### 6.5 Ejecutar Migraciones

```bash
# Ejecutar migraciones
php artisan migrate --force

# Seed de datos iniciales
php artisan db:seed --class=RolePermissionSeeder --force
php artisan db:seed --class=AdminUserSeeder --force
```

### 6.6 Configurar Permisos

```bash
# Storage y cache writable
sudo chown -R www-data:www-data /var/www/cms-gubernamental/backend/storage
sudo chown -R www-data:www-data /var/www/cms-gubernamental/backend/bootstrap/cache

# Permisos adecuados
sudo chmod -R 755 /var/www/cms-gubernamental/backend/storage
sudo chmod -R 755 /var/www/cms-gubernamental/backend/bootstrap/cache

# Link de storage
php artisan storage:link
```

### 6.7 Optimizar

```bash
# Cache de config
php artisan config:cache

# Cache de routes
php artisan route:cache

# Cache de views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

---

## 7. Configuración SSL

### 7.1 Certbot (Let's Encrypt)

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtener certificado
sudo certbot --nginx -d api.alcaldia.gov.co

# Verificar auto-renewal
sudo systemctl status certbot.timer
```

### 7.2 Nginx con SSL

El archivo se actualizará automáticamente:

```nginx
server {
    listen 443 ssl http2;
    server_name api.alcaldia.gov.co;
    
    ssl_certificate /etc/letsencrypt/live/api.alcaldia.gov.co/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.alcaldia.gov.co/privkey.pem;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # ... resto de configuración ...
}

server {
    listen 80;
    server_name api.alcaldia.gov.co;
    return 301 https://$server_name$request_uri;
}
```

---

## 8. Configuración de Servicios

### 8.1 Supervisor para Queue Worker

```bash
# Instalar Supervisor
sudo apt install supervisor -y

# Crear configuración
sudo nano /etc/supervisor/conf.d/cms-worker.conf
```

Contenido:

```ini
[program:cms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/cms-gubernamental/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/cms-gubernamental/backend/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Recargar Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cms-worker:*

# Verificar
sudo supervisorctl status
```

### 8.2 Cron para Scheduled Tasks

```bash
# Editar crontab
sudo crontab -e -u www-data

# Agregar línea:
* * * * * cd /var/www/cms-gubernamental/backend && php artisan schedule:run >> /dev/null 2>&1
```

---

## 9. Optimización

### 9.1 PHP-FPM

```bash
# Editar configuración
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

Optimizaciones:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

```bash
# Reiniciar PHP-FPM
sudo systemctl restart php8.3-fpm
```

### 9.2 OPcache

```bash
# Configurar
sudo nano /etc/php/8.3/fpm/conf.d/10-opcache.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

### 9.3 Redis

```bash
# Configurar
sudo nano /etc/redis/redis.conf
```

```
maxmemory 256mb
maxmemory-policy allkeys-lru
```

---

## 10. Monitoreo

### 10.1 Laravel Pulse (Recomendado)

```bash
# Instalar
composer require laravel/pulse

# Migrar
php artisan pulse:install
php artisan migrate

# Configurar
php artisan vendor:publish --tag=pulse-config
```

### 10.2 Logs

```bash
# Ver logs de Laravel
tail -f /var/www/cms-gubernamental/backend/storage/logs/laravel.log

# Ver logs de Nginx
sudo tail -f /var/log/nginx/cms-error.log
sudo tail -f /var/log/nginx/cms-access.log

# Ver logs de PHP-FPM
sudo tail -f /var/log/php8.3-fpm.log
```

---

## 11. Backup

### 11.1 Script de Backup

```bash
#!/bin/bash
# /usr/local/bin/cms-backup.sh

BACKUP_DIR="/backups/cms"
DATE=$(date +%Y%m%d_%H%M%S)

# Crear directorio
mkdir -p $BACKUP_DIR

# Backup de base de datos
mysqldump -u cms_user -p'PASSWORD' cms_gubernamental | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup de archivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/cms-gubernamental/backend/storage/app

# Eliminar backups antiguos (>7 días)
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

### 11.2 Automatizar Backup

```bash
# Hacer ejecutable
chmod +x /usr/local/bin/cms-backup.sh

# Agregar a cron (diario a las 2am)
sudo crontab -e
0 2 * * * /usr/local/bin/cms-backup.sh >> /var/log/cms-backup.log 2>&1
```

---

## 12. Troubleshooting

### 12.1 Problemas Comunes

#### 500 Internal Server Error
```bash
# Ver logs
sudo tail -f /var/log/nginx/cms-error.log
tail -f storage/logs/laravel.log

# Verificar permisos
sudo chown -R www-data:www-data storage bootstrap/cache
```

#### Database Connection Error
```bash
# Verificar MySQL
sudo systemctl status mysql

# Test connection
mysql -u cms_user -p cms_gubernamental

# Verificar .env
cat .env | grep DB_
```

#### Permission Denied en Storage
```bash
# Fix permisos
sudo chown -R www-data:www-data storage
sudo chmod -R 755 storage
```

---

**¡Deployment completado!** 🚀

*Última actualización: 17 de Febrero, 2026*
