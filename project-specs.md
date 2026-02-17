# Especificaciones del Proyecto - Portal de Configuración VPS

## 1. Visión General del Proyecto

### 1.1 Descripción
Portal web profesional para la documentación, configuración y gestión de servidores VPS (Virtual Private Server). El sistema proporciona guías paso a paso, automatización de tareas comunes, monitoreo de servidores y gestión de usuarios con diferentes niveles de acceso.

### 1.2 Objetivos
- Centralizar la documentación de configuración de VPS
- Automatizar tareas repetitivas de administración
- Facilitar el onboarding de nuevos administradores
- Monitorear el estado de servidores en tiempo real
- Gestionar permisos y accesos de forma segura

### 1.3 Alcance
El proyecto incluye un portal web interactivo con documentación técnica, sistema de gestión de usuarios, panel de administración, y APIs para automatización de tareas en servidores VPS.

## 2. Arquitectura del Sistema

### 2.1 Arquitectura General
```
┌─────────────────────────────────────────────────────────────┐
│                     FRONTEND (SPA)                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   React.js   │  │  Bootstrap   │  │    Redux     │     │
│  │  TypeScript  │  │     Icons    │  │   Toolkit    │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ HTTPS/REST API
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    API GATEWAY (NGINX)                      │
│                    Load Balancer + SSL                      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    BACKEND (Node.js)                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │  Express.js  │  │  Passport    │  │    JWT       │     │
│  │  TypeScript  │  │     Auth     │  │  Sessions    │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   SSH2 Lib   │  │   PM2 Mgmt   │  │  WebSockets  │     │
│  │   for VPS    │  │   Process    │  │   Real-time  │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                  DATABASE LAYER                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │  PostgreSQL  │  │    Redis     │  │   MongoDB    │     │
│  │  (Principal) │  │   (Cache)    │  │    (Logs)    │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                  EXTERNAL SERVICES                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │     VPS      │  │    GitHub    │  │   Email      │     │
│  │   Servers    │  │   (CI/CD)    │  │   (SMTP)     │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Stack Tecnológico

#### Frontend
- **Framework:** React 18.x con TypeScript
- **UI Framework:** Bootstrap 5.3+
- **State Management:** Redux Toolkit
- **Routing:** React Router v6
- **HTTP Client:** Axios
- **Real-time:** Socket.io Client
- **Form Handling:** Formik + Yup
- **Code Highlighting:** Prism.js
- **Icons:** Bootstrap Icons + Font Awesome

#### Backend
- **Runtime:** Node.js 18.x LTS
- **Framework:** Express.js 4.x
- **Language:** TypeScript 5.x
- **Authentication:** Passport.js + JWT
- **SSH Management:** ssh2 library
- **Process Management:** PM2
- **Real-time:** Socket.io
- **Validation:** Joi
- **ORM:** Prisma (PostgreSQL)
- **API Documentation:** Swagger/OpenAPI

#### Database
- **Principal:** PostgreSQL 15+ (datos estructurados)
- **Cache:** Redis 7+ (sesiones, cache)
- **Logs:** MongoDB 6+ (logs de operaciones)

#### DevOps & Infrastructure
- **Web Server:** NGINX (reverse proxy + load balancer)
- **SSL:** Let's Encrypt (certbot)
- **Containerization:** Docker + Docker Compose
- **CI/CD:** GitHub Actions
- **Monitoring:** Prometheus + Grafana
- **Logging:** Winston + ELK Stack (opcional)
- **VPS Provider:** DigitalOcean / Linode / AWS

### 2.3 Capas de la Aplicación

#### Capa de Presentación
- Interfaz de usuario responsive
- Dashboard interactivo
- Documentación técnica navegable
- Terminal web integrada

#### Capa de Lógica de Negocio
- Gestión de usuarios y permisos
- Autenticación y autorización
- Ejecución de comandos SSH
- Validación de operaciones
- Auditoría de acciones

#### Capa de Datos
- Persistencia de usuarios y configuraciones
- Cache de datos frecuentes
- Almacenamiento de logs
- Histórico de operaciones

#### Capa de Integración
- Conexión SSH a VPS
- APIs REST
- WebSockets para tiempo real
- Notificaciones por email

## 3. Requisitos Funcionales (RF)

### RF-01: Gestión de Usuarios
**Descripción:** El sistema debe permitir crear, editar, eliminar y listar usuarios con diferentes roles.

**Criterios de Aceptación:**
- Los usuarios pueden registrarse con email y contraseña
- Los administradores pueden crear usuarios manualmente
- Se pueden asignar roles: Super Admin, Admin, Editor, Viewer
- Los usuarios pueden actualizar su perfil
- Los administradores pueden desactivar usuarios

**Prioridad:** ALTA

### RF-02: Autenticación y Autorización
**Descripción:** El sistema debe implementar autenticación segura con JWT y control de acceso basado en roles (RBAC).

**Criterios de Aceptación:**
- Login con email/contraseña
- Tokens JWT con expiración (24h)
- Refresh tokens para renovación
- Logout y revocación de tokens
- Verificación de permisos por ruta
- Autenticación de dos factores (2FA) opcional

**Prioridad:** CRÍTICA

### RF-03: Documentación Interactiva
**Descripción:** El sistema debe mostrar documentación técnica estructurada con búsqueda y navegación.

**Criterios de Aceptación:**
- Sidebar con índice navegable
- Búsqueda por palabras clave
- Código con resaltado de sintaxis
- Botones de copiar código
- Marcadores y favoritos
- Exportación a PDF

**Prioridad:** ALTA

### RF-04: Gestión de Servidores VPS
**Descripción:** El sistema debe permitir agregar, configurar y monitorear servidores VPS.

**Criterios de Aceptación:**
- Registro de servidores (IP, puerto, credenciales SSH)
- Prueba de conexión SSH
- Visualización de estado (online/offline)
- Agrupación por tags
- Almacenamiento seguro de credenciales

**Prioridad:** ALTA

### RF-05: Ejecución de Comandos SSH
**Descripción:** El sistema debe permitir ejecutar comandos en servidores remotos vía SSH.

**Criterios de Aceptación:**
- Terminal web interactiva
- Ejecución de comandos predefinidos
- Scripts automatizados
- Histórico de comandos ejecutados
- Logs de salida en tiempo real
- Control de permisos por comando

**Prioridad:** MEDIA

### RF-06: Plantillas de Configuración
**Descripción:** El sistema debe proporcionar plantillas de configuración reutilizables.

**Criterios de Aceptación:**
- Crear plantillas de scripts
- Editar plantillas existentes
- Aplicar plantillas a servidores
- Plantillas parametrizables
- Biblioteca de plantillas comunes

**Prioridad:** MEDIA

### RF-07: Monitoreo en Tiempo Real
**Descripción:** El sistema debe mostrar métricas de servidores en tiempo real.

**Criterios de Aceptación:**
- CPU usage
- Memoria RAM
- Almacenamiento en disco
- Tráfico de red
- Procesos activos
- Alertas por umbrales

**Prioridad:** MEDIA

### RF-08: Gestión de Llaves SSH
**Descripción:** El sistema debe facilitar la generación y gestión de llaves SSH.

**Criterios de Aceptación:**
- Generar pares de llaves SSH
- Almacenar llaves de forma segura
- Distribuir llaves públicas a servidores
- Revocar llaves comprometidas
- Rotación de llaves programada

**Prioridad:** ALTA

### RF-09: Auditoría y Logs
**Descripción:** El sistema debe registrar todas las operaciones críticas.

**Criterios de Aceptación:**
- Log de logins/logouts
- Log de comandos ejecutados
- Log de cambios de configuración
- Filtrado por usuario, fecha, acción
- Exportación de logs
- Retención por 90 días mínimo

**Prioridad:** ALTA

### RF-10: Notificaciones
**Descripción:** El sistema debe notificar eventos importantes a los usuarios.

**Criterios de Aceptación:**
- Notificaciones in-app (WebSocket)
- Notificaciones por email
- Configuración de preferencias de notificación
- Alertas de servidor caído
- Alertas de operaciones críticas
- Historial de notificaciones

**Prioridad:** MEDIA

## 4. Requisitos No Funcionales (RNF)

### RNF-01: Seguridad
**Descripción:** El sistema debe implementar las mejores prácticas de seguridad.

**Especificaciones:**
- Todas las comunicaciones mediante HTTPS/TLS 1.3
- Contraseñas hasheadas con bcrypt (salt rounds: 12)
- Protección contra SQL Injection (ORM parametrizado)
- Protección contra XSS (sanitización de inputs)
- Protección contra CSRF (tokens)
- Rate limiting (100 req/min por IP)
- Headers de seguridad (helmet.js)
- Credenciales SSH encriptadas en DB (AES-256)

**Prioridad:** CRÍTICA

### RNF-02: Rendimiento
**Descripción:** El sistema debe responder rápidamente bajo carga normal.

**Especificaciones:**
- Tiempo de respuesta API: < 500ms (p95)
- Tiempo de carga de página: < 2s
- Conexión SSH: < 3s
- Soporte de 100 usuarios concurrentes
- Cache Redis para queries frecuentes
- Compresión gzip/brotli
- Lazy loading de componentes

**Prioridad:** ALTA

### RNF-03: Escalabilidad
**Descripción:** El sistema debe poder escalar horizontalmente.

**Especificaciones:**
- Arquitectura stateless (backend)
- Sesiones en Redis (compartidas)
- Posibilidad de múltiples instancias backend
- Load balancer (NGINX)
- Base de datos con replicación
- CDN para assets estáticos

**Prioridad:** MEDIA

### RNF-04: Disponibilidad
**Descripción:** El sistema debe estar disponible la mayor parte del tiempo.

**Especificaciones:**
- Uptime objetivo: 99.5% (SLA)
- Backup automático diario (base de datos)
- Recuperación ante desastres (RTO: 4h, RPO: 24h)
- Monitoreo proactivo
- Health checks automáticos
- Failover automático (si multi-instancia)

**Prioridad:** ALTA

### RNF-05: Usabilidad
**Descripción:** El sistema debe ser intuitivo y fácil de usar.

**Especificaciones:**
- Interfaz responsive (mobile, tablet, desktop)
- Accesibilidad WCAG 2.1 AA
- Navegación intuitiva (máx 3 clics)
- Tooltips y ayuda contextual
- Mensajes de error claros
- Confirmaciones para acciones críticas

**Prioridad:** MEDIA

### RNF-06: Mantenibilidad
**Descripción:** El código debe ser fácil de mantener y extender.

**Especificaciones:**
- Código documentado (JSDoc/TSDoc)
- Cobertura de tests: > 70%
- Linting estricto (ESLint, Prettier)
- Arquitectura modular
- Commits semánticos (Conventional Commits)
- Versionado semántico (SemVer)

**Prioridad:** MEDIA

### RNF-07: Compatibilidad
**Descripción:** El sistema debe funcionar en múltiples navegadores y SO.

**Especificaciones:**
- Navegadores: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- SO Backend: Ubuntu 20.04+, Debian 11+, CentOS 8+
- Node.js: 18.x LTS o superior
- PostgreSQL: 13+ (recomendado 15+)
- Redis: 6+ (recomendado 7+)

**Prioridad:** MEDIA

### RNF-08: Compliance y Regulaciones
**Descripción:** El sistema debe cumplir con normativas de privacidad.

**Especificaciones:**
- Cumplimiento GDPR (datos en EU)
- Política de privacidad visible
- Consentimiento explícito para cookies
- Derecho al olvido (eliminación de datos)
- Portabilidad de datos
- Encriptación de datos sensibles en reposo

**Prioridad:** MEDIA

## 5. Modelo de Datos

### 5.1 Entidades Principales

#### Usuario (User)
```typescript
interface User {
  id: string; // UUID
  email: string; // único
  password: string; // hash bcrypt
  firstName: string;
  lastName: string;
  role: 'super_admin' | 'admin' | 'editor' | 'viewer';
  isActive: boolean;
  twoFactorEnabled: boolean;
  twoFactorSecret?: string;
  lastLogin?: Date;
  createdAt: Date;
  updatedAt: Date;
}
```

#### Servidor VPS (VPSServer)
```typescript
interface VPSServer {
  id: string; // UUID
  name: string;
  ipAddress: string;
  port: number; // default 22
  username: string;
  authMethod: 'password' | 'ssh_key';
  sshKeyId?: string;
  tags: string[];
  status: 'online' | 'offline' | 'unknown';
  lastChecked?: Date;
  createdBy: string; // User.id
  createdAt: Date;
  updatedAt: Date;
}
```

#### Llave SSH (SSHKey)
```typescript
interface SSHKey {
  id: string; // UUID
  name: string;
  publicKey: string;
  privateKey: string; // encriptado
  passphrase?: string; // encriptado
  fingerprint: string;
  createdBy: string; // User.id
  createdAt: Date;
  expiresAt?: Date;
}
```

#### Log de Auditoría (AuditLog)
```typescript
interface AuditLog {
  id: string; // UUID
  userId: string;
  action: string; // e.g., "LOGIN", "EXECUTE_COMMAND", "UPDATE_SERVER"
  resource: string; // e.g., "user:123", "server:456"
  details: object; // JSON con detalles
  ipAddress: string;
  userAgent: string;
  timestamp: Date;
}
```

#### Plantilla (Template)
```typescript
interface Template {
  id: string; // UUID
  name: string;
  description: string;
  category: string; // e.g., "setup", "maintenance", "security"
  script: string;
  parameters: Parameter[];
  createdBy: string; // User.id
  createdAt: Date;
  updatedAt: Date;
}
```

### 5.2 Relaciones
- Un Usuario puede gestionar múltiples Servidores
- Un Servidor puede tener múltiples Llaves SSH
- Un Usuario genera múltiples Logs de Auditoría
- Un Usuario puede crear múltiples Plantillas

## 6. APIs REST

### 6.1 Autenticación
```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
POST   /api/v1/auth/refresh
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/reset-password
POST   /api/v1/auth/verify-2fa
```

### 6.2 Usuarios
```
GET    /api/v1/users
GET    /api/v1/users/:id
POST   /api/v1/users
PUT    /api/v1/users/:id
DELETE /api/v1/users/:id
PATCH  /api/v1/users/:id/activate
PATCH  /api/v1/users/:id/deactivate
```

### 6.3 Servidores VPS
```
GET    /api/v1/servers
GET    /api/v1/servers/:id
POST   /api/v1/servers
PUT    /api/v1/servers/:id
DELETE /api/v1/servers/:id
POST   /api/v1/servers/:id/test-connection
GET    /api/v1/servers/:id/status
GET    /api/v1/servers/:id/metrics
```

### 6.4 SSH y Comandos
```
POST   /api/v1/ssh/keys
GET    /api/v1/ssh/keys
DELETE /api/v1/ssh/keys/:id
POST   /api/v1/ssh/execute
GET    /api/v1/ssh/history
```

### 6.5 Plantillas
```
GET    /api/v1/templates
GET    /api/v1/templates/:id
POST   /api/v1/templates
PUT    /api/v1/templates/:id
DELETE /api/v1/templates/:id
POST   /api/v1/templates/:id/execute
```

### 6.6 Documentación
```
GET    /api/v1/docs/sections
GET    /api/v1/docs/search?q=keyword
GET    /api/v1/docs/:id
```

### 6.7 Auditoría
```
GET    /api/v1/audit/logs
GET    /api/v1/audit/logs/:id
GET    /api/v1/audit/export
```

## 7. Guía de Deployment

### 7.1 Requisitos del Servidor

#### Hardware Mínimo
- CPU: 2 cores
- RAM: 4 GB
- Disco: 40 GB SSD
- Ancho de banda: 100 Mbps

#### Hardware Recomendado
- CPU: 4 cores
- RAM: 8 GB
- Disco: 80 GB SSD
- Ancho de banda: 1 Gbps

#### Software
- OS: Ubuntu 22.04 LTS
- Node.js 18.x LTS
- PostgreSQL 15
- Redis 7
- NGINX 1.22+
- Docker 24+ y Docker Compose 2+

### 7.2 Instalación con Docker

#### Paso 1: Clonar repositorio
```bash
git clone https://github.com/SantanderAcuna/documentacion.git
cd documentacion
```

#### Paso 2: Configurar variables de entorno
```bash
cp .env.example .env
nano .env
```

Variables críticas:
```env
NODE_ENV=production
PORT=3000
DATABASE_URL=postgresql://user:pass@localhost:5432/vps_portal
REDIS_URL=redis://localhost:6379
JWT_SECRET=your-super-secret-jwt-key-change-this
ENCRYPTION_KEY=your-32-byte-encryption-key
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password
```

#### Paso 3: Levantar servicios con Docker Compose
```bash
docker-compose up -d
```

#### Paso 4: Ejecutar migraciones
```bash
docker-compose exec backend npm run migrate
```

#### Paso 5: Crear usuario administrador inicial
```bash
docker-compose exec backend npm run seed:admin
```

### 7.3 Instalación Manual

#### Paso 1: Instalar dependencias del sistema
```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Instalar PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# Instalar Redis
sudo apt install -y redis-server

