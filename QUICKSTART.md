# Inicio Rápido - CMS Gubernamental

Esta guía te ayudará a tener el proyecto funcionando en menos de 15 minutos.

## 📋 Requisitos Previos

Asegúrate de tener instalado:

- ✅ **Docker** 20.10+ ([Instalar Docker](https://docs.docker.com/get-docker/))
- ✅ **Docker Compose** v2.0+ (incluido con Docker Desktop)
- ✅ **Git** ([Instalar Git](https://git-scm.com/downloads))

**Opcional para desarrollo sin Docker:**
- Node.js 18.x LTS
- PHP 8.3+
- Composer 2.x

## 🚀 Instalación en 5 Pasos

### 1. Clonar el Repositorio

```bash
git clone https://github.com/SantanderAcuna/documentacion.git cms-gubernamental
cd cms-gubernamental
```

### 2. Configurar Variables de Entorno

```bash
# Backend
cp backend/.env.example backend/.env

# Frontend Admin
cp frontend-admin/.env.example frontend-admin/.env

# Frontend Public
cp frontend-public/.env.example frontend-public/.env
```

> **Nota:** Para desarrollo local, las variables por defecto funcionan. Para producción, revisa la [Guía de Despliegue](docs/deployment.md).

### 3. Iniciar Contenedores Docker

```bash
docker-compose up -d
```

Esto iniciará todos los servicios:
- ✅ MySQL 8.0
- ✅ Redis 7.x
- ✅ Backend (Laravel) - Puerto 8000
- ✅ Frontend Admin - Puerto 3000
- ✅ Frontend Public - Puerto 3001
- ✅ PhpMyAdmin - Puerto 8080
- ✅ Redis Commander - Puerto 8081

### 4. Configurar el Backend (Una Sola Vez)

```bash
# Instalar dependencias
docker-compose exec backend composer install

# Generar clave de aplicación
docker-compose exec backend php artisan key:generate

# Ejecutar migraciones (cuando estén creadas)
docker-compose exec backend php artisan migrate

# Ejecutar seeders (cuando estén creados)
docker-compose exec backend php artisan db:seed
```

### 5. Instalar Dependencias de Frontend

```bash
# Frontend Admin
docker-compose exec frontend-admin npm install

# Frontend Public
docker-compose exec frontend-public npm install
```

## ✅ Verificar la Instalación

Abre tu navegador y visita:

- 🔹 **Backend API:** http://localhost:8000
- 🔹 **Panel Admin:** http://localhost:3000
- 🔹 **Sitio Público:** http://localhost:3001
- 🔹 **PhpMyAdmin:** http://localhost:8080 (usuario: `cms_user`, password: `cms_password`)
- 🔹 **Redis Commander:** http://localhost:8081

## 🛠️ Comandos Útiles

### Ver Logs

```bash
# Todos los servicios
docker-compose logs -f

# Backend específico
docker-compose logs -f backend

# Frontend admin
docker-compose logs -f frontend-admin
```

### Detener Servicios

```bash
# Detener todo
docker-compose down

# Detener y eliminar volúmenes (resetea base de datos)
docker-compose down -v
```

### Reiniciar Servicios

```bash
# Reiniciar todo
docker-compose restart

# Reiniciar backend
docker-compose restart backend
```

### Ejecutar Comandos

```bash
# Backend - Laravel Artisan
docker-compose exec backend php artisan [comando]

# Frontend Admin - NPM
docker-compose exec frontend-admin npm run [comando]

# Frontend Public - NPM
docker-compose exec frontend-public npm run [comando]

# MySQL
docker-compose exec mysql mysql -u cms_user -p cms_db
```

## 🧪 Ejecutar Tests

```bash
# Backend
docker-compose exec backend php artisan test

# Frontend Admin
docker-compose exec frontend-admin npm run test

# Frontend Public
docker-compose exec frontend-public npm run test
```

## 🐛 Solución de Problemas

### Puerto ya en uso

Si algún puerto está ocupado:

```bash
# Opción 1: Detener el servicio que usa el puerto
# Opción 2: Cambiar el puerto en docker-compose.yml
ports:
  - "8001:8000"  # Cambiar de 8000 a 8001
```

### Error de permisos

```bash
# Linux/Mac
sudo chown -R $USER:$USER .

# Windows con WSL2
wsl --shutdown
```

### Contenedor no inicia

```bash
# Ver logs detallados
docker-compose logs [servicio]

# Reconstruir imagen
docker-compose build --no-cache [servicio]
docker-compose up -d [servicio]
```

### Base de datos no conecta

```bash
# Verificar que MySQL esté corriendo
docker-compose ps mysql

# Reiniciar MySQL
docker-compose restart mysql

# Verificar variables en backend/.env
DB_HOST=mysql  # Debe ser 'mysql', no 'localhost'
```

## 📚 Próximos Pasos

1. **Leer la Documentación:**
   - [README Principal](README.md)
   - [Constitución del Proyecto](constitution.md)
   - [Contexto](docs/context.md)
   - [ADRs](docs/adr/)

2. **Desarrollo:**
   - Revisar [CONTRIBUTING.md](CONTRIBUTING.md)
   - Conocer estándares de código
   - Entender flujo de trabajo Git

3. **Aprender el Stack:**
   - [Laravel 12 Docs](https://laravel.com/docs/12.x)
   - [Vue 3 Docs](https://vuejs.org/guide/)
   - [Vuestic UI Docs](https://vuestic.dev/)
   - [GOV.CO Design](https://www.gov.co/)

## 🆘 Ayuda

Si tienes problemas:

1. Revisa la [Solución de Problemas](#solución-de-problemas)
2. Busca en [GitHub Issues](https://github.com/SantanderAcuna/documentacion/issues)
3. Lee la [documentación completa](README.md)
4. Abre un nuevo issue describiendo el problema

## 📝 Notas Importantes

- ⚠️ **Este es un entorno de DESARROLLO**. Para producción, consulta [docs/deployment.md](docs/deployment.md)
- ⚠️ Las credenciales por defecto son para desarrollo local únicamente
- ⚠️ En producción, usa HTTPS y credenciales seguras
- ⚠️ Revisa las normativas colombianas aplicables

## ✨ Estado del Proyecto

**Fase Actual:** Fase 1 - Constitución del Proyecto

**Completado:**
- ✅ Estructura de directorios
- ✅ Configuración Docker
- ✅ Documentación base
- ✅ ADRs iniciales
- ✅ CI/CD pipeline

**En Progreso:**
- 🔄 Inicialización de Laravel
- 🔄 Inicialización de Vue 3
- 🔄 Implementación de autenticación

**Próximo:**
- ⏳ Modelos y migraciones
- ⏳ APIs REST
- ⏳ Interfaz administrativa
- ⏳ Sitio público

---

**¿Listo para empezar?** 🚀

```bash
docker-compose up -d
```

¡Bienvenido al equipo! 🇨🇴
