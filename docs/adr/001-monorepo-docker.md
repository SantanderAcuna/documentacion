# ADR-001: Monorepo con Docker para Desarrollo

**Estado:** Aceptado  
**Fecha:** 2026-02-17  
**Decisores:** Equipo de Arquitectura  
**Contexto:** Fase 1 - Constitución del Proyecto

---

## Contexto y Problema

El proyecto CMS Gubernamental requiere múltiples componentes:
- Backend API (Laravel)
- Frontend Admin (Vue 3 + Vuestic)
- Frontend Público (Vue 3 + GOV.CO)
- Base de datos (MySQL)
- Caché (Redis)

Debemos decidir cómo organizar y gestionar estos componentes para facilitar el desarrollo, testing y despliegue.

---

## Factores de Decisión

- **Simplicidad de desarrollo:** Fácil configuración local
- **Consistencia de entornos:** Dev, staging, producción
- **Versionamiento:** Control de versiones unificado
- **Reproducibilidad:** Cualquier desarrollador puede iniciar el proyecto
- **Aislamiento:** Evitar "funciona en mi máquina"

---

## Opciones Consideradas

### Opción 1: Monorepo con Docker Compose ✅
- **Pros:**
  - Todos los componentes versionados juntos
  - Configuración centralizada en docker-compose.yml
  - Fácil inicio con un solo comando
  - Networking automático entre servicios
  - Consistencia entre desarrolladores
- **Contras:**
  - Tamaño del repositorio crece más rápido
  - Requiere Docker instalado
  - Builds iniciales pueden ser lentos

### Opción 2: Repositorios Separados
- **Pros:**
  - Repositorios más pequeños
  - Equipos pueden trabajar independientemente
- **Contras:**
  - Sincronización de versiones compleja
  - Configuración local más compleja
  - Dificulta testing de integración

### Opción 3: Monorepo sin Docker
- **Pros:**
  - No requiere Docker
  - Más ligero
- **Contras:**
  - "Funciona en mi máquina"
  - Configuración manual de PHP, Node, MySQL, Redis
  - Versiones inconsistentes entre desarrolladores

---

## Decisión

**Elegimos Opción 1: Monorepo con Docker Compose**

### Estructura del Monorepo
```
cms-gubernamental/
├── backend/              # Laravel 12
├── frontend-admin/       # Vue 3 + Vuestic
├── frontend-public/      # Vue 3 + GOV.CO
├── docker/
│   ├── backend/Dockerfile
│   ├── frontend/Dockerfile
│   ├── nginx/
│   └── mysql/
├── docker-compose.yml
└── docs/
```

### Servicios Docker
1. **nginx:** Reverse proxy
2. **backend:** PHP 8.3-fpm + Laravel 12
3. **mysql:** MySQL 8.0
4. **redis:** Redis 7.x
5. **frontend-admin:** Node 18 + Vite
6. **frontend-public:** Node 18 + Vite
7. **phpmyadmin:** Gestión BD
8. **redis-commander:** Gestión Redis

---

## Consecuencias

### Positivas
- ✅ Desarrollo local con `docker-compose up -d`
- ✅ Todos los desarrolladores trabajan en mismo entorno
- ✅ Fácil onboarding de nuevos desarrolladores
- ✅ CI/CD simplificado (usa mismos Dockerfiles)
- ✅ Testing de integración más fácil

### Negativas
- ⚠️ Requiere Docker instalado (20.10+)
- ⚠️ Build inicial ~5-10 minutos
- ⚠️ Tamaño del repositorio será mayor

### Neutrales
- ℹ️ Para desarrollo local sin Docker, documentar setup manual

---

## Validación

### Criterios de Éxito
- [ ] Cualquier desarrollador puede iniciar el proyecto en <15 minutos
- [ ] Mismo comportamiento en todos los entornos
- [ ] Tests pasan en local y CI

### Plan de Migración
No aplica (proyecto nuevo)

---

## Notas

### Comandos Importantes
```bash
# Iniciar todos los servicios
docker-compose up -d

# Ver logs
docker-compose logs -f

# Detener servicios
docker-compose down

# Reconstruir imágenes
docker-compose build --no-cache
```

### Puertos Expuestos
- 80/443: Nginx
- 3000: Frontend Admin
- 3001: Frontend Público
- 8000: Backend API (directo)
- 3306: MySQL
- 6379: Redis
- 8080: PhpMyAdmin
- 8081: Redis Commander

---

## Referencias
- Docker Compose docs: https://docs.docker.com/compose/
- Laravel Sail (inspiración): https://laravel.com/docs/sail
- Trunk-based Development: https://trunkbaseddevelopment.com/

---

**Firmado:** Equipo de Arquitectura  
**Próxima revisión:** 2026-08-17 (6 meses)