# Instalar NGINX
sudo apt install -y nginx
```

#### Paso 2: Configurar PostgreSQL
```bash
sudo -u postgres psql
CREATE DATABASE vps_portal;
CREATE USER vps_user WITH ENCRYPTED PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE vps_portal TO vps_user;
\q
```

#### Paso 3: Clonar y configurar aplicación
```bash
cd /var/www
git clone https://github.com/SantanderAcuna/documentacion.git
cd documentacion

# Backend
cd backend
npm install --production
cp .env.example .env
nano .env  # Configurar variables
npm run build
npm run migrate

# Frontend
cd ../frontend
npm install --production
npm run build
```

#### Paso 4: Configurar PM2 (Process Manager)
```bash
sudo npm install -g pm2
pm2 start ecosystem.config.js
pm2 save
pm2 startup
```

#### Paso 5: Configurar NGINX
```bash
sudo nano /etc/nginx/sites-available/vps-portal
```

Configuración NGINX:
```nginx
upstream backend {
    server 127.0.0.1:3000;
}

server {
    listen 80;
    server_name vps-portal.example.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name vps-portal.example.com;
    
    ssl_certificate /etc/letsencrypt/live/vps-portal.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/vps-portal.example.com/privkey.pem;
    
    # Frontend
    location / {
        root /var/www/documentacion/frontend/build;
        try_files $uri $uri/ /index.html;
    }
    
    # Backend API
    location /api {
        proxy_pass http://backend;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }
    
    # WebSocket
    location /socket.io {
        proxy_pass http://backend;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/vps-portal /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### Paso 6: Configurar SSL con Let's Encrypt
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d vps-portal.example.com
```

### 7.4 CI/CD con GitHub Actions

Archivo `.github/workflows/deploy.yml`:
```yaml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
      
      - name: Install dependencies
        run: |
          cd backend && npm ci
          cd ../frontend && npm ci
      
      - name: Run tests
        run: |
          cd backend && npm test
          cd ../frontend && npm test
      
      - name: Build
        run: |
          cd backend && npm run build
          cd ../frontend && npm run build
      
      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.VPS_HOST }}
          username: ${{ secrets.VPS_USER }}
          key: ${{ secrets.VPS_SSH_KEY }}
          script: |
            cd /var/www/documentacion
            git pull origin main
            cd backend && npm install && npm run build
            cd ../frontend && npm install && npm run build
            pm2 restart all
```

### 7.5 Monitoreo y Mantenimiento

#### Logs
```bash
# Logs de aplicación (PM2)
pm2 logs

# Logs de NGINX
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# Logs de PostgreSQL
sudo tail -f /var/log/postgresql/postgresql-15-main.log
```

#### Backups
```bash
# Script de backup automático
#!/bin/bash
BACKUP_DIR="/backups/vps-portal"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup de base de datos
pg_dump vps_portal > "$BACKUP_DIR/db_$DATE.sql"

# Backup de archivos
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" /var/www/documentacion

# Mantener solo últimos 7 días
find $BACKUP_DIR -type f -mtime +7 -delete
```

Configurar cron:
```bash
crontab -e
# Backup diario a las 2 AM
0 2 * * * /usr/local/bin/backup-vps-portal.sh
```

#### Actualizaciones
```bash
# Actualizar dependencias
cd /var/www/documentacion/backend
npm audit fix
npm update

cd ../frontend
npm audit fix
npm update

# Reiniciar servicios
pm2 restart all
```

## 8. Consideraciones de Seguridad

### 8.1 Checklist de Seguridad
- [ ] HTTPS configurado con certificado válido
- [ ] Firewall configurado (UFW): solo puertos 22, 80, 443
- [ ] SSH con autenticación por llave (sin password)
- [ ] Fail2ban instalado y configurado
- [ ] Credenciales en variables de entorno (no en código)
- [ ] Secrets rotados periódicamente
- [ ] Backups encriptados
- [ ] Logs de auditoría habilitados
- [ ] Rate limiting configurado
- [ ] Headers de seguridad (CSP, HSTS, etc.)

### 8.2 Gestión de Secretos
- Usar variables de entorno para secretos
- No commitear archivos `.env`
- Rotar JWT secrets cada 90 días
- Rotar credenciales de base de datos anualmente
- Usar servicios como HashiCorp Vault para producción enterprise

## 9. Roadmap Futuro

### Fase 1 - MVP (Actual)
- ✅ Documentación estática
- ✅ UI responsive
- ✅ Sistema de navegación

### Fase 2 - Backend y Auth (Q1 2026)
- Implementar backend Node.js
- Sistema de autenticación completo
- API REST funcional
- Base de datos PostgreSQL

### Fase 3 - Gestión VPS (Q2 2026)
- Conexión SSH a servidores
- Ejecución de comandos
- Plantillas de scripts
- Monitoreo básico

### Fase 4 - Automatización (Q3 2026)
- Tareas programadas (cron)
- Ejecución en lote
- Webhooks
- Integraciones con CI/CD

### Fase 5 - Monitoreo Avanzado (Q4 2026)
- Dashboard de métricas en tiempo real
- Alertas configurables
- Reportes automáticos
- Machine learning para predicción de fallos

## 10. Glosario

- **VPS:** Virtual Private Server - Servidor privado virtual
- **SSH:** Secure Shell - Protocolo de acceso remoto seguro
- **JWT:** JSON Web Token - Estándar para autenticación
- **RBAC:** Role-Based Access Control - Control de acceso basado en roles
- **2FA:** Two-Factor Authentication - Autenticación de dos factores
- **API:** Application Programming Interface - Interfaz de programación
- **ORM:** Object-Relational Mapping - Mapeo objeto-relacional
- **CDN:** Content Delivery Network - Red de distribución de contenido
- **SSL/TLS:** Secure Sockets Layer / Transport Layer Security - Protocolos de seguridad

---

**Versión:** 1.0.0  
**Última actualización:** Febrero 2026  
**Autor:** Equipo de Desarrollo VPS Portal  
**Licencia:** Propietaria
