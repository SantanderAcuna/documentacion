# Guía de Despliegue - Portal de Configuración VPS

## Índice

1. [Requisitos Previos](#requisitos-previos)
2. [Opción 1: DigitalOcean App Platform](#opción-1-digitalocean-app-platform)
3. [Opción 2: Droplet Ubuntu 24.04](#opción-2-droplet-ubuntu-2404)
4. [Configuración de Base de Datos](#configuración-de-base-de-datos)
5. [Configuración de Redis](#configuración-de-redis)
6. [Configuración de Almacenamiento](#configuración-de-almacenamiento)
7. [CI/CD con GitHub Actions](#cicd-con-github-actions)
8. [Monitoreo y Logs](#monitoreo-y-logs)
9. [Troubleshooting](#troubleshooting)

---

## Requisitos Previos

### Cuenta de DigitalOcean
- Cuenta activa en DigitalOcean
- Método de pago configurado
- Acceso a API tokens (para automatización)

### Cuenta de GitHub
- Repositorio privado o público
- GitHub Actions habilitado
- Secrets configurados para deployment

### Dominio (Opcional pero Recomendado)
- Dominio registrado
- Acceso a DNS para configurar registros A/CNAME

### Herramientas Locales
```bash
# Instalar doctl (CLI de DigitalOcean)
cd ~
wget https://github.com/digitalocean/doctl/releases/download/v1.94.0/doctl-1.94.0-linux-amd64.tar.gz
tar xf doctl-1.94.0-linux-amd64.tar.gz
sudo mv doctl /usr/local/bin

# Autenticar doctl
doctl auth init
```

---

## Opción 1: DigitalOcean App Platform

### Ventajas
- ✅ Deploy automático desde Git
- ✅ SSL automático
- ✅ Escalado automático
- ✅ Zero-downtime deployments
- ✅ Menos configuración manual

### Desventajas
- ❌ Menos control sobre infraestructura
- ❌ Costo ligeramente mayor

### Paso 1: Crear App Spec

Crea `deployment/digitalocean/app.yaml`:

```yaml
name: vps-portal
region: nyc
services:
  # Backend Laravel
  - name: api
    github:
      repo: SantanderAcuna/documentacion
      branch: main
      deploy_on_push: true
    source_dir: /backend
    build_command: |
      composer install --no-dev --optimize-autoloader
      php artisan config:cache
      php artisan route:cache
      php artisan view:cache
    run_command: |
      php artisan migrate --force
      php artisan serve --host=0.0.0.0 --port=8080
    envs:
      - key: APP_NAME
        value: "Portal VPS"
      - key: APP_ENV
        value: production
      - key: APP_KEY
        scope: RUN_AND_BUILD_TIME
        type: SECRET
      - key: APP_DEBUG
        value: "false"
      - key: APP_URL
        value: ${api.PUBLIC_URL}
      - key: DB_CONNECTION
        value: mysql
      - key: DB_HOST
        value: ${mysql.HOSTNAME}
      - key: DB_PORT
        value: ${mysql.PORT}
      - key: DB_DATABASE
        value: ${mysql.DATABASE}
      - key: DB_USERNAME
        value: ${mysql.USERNAME}
      - key: DB_PASSWORD
        value: ${mysql.PASSWORD}
        type: SECRET
      - key: REDIS_HOST
        value: ${redis.HOSTNAME}
      - key: REDIS_PASSWORD
        value: ${redis.PASSWORD}
        type: SECRET
      - key: REDIS_PORT
        value: ${redis.PORT}
      - key: FILESYSTEM_DISK
        value: spaces
      - key: DO_SPACES_KEY
        scope: RUN_AND_BUILD_TIME
        type: SECRET
      - key: DO_SPACES_SECRET
        scope: RUN_AND_BUILD_TIME
        type: SECRET
      - key: DO_SPACES_ENDPOINT
        value: https://nyc3.digitaloceanspaces.com
      - key: DO_SPACES_REGION
        value: nyc3
      - key: DO_SPACES_BUCKET
        value: vps-portal
    http_port: 8080
    instance_count: 1
    instance_size_slug: basic-xxs
    
  # Frontend Vue.js
  - name: web
    github:
      repo: SantanderAcuna/documentacion
      branch: main
      deploy_on_push: true
    source_dir: /frontend
    build_command: |
      npm ci
      npm run build
    run_command: |
      npx serve -s dist -l 8080
    envs:
      - key: VITE_API_URL
        value: ${api.PUBLIC_URL}
    http_port: 8080
    instance_count: 1
    instance_size_slug: basic-xxs
    routes:
      - path: /
        
  # Queue Worker
  - name: worker
    github:
      repo: SantanderAcuna/documentacion
      branch: main
      deploy_on_push: true
    source_dir: /backend
    build_command: |
      composer install --no-dev --optimize-autoloader
    run_command: |
      php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
    envs:
      # (mismas env vars que api)
    instance_count: 1
    instance_size_slug: basic-xxs

databases:
  - name: mysql
    engine: MYSQL
    version: "8"
    size: db-s-1vcpu-1gb
    num_nodes: 1
    
  - name: redis
    engine: REDIS
    version: "7"
    size: db-s-1vcpu-1gb
    num_nodes: 1
```

### Paso 2: Deploy con doctl

```bash
# Crear app desde spec
doctl apps create --spec deployment/digitalocean/app.yaml

# O actualizar app existente
doctl apps update YOUR_APP_ID --spec deployment/digitalocean/app.yaml

# Ver status del deployment
doctl apps list
doctl apps get YOUR_APP_ID

# Ver logs
doctl apps logs YOUR_APP_ID --type build
doctl apps logs YOUR_APP_ID --type run
```

### Paso 3: Configurar Dominio

```bash
# En la consola de DigitalOcean o via doctl
doctl apps update-domain YOUR_APP_ID --domain yourdomain.com
```

Luego agrega registro CNAME en tu DNS:
```
CNAME  www  your-app.ondigitalocean.app.
```

### Paso 4: Configurar Spaces

```bash
# Crear Space
doctl compute droplet create vps-portal \
  --region nyc3 \
  --size s-1vcpu-1gb \
  --image ubuntu-24-04-x64

# Configurar CORS
cat > cors.json << 'EOF'
{
  "CORSRules": [
    {
      "AllowedOrigins": ["*"],
      "AllowedMethods": ["GET", "PUT", "POST"],
      "AllowedHeaders": ["*"],
      "MaxAgeSeconds": 3000
    }
  ]
}
EOF

# Aplicar CORS
doctl compute cdn create --origin vps-portal.nyc3.digitaloceanspaces.com
```

---

## Opción 2: Droplet Ubuntu 24.04

### Ventajas
- ✅ Control total sobre infraestructura
- ✅ Costo potencialmente menor
- ✅ Más flexible para customización

### Paso 1: Crear Droplet

```bash
# Generar SSH key si no tienes
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"

# Agregar SSH key a DigitalOcean
doctl compute ssh-key import vps-portal-key --public-key-file ~/.ssh/id_rsa.pub

# Crear droplet
doctl compute droplet create vps-portal \
  --region nyc3 \
  --size s-2vcpu-2gb \
  --image ubuntu-24-04-x64 \
  --ssh-keys $(doctl compute ssh-key list --format ID --no-header) \
  --enable-monitoring \
  --enable-ipv6 \
  --tag-names web,production

# Obtener IP
doctl compute droplet list
```

### Paso 2: Configuración Inicial del Servidor

```bash
# Conectar al droplet
ssh root@YOUR_DROPLET_IP

# Actualizar sistema
apt update && apt upgrade -y

# Configurar timezone
timedatectl set-timezone America/New_York

# Crear usuario deploy
adduser deploy
usermod -aG sudo deploy
mkdir -p /home/deploy/.ssh
cp ~/.ssh/authorized_keys /home/deploy/.ssh/
chown -R deploy:deploy /home/deploy/.ssh
chmod 700 /home/deploy/.ssh
chmod 600 /home/deploy/.ssh/authorized_keys

# Configurar firewall
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable

# Instalar fail2ban
apt install -y fail2ban
systemctl enable fail2ban
systemctl start fail2ban
```

### Paso 3: Instalar Stack LEMP

```bash
# Nginx
apt install -y nginx
systemctl enable nginx
systemctl start nginx

# MySQL (usar Managed Database en producción es mejor)
apt install -y mysql-server
mysql_secure_installation

# PHP 8.3
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-bcmath php8.3-curl php8.3-gd \
  php8.3-zip php8.3-redis php8.3-intl

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Redis
apt install -y redis-server
systemctl enable redis-server
systemctl start redis-server

# Supervisor (para queue workers)
apt install -y supervisor
systemctl enable supervisor
systemctl start supervisor
```

### Paso 4: Configurar Nginx

```bash
# Crear directorio del proyecto
mkdir -p /var/www/vps-portal
chown -R deploy:deploy /var/www/vps-portal

# Configuración de Nginx
cat > /etc/nginx/sites-available/vps-portal << 'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    # SSL certificates (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # Logs
    access_log /var/log/nginx/vps-portal-access.log;
    error_log /var/log/nginx/vps-portal-error.log;
    
    # Frontend (Vue.js SPA)
    root /var/www/vps-portal/frontend/dist;
    index index.html;
    
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # Backend API
    location /api {
        alias /var/www/vps-portal/backend/public;
        try_files $uri $uri/ @api;
        
        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
    }
    
    location @api {
        rewrite /api/(.*)$ /api/index.php?/$1 last;
    }
    
    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Deny access to hidden files
    location ~ /\.(?!well-known) {
        deny all;
    }
}
EOF

# Habilitar sitio
ln -s /etc/nginx/sites-available/vps-portal /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default

# Test configuración
nginx -t

# Reload Nginx
systemctl reload nginx
```

### Paso 5: Configurar SSL con Let's Encrypt

```bash
# Instalar Certbot
apt install -y certbot python3-certbot-nginx

# Obtener certificado
certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renovación (ya está configurada)
# Verificar con: certbot renew --dry-run
```

### Paso 6: Clonar y Configurar Proyecto

```bash
# Como usuario deploy
su - deploy
cd /var/www/vps-portal

# Clonar repositorio
git clone https://github.com/SantanderAcuna/documentacion.git .

# Backend
cd backend
composer install --no-dev --optimize-autoloader
cp .env.example .env

# Editar .env
nano .env
# Configurar:
# - APP_KEY (generar con: php artisan key:generate)
# - DB_* (credenciales de MySQL)
# - REDIS_HOST, REDIS_PORT
# - DO_SPACES_* (configuración de Spaces)

# Permisos
chown -R deploy:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Migraciones
php artisan migrate --force
php artisan db:seed --force

# Optimizaciones
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Frontend
cd ../frontend
npm ci
npm run build
```

### Paso 7: Configurar Queue Workers

```bash
# Crear configuración de Supervisor
cat > /etc/supervisor/conf.d/vps-portal-worker.conf << 'EOF'
[program:vps-portal-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/vps-portal/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 --timeout=300
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/vps-portal/backend/storage/logs/worker.log
stopwaitsecs=3600
startsecs=0
EOF

# Recargar Supervisor
supervisorctl reread
supervisorctl update
supervisorctl start vps-portal-worker:*
supervisorctl status
```

### Paso 8: Configurar Cron para Scheduler

```bash
# Editar crontab del usuario deploy
crontab -e

# Agregar línea:
* * * * * cd /var/www/vps-portal/backend && php artisan schedule:run >> /dev/null 2>&1
```

---

## Configuración de Base de Datos

### Opción A: Managed MySQL (Recomendado)

```bash
# Crear database cluster
doctl databases create vps-portal-db \
  --engine mysql \
  --version 8 \
  --region nyc3 \
  --size db-s-1vcpu-1gb \
  --num-nodes 1

# Obtener credenciales
doctl databases connection vps-portal-db

# Crear database
doctl databases db create vps-portal-db vps_portal_production

# Configurar firewall (agregar droplet IP)
doctl databases firewalls append vps-portal-db \
  --rule type:droplet,value:YOUR_DROPLET_ID
```

### Opción B: MySQL en Droplet

```bash
# Conectar a MySQL
mysql -u root -p

# Crear database y usuario
CREATE DATABASE vps_portal_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'vps_portal'@'localhost' IDENTIFIED BY 'SECURE_PASSWORD';
GRANT ALL PRIVILEGES ON vps_portal_production.* TO 'vps_portal'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Configurar para performance
nano /etc/mysql/mysql.conf.d/mysqld.cnf

# Agregar:
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_file_per_table = 1
max_connections = 200

# Reiniciar MySQL
systemctl restart mysql
```

---

## Configuración de Redis

### Opción A: Managed Redis (Recomendado)

```bash
# Crear Redis cluster
doctl databases create vps-portal-redis \
  --engine redis \
  --version 7 \
  --region nyc3 \
  --size db-s-1vcpu-1gb \
  --num-nodes 1

# Obtener conexión
doctl databases connection vps-portal-redis

# Configurar firewall
doctl databases firewalls append vps-portal-redis \
  --rule type:droplet,value:YOUR_DROPLET_ID
```

### Opción B: Redis en Droplet

```bash
# Configurar Redis
nano /etc/redis/redis.conf

# Modificar:
bind 127.0.0.1
maxmemory 512mb
maxmemory-policy allkeys-lru
requirepass YOUR_REDIS_PASSWORD

# Reiniciar Redis
systemctl restart redis-server

# Test
redis-cli
> AUTH YOUR_REDIS_PASSWORD
> PING
```

---

## Configuración de Almacenamiento (Spaces)

```bash
# Crear Space
doctl compute space create vps-portal --region nyc3

# Generar API keys
# En la consola web: API > Spaces Keys > Generate New Key

# Configurar en .env
DO_SPACES_KEY=YOUR_KEY
DO_SPACES_SECRET=YOUR_SECRET
DO_SPACES_ENDPOINT=https://nyc3.digitaloceanspaces.com
DO_SPACES_REGION=nyc3
DO_SPACES_BUCKET=vps-portal

# Habilitar CDN
doctl compute cdn create --origin vps-portal.nyc3.digitaloceanspaces.com

# Configurar CORS (via s3cmd o web console)
```

---

## CI/CD con GitHub Actions

### Workflow de Deploy

Crea `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [main]
  workflow_dispatch:

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          
      - name: Install Backend Dependencies
        working-directory: ./backend
        run: composer install --prefer-dist --no-progress
        
      - name: Run Backend Tests
        working-directory: ./backend
        run: php artisan test
        
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '20'
          
      - name: Install Frontend Dependencies
        working-directory: ./frontend
        run: npm ci
        
      - name: Run Frontend Tests
        working-directory: ./frontend
        run: npm run test:unit
        
  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Deploy to DigitalOcean
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.DO_HOST }}
          username: ${{ secrets.DO_USERNAME }}
          key: ${{ secrets.DO_SSH_KEY }}
          script: |
            cd /var/www/vps-portal
            
            # Pull latest code
            git pull origin main
            
            # Backend
            cd backend
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            
            # Frontend
            cd ../frontend
            npm ci
            npm run build
            
            # Restart services
            sudo supervisorctl restart vps-portal-worker:*
            sudo systemctl reload php8.3-fpm
            sudo systemctl reload nginx
            
      - name: Verify Deployment
        run: |
          sleep 10
          curl -f https://yourdomain.com/api/health || exit 1
```

### Configurar Secrets en GitHub

```
Settings > Secrets and variables > Actions > New repository secret

Agregar:
- DO_HOST: IP del droplet
- DO_USERNAME: deploy
- DO_SSH_KEY: contenido de ~/.ssh/id_rsa (private key)
```

---

## Monitoreo y Logs

### Laravel Pulse

```bash
# Instalar Laravel Pulse
cd /var/www/vps-portal/backend
composer require laravel/pulse

# Publicar assets
php artisan vendor:publish --tag=pulse-config
php artisan vendor:publish --tag=pulse-migrations

# Migrar
php artisan migrate

# Acceder a: https://yourdomain.com/pulse
```

### DigitalOcean Monitoring

```bash
# Habilitar monitoring en droplet
doctl compute droplet update YOUR_DROPLET_ID --enable-monitoring

# Ver métricas
doctl monitoring metrics list

# Configurar alertas (via web console)
```

### Logs Centralizados

```bash
# Instalar Filebeat (opcional)
curl -L -O https://artifacts.elastic.co/downloads/beats/filebeat/filebeat-8.11.0-amd64.deb
sudo dpkg -i filebeat-8.11.0-amd64.deb

# Configurar para enviar a ELK stack o similar
```

---

## Troubleshooting

### Error: 502 Bad Gateway

```bash
# Verificar PHP-FPM
systemctl status php8.3-fpm
tail -f /var/log/php8.3-fpm.log

# Verificar Nginx
nginx -t
systemctl status nginx
tail -f /var/log/nginx/error.log

# Verificar permisos
ls -la /var/www/vps-portal
```

### Error: Database Connection Failed

```bash
# Test MySQL connection
mysql -h HOST -u USER -p DATABASE

# Verificar firewall en Managed DB
doctl databases firewalls list DATABASE_ID

# Test desde Laravel
cd /var/www/vps-portal/backend
php artisan tinker
> DB::connection()->getPdo();
```

### Error: Queue Workers No Procesan

```bash
# Ver status de Supervisor
supervisorctl status

# Ver logs de workers
tail -f /var/www/vps-portal/backend/storage/logs/worker.log

# Reiniciar workers
supervisorctl restart vps-portal-worker:*

# Ver jobs en Redis
redis-cli
> LLEN queues:default
```

### Performance Lenta

```bash
# Verificar CPU/RAM
htop

# Verificar slow queries MySQL
mysql -e "SHOW VARIABLES LIKE 'slow_query_log%';"

# Verificar cache
redis-cli
> INFO stats

# Optimizar Laravel
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Backups Automatizados

```bash
# Script de backup
cat > /usr/local/bin/backup-vps-portal.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/vps-portal"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u USER -pPASSWORD vps_portal_production | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/vps-portal/backend/storage

# Upload to Spaces
s3cmd put $BACKUP_DIR/db_$DATE.sql.gz s3://vps-portal/backups/
s3cmd put $BACKUP_DIR/files_$DATE.tar.gz s3://vps-portal/backups/

# Cleanup old backups (keep last 7 days)
find $BACKUP_DIR -type f -mtime +7 -delete
EOF

chmod +x /usr/local/bin/backup-vps-portal.sh

# Cron daily backup
echo "0 2 * * * /usr/local/bin/backup-vps-portal.sh" | crontab -
```

---

## Checklist de Post-Deployment

- [ ] SSL habilitado y funcionando
- [ ] Firewall configurado correctamente
- [ ] Backups automatizados funcionando
- [ ] Monitoring habilitado
- [ ] Logs rotando correctamente
- [ ] Queue workers activos
- [ ] Scheduler funcionando
- [ ] Dominio apuntando correctamente
- [ ] Email configurado (si aplica)
- [ ] Tests pasando en producción
- [ ] Performance aceptable (< 200ms API)

---

**Última actualización:** 2026-02-17  
**Versión:** 1.0
