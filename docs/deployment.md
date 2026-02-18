# Guía de Despliegue - CMS Gubernamental

**Fecha:** 2026-02-17  
**Versión:** 1.0.0  
**Entorno:** DigitalOcean Droplet - Ubuntu 24.04

---

## 📋 Requisitos

### Servidor
- **Proveedor:** DigitalOcean
- **OS:** Ubuntu 24.04 LTS
- **RAM:** Mínimo 4GB (recomendado 8GB)
- **CPU:** 2 vCPUs (recomendado 4 vCPUs)
- **Disco:** 80GB SSD

### Software
- Docker 24.x
- Docker Compose v2.x
- Nginx 1.24+
- Certbot (Let's Encrypt)
- Git

---

## 🚀 Instalación Inicial

### 1. Preparar el Servidor

```bash
# Conectar al servidor
ssh root@your-server-ip

# Actualizar sistema
apt update && apt upgrade -y

# Instalar dependencias
apt install -y git curl wget ufw

# Configurar firewall
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw enable
```

### 2. Instalar Docker

```bash
# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Instalar Docker Compose
apt install -y docker-compose-plugin

# Verificar instalación
docker --version
docker compose version
```

### 3. Clonar Repositorio

```bash
# Crear directorio de aplicación
mkdir -p /var/www
cd /var/www

# Clonar repositorio
git clone https://github.com/SantanderAcuna/documentacion.git cms-gubernamental
cd cms-gubernamental

# Crear usuario para la aplicación
useradd -m -s /bin/bash cmsapp
chown -R cmsapp:cmsapp /var/www/cms-gubernamental
```

### 4. Configurar Variables de Entorno

```bash
# Backend
cd /var/www/cms-gubernamental/backend
cp .env.example .env
nano .env

# Actualizar:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.alcaldia.gov.co

DB_HOST=mysql
DB_DATABASE=cms_production
DB_USERNAME=cms_user
DB_PASSWORD=STRONG_PASSWORD_HERE

SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=admin.alcaldia.gov.co,www.alcaldia.gov.co

# Frontend Admin
cd /var/www/cms-gubernamental/frontend-admin
cp .env.example .env
nano .env

# Actualizar:
VITE_API_URL=https://api.alcaldia.gov.co/api/v1
VITE_APP_ENV=production

# Frontend Public
cd /var/www/cms-gubernamental/frontend-public
cp .env.example .env
nano .env

# Actualizar:
VITE_API_URL=https://api.alcaldia.gov.co/api/v1
VITE_APP_ENV=production
VITE_SITE_URL=https://www.alcaldia.gov.co
```

---

## 🔒 Configurar SSL/TLS

### 1. Instalar Certbot

```bash
apt install -y certbot python3-certbot-nginx
```

### 2. Obtener Certificados

```bash
# Para API
certbot certonly --nginx -d api.alcaldia.gov.co

# Para Admin
certbot certonly --nginx -d admin.alcaldia.gov.co

# Para Sitio Público
certbot certonly --nginx -d www.alcaldia.gov.co -d alcaldia.gov.co

# Certificados se guardan en:
# /etc/letsencrypt/live/api.alcaldia.gov.co/
# /etc/letsencrypt/live/admin.alcaldia.gov.co/
# /etc/letsencrypt/live/www.alcaldia.gov.co/
```

### 3. Configurar Auto-Renovación

```bash
# Test de renovación
certbot renew --dry-run

# Crontab ya configurado automáticamente
```

---

## 🐳 Desplegar con Docker

### 1. Build de Imágenes

```bash
cd /var/www/cms-gubernamental

# Build backend
docker compose build backend

# Build frontends
docker compose build frontend-admin
docker compose build frontend-public
```

### 2. Iniciar Servicios

```bash
# Iniciar todos los servicios
docker compose up -d

# Verificar estado
docker compose ps

# Ver logs
docker compose logs -f
```

### 3. Configurar Backend

```bash
# Instalar dependencias
docker compose exec backend composer install --optimize-autoloader --no-dev

# Generar APP_KEY
docker compose exec backend php artisan key:generate

# Ejecutar migraciones
docker compose exec backend php artisan migrate --force

# Ejecutar seeders
docker compose exec backend php artisan db:seed --force

# Crear usuario administrador
docker compose exec backend php artisan tinker
>>> $user = User::create(['name' => 'Admin', 'email' => 'admin@alcaldia.gov.co', 'password' => Hash::make('STRONG_PASSWORD')]);
>>> $user->assignRole('super-admin');

# Optimizar Laravel
docker compose exec backend php artisan optimize
docker compose exec backend php artisan config:cache
docker compose exec backend php artisan route:cache
docker compose exec backend php artisan view:cache
```

### 4. Build de Frontends

```bash
# Frontend Admin
docker compose exec frontend-admin npm ci
docker compose exec frontend-admin npm run build

# Frontend Public
docker compose exec frontend-public npm ci
docker compose exec frontend-public npm run build
```

---

## 🌐 Configurar Nginx

### 1. Configuración de API

```nginx
# /etc/nginx/sites-available/api.alcaldia.gov.co

server {
    listen 80;
    server_name api.alcaldia.gov.co;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.alcaldia.gov.co;

    ssl_certificate /etc/letsencrypt/live/api.alcaldia.gov.co/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.alcaldia.gov.co/privkey.pem;
    
    # SSL Configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### 2. Configuración de Admin

```nginx
# /etc/nginx/sites-available/admin.alcaldia.gov.co

server {
    listen 80;
    server_name admin.alcaldia.gov.co;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name admin.alcaldia.gov.co;
    
    root /var/www/cms-gubernamental/frontend-admin/dist;
    index index.html;

    ssl_certificate /etc/letsencrypt/live/admin.alcaldia.gov.co/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/admin.alcaldia.gov.co/privkey.pem;
    
    # SSL Configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 3. Configuración de Sitio Público

```nginx
# /etc/nginx/sites-available/www.alcaldia.gov.co

server {
    listen 80;
    server_name www.alcaldia.gov.co alcaldia.gov.co;
    return 301 https://www.alcaldia.gov.co$request_uri;
}

server {
    listen 443 ssl http2;
    server_name www.alcaldia.gov.co alcaldia.gov.co;
    
    root /var/www/cms-gubernamental/frontend-public/dist;
    index index.html;

    ssl_certificate /etc/letsencrypt/live/www.alcaldia.gov.co/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/www.alcaldia.gov.co/privkey.pem;
    
    # SSL Configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    
    # Gzip
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss application/rss+xml font/truetype font/opentype application/vnd.ms-fontobject image/svg+xml;
    
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 4. Activar Sitios

```bash
# Crear enlaces simbólicos
ln -s /etc/nginx/sites-available/api.alcaldia.gov.co /etc/nginx/sites-enabled/
ln -s /etc/nginx/sites-available/admin.alcaldia.gov.co /etc/nginx/sites-enabled/
ln -s /etc/nginx/sites-available/www.alcaldia.gov.co /etc/nginx/sites-enabled/

# Verificar configuración
nginx -t

# Recargar Nginx
systemctl reload nginx
```

---

## 🔄 Actualizaciones

### Desplegar Nueva Versión

```bash
cd /var/www/cms-gubernamental

# Pull cambios
git pull origin main

# Backend
docker compose exec backend composer install --optimize-autoloader --no-dev
docker compose exec backend php artisan migrate --force
docker compose exec backend php artisan optimize

# Frontend Admin
docker compose exec frontend-admin npm ci
docker compose exec frontend-admin npm run build

# Frontend Public
docker compose exec frontend-public npm ci
docker compose exec frontend-public npm run build

# Reiniciar servicios
docker compose restart
```

---

## 🔍 Monitoreo

### Logs

```bash
# Ver todos los logs
docker compose logs -f

# Logs específicos
docker compose logs -f backend
docker compose logs -f mysql

# Logs de Nginx
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

### Estado del Sistema

```bash
# Docker
docker compose ps
docker stats

# Servidor
htop
df -h
free -m
```

---

## 🛡️ Backup

### Script de Backup

```bash
#!/bin/bash
# /var/www/cms-gubernamental/backup.sh

BACKUP_DIR="/var/backups/cms"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup base de datos
docker compose exec -T mysql mysqldump -u cms_user -pcms_password cms_production > $BACKUP_DIR/db_$DATE.sql

# Backup archivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/cms-gubernamental/backend/storage

# Eliminar backups antiguos (>7 días)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

### Cron para Backup Diario

```bash
# crontab -e
0 2 * * * /var/www/cms-gubernamental/backup.sh
```

---

## 📊 Performance

### Optimizaciones

```bash
# Backend
docker compose exec backend php artisan optimize
docker compose exec backend php artisan queue:work &

# Configurar Supervisor para workers
apt install -y supervisor
```

---

## ✅ Checklist de Despliegue

- [ ] Servidor creado y actualizado
- [ ] Docker instalado
- [ ] Repositorio clonado
- [ ] Variables de entorno configuradas
- [ ] Certificados SSL obtenidos
- [ ] Servicios Docker iniciados
- [ ] Base de datos migrada
- [ ] Usuario administrador creado
- [ ] Frontends construidos
- [ ] Nginx configurado
- [ ] Backup configurado
- [ ] Monitoreo activo
- [ ] Tests de accesibilidad pasados
- [ ] Tests de seguridad pasados

---

**Última actualización:** 2026-02-17
