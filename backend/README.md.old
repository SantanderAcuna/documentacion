# Backend - Laravel 12

Este directorio contiene la aplicación backend desarrollada con Laravel 12.

## Requisitos

- PHP 8.3+
- Composer 2.x
- MySQL 8.0+
- Redis 7.x

## Instalación

### Con Docker (Recomendado)
```bash
docker-compose up -d
docker-compose exec backend composer install
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate --seed
```

### Sin Docker
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Estructura

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/V1/          # Controladores API versión 1
│   │   ├── Middleware/
│   │   ├── Requests/            # FormRequests para validación
│   │   └── Resources/           # API Resources
│   ├── Models/
│   ├── Services/                # Lógica de negocio
│   ├── Repositories/            # Patrón Repository
│   └── Observers/               # Model Observers
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   ├── api.php                  # Rutas API
│   └── web.php
├── tests/
│   ├── Feature/
│   └── Unit/
└── storage/
```

## Comandos Útiles

```bash
# Tests
php artisan test
php artisan test --coverage

# Análisis estático
vendor/bin/phpstan analyse

# Code style
vendor/bin/php-cs-fixer fix

# Generar documentación API
php artisan scribe:generate

# Limpiar caché
php artisan optimize:clear

# Ver rutas
php artisan route:list
```

## API

La API está versionada en `/api/v1/`

### Endpoints Principales

- `POST /api/v1/login` - Autenticación
- `POST /api/v1/logout` - Cerrar sesión
- `GET /api/v1/user` - Usuario actual
- `GET /api/v1/contents` - Listado de contenidos
- `GET /api/v1/transparency` - Información de transparencia

Ver documentación completa en `/docs/api` después de ejecutar `php artisan scribe:generate`

## Seguridad

- ✅ Laravel Sanctum (cookies HTTP-Only)
- ✅ CSRF Protection
- ✅ Rate Limiting
- ✅ SQL Injection prevention (Eloquent)
- ✅ XSS prevention (Blade escaping)
- ✅ Auditoría (spatie/laravel-activitylog)

## Testing

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=AuthenticationTest

# Con cobertura
php artisan test --coverage --min=80
```

## Próximos Pasos

1. [ ] Crear modelos base (User, Content, Category)
2. [ ] Implementar autenticación con Sanctum
3. [ ] Configurar roles y permisos (Spatie Permission)
4. [ ] Crear migraciones iniciales
5. [ ] Implementar API REST base
6. [ ] Configurar auditoría
