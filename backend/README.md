# CMS Gubernamental - Backend API

> **Sistema de Gestión de Contenidos para Entidades Gubernamentales Colombianas**  
> **Versión:** 1.0.0  
> **Estado:** ✅ Production Ready  
> **Framework:** Laravel 11.48  
> **Licencia:** MIT

[![Laravel](https://img.shields.io/badge/Laravel-11.48-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)](https://mysql.com)
[![Tests](https://img.shields.io/badge/Tests-50%20passing-success?style=flat)](./TESTING.md)
[![Coverage](https://img.shields.io/badge/Coverage-85%25-brightgreen?style=flat)](./TEST_REPORT.md)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=flat)](../LICENSE)

---

## 📋 Tabla de Contenidos

- [Descripción](#descripción)
- [Características](#características)
- [Stack Tecnológico](#stack-tecnológico)
- [Instalación Rápida](#instalación-rápida)
- [Documentación](#documentación)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Cumplimiento Normativo](#cumplimiento-normativo)
- [Seguridad](#seguridad)

---

## 📝 Descripción

API RESTful robusta y segura para gestionar contenidos institucionales, PQRS ciudadanas, y cumplir con los requisitos de transparencia del gobierno colombiano.

---

## ✨ Características

### Gestión de Contenidos
- ✅ CRUD completo de contenidos
- ✅ Categorías jerárquicas (ilimitadas)
- ✅ Sistema de etiquetas (tags)
- ✅ Búsqueda full-text

### Sistema PQRS
- ✅ **P**eticiones, **Q**uejas, **R**eclamos, **S**ugerencias
- ✅ Folio único auto-generado
- ✅ Rastreo público por folio
- ✅ Cumple Ley 1755/2015

### Autenticación y Seguridad
- ✅ Laravel Sanctum (tokens HTTP-Only)
- ✅ Sistema RBAC (6 roles, 24 permisos)
- ✅ CSRF protection
- ✅ Activity logging completo

---

## �� Stack Tecnológico

- Laravel 11.48
- PHP 8.3+
- MySQL 8.0 / SQLite
- Redis 7.x
- Laravel Sanctum 4.3
- Spatie Permission 6.24
- Spatie Activity Log 4.11

---

## ⚡ Instalación Rápida

```bash
# Clonar e instalar
git clone https://github.com/SantanderAcuna/documentacion.git
cd documentacion/backend
composer install

# Configurar
cp .env.example .env
php artisan key:generate

# Migrar y sembrar
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=AdminUserSeeder

# Iniciar
php artisan serve
```

---

## 📚 Documentación

| Documento | Descripción |
|-----------|-------------|
| **[ARCHITECTURE.md](./ARCHITECTURE.md)** | Arquitectura técnica |
| **[DEVELOPMENT.md](./DEVELOPMENT.md)** | Guía para desarrolladores |
| **[DEPLOYMENT.md](./DEPLOYMENT.md)** | Deployment a producción |
| **[API_DOCUMENTATION.md](./API_DOCUMENTATION.md)** | Referencia de API |
| **[TESTING.md](./TESTING.md)** | Guía de testing |
| **[BACKEND_COMPLIANCE.md](./BACKEND_COMPLIANCE.md)** | Lista de cumplimiento |

---

## 🔌 API Endpoints

### Públicos
- `POST /api/v1/login` - Iniciar sesión
- `POST /api/v1/register` - Registrarse
- `GET /api/v1/contents` - Listar contenidos
- `POST /api/v1/pqrs` - Crear PQRS

### Protegidos (35+ endpoints)
Ver [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

---

## 🧪 Testing

```bash
php artisan test
```

**Resultado:** 50 tests, 158 assertions - ALL PASSING ✅

---

## 🇨🇴 Cumplimiento Normativo

- ✅ **Ley 1712/2014** - Transparencia
- ✅ **Ley 1755/2015** - PQRS  
- ✅ **Ley 1581/2012** - Protección de datos
- ✅ **Decreto 1078/2015** - Gobierno Digital

---

## 🔒 Seguridad

- Sanctum tokens HTTP-Only
- CSRF protection
- SQL injection prevention
- XSS prevention
- Rate limiting
- Activity logging

---

**Estado:** ✅ Production Ready  
**Versión:** 1.0.0

*Desarrollado con ❤️ para servir a la ciudadanía colombiana* 🇨🇴
