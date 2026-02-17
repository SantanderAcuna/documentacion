# Architecture Decision Records (ADR)

## ¿Qué es un ADR?

Un Architecture Decision Record documenta una decisión arquitectónica importante, su contexto y sus consecuencias. Este documento contiene los ADRs del Portal de Configuración VPS.

---

## ADR-001: Usar Laravel 12 como Framework Backend

**Fecha:** 2026-01-15  
**Estado:** Aceptado  
**Contexto:** Se requiere un framework robusto para el backend con buena comunidad y soporte.

### Decisión
Usar Laravel 12 (PHP 8.3+) como framework principal para el backend.

### Razones
1. **Ecosystem Maduro**: ORM (Eloquent), autenticación (Sanctum), queue, cache
2. **Comunidad Grande**: Documentación extensa, paquetes disponibles
3. **Developer Experience**: Artisan CLI, migrations, seeders
4. **Performance**: PHP 8.3 mejoras significativas
5. **Seguridad**: Built-in protecciones CSRF, XSS, SQL Injection

### Consecuencias

**Positivas:**
- Desarrollo rápido con convenciones claras
- Ecosistema de paquetes (Spatie Permission, etc.)
- Testing framework incluido (PHPUnit)
- Deployment bien documentado

**Negativas:**
- Dependencia de PHP (no Go, Node, etc.)
- Overhead del framework vs. micro-framework
- Learning curve para equipo sin experiencia Laravel

### Alternativas Consideradas
- **Symfony**: Más complejo, mayor learning curve
- **Lumen**: Micro-framework, menos features out-of-the-box
- **Node.js (Express/NestJS)**: Diferente ecosistema, menos maduro para este caso de uso

---

## ADR-002: Usar Vue.js 3 con Composition API

**Fecha:** 2026-01-16  
**Estado:** Aceptado  
**Contexto:** Necesitamos un framework moderno de frontend para SPA interactiva.

### Decisión
Usar Vue.js 3 con Composition API y TypeScript.

### Razones
1. **Modern Reactivity**: Sistema de reactividad mejorado
2. **TypeScript Support**: Primera clase con Composition API
3. **Performance**: Virtual DOM optimizado, mejor que Vue 2
4. **Developer Experience**: Composition API más intuitivo
5. **Ecosystem**: Pinia (state), Vue Router (routing), Vite (tooling)

### Consecuencias

**Positivas:**
- Componentes reutilizables y mantenibles
- TypeScript para type safety
- Ecosystem unificado (Vite, Pinia, Router)
- Performance excelente

**Negativas:**
- Learning curve para Composition API
- Breaking changes desde Vue 2
- Menor ecosistema que React

### Alternativas Consideradas
- **React**: Mayor ecosistema pero más complejo para este caso
- **Svelte**: Menos maduro, menor comunidad
- **Angular**: Overhead excesivo para este proyecto

---

## ADR-003: Usar MySQL 8.0+ como Base de Datos Principal

**Fecha:** 2026-01-17  
**Estado:** Aceptado  
**Contexto:** Se requiere base de datos relacional confiable con soporte ACID.

### Decisión
MySQL 8.0+ como base de datos principal.

### Razones
1. **ACID Compliance**: Transacciones confiables
2. **Full-Text Search**: Búsqueda integrada
3. **Performance**: Optimizaciones en versión 8.0
4. **Managed Options**: DigitalOcean Managed Database disponible
5. **Ecosystem**: Bien soportado por Laravel Eloquent

### Consecuencias

**Positivas:**
- Queries optimizadas con índices
- Full-text search nativo
- JSON column type para flexibilidad
- Backups automáticos disponibles

**Negativas:**
- Menos flexible que NoSQL para datos no estructurados
- Escala vertical más que horizontal
- Costo de managed database

### Alternativas Consideradas
- **PostgreSQL**: Features avanzadas pero mayor complejidad
- **MongoDB**: No relacional, no apropiado para nuestro modelo
- **SQLite**: Insuficiente para producción

---

## ADR-004: Autenticación con Laravel Sanctum

**Fecha:** 2026-01-18  
**Estado:** Aceptado  
**Contexto:** Necesitamos autenticación segura para SPA.

### Decisión
Usar Laravel Sanctum con cookies HTTP-Only para autenticación.

### Razones
1. **SPA-Friendly**: Diseñado para SPAs Vue/React
2. **Seguridad**: Cookies HTTP-Only previenen XSS
3. **Simplicidad**: Menos complejo que OAuth2/JWT
4. **Laravel Native**: Integrado con Laravel
5. **CSRF Protection**: Built-in

### Consecuencias

**Positivas:**
- No necesitamos almacenar tokens en localStorage
- CSRF protection automático
- Sessiones stateful simples
- Compatible con mobile apps (token API)

**Negativas:**
- Limitado a same-domain o CORS configurado
- No es OAuth2 estándar (si necesitáramos third-party)
- Sessiones en Redis requieren sticky sessions en load balancer

### Alternativas Consideradas
- **Laravel Passport (OAuth2)**: Overhead innecesario
- **JWT**: Más complejo, tokens en localStorage (XSS vulnerable)
- **Session-based**: Sanctum es mejor para SPA

---

## ADR-005: Usar Spatie Laravel-Permission para RBAC

**Fecha:** 2026-01-19  
**Estado:** Aceptado  
**Contexto:** Necesitamos sistema flexible de roles y permisos.

### Decisión
Implementar RBAC con paquete Spatie Laravel-Permission.

### Razones
1. **Community Standard**: Paquete más popular para RBAC en Laravel
2. **Flexible**: Roles, permisos, guards
3. **Cache**: Built-in caching de permisos
4. **Blade/Policy Integration**: Integra con sistema de autorización Laravel
5. **Well Tested**: Maduro y estable

### Consecuencias

**Positivas:**
- Fácil agregar/modificar roles y permisos
- Cache automático para performance
- Middleware disponibles
- Blade directives útiles

**Negativas:**
- Dependencia externa
- Overhead vs. implementación custom simple
- Migraciones adicionales

### Alternativas Consideradas
- **Custom Implementation**: Más trabajo, menos flexible
- **Laravel Built-in**: Básico, sin RBAC avanzado
- **Bouncer**: Alternativa viable pero menos popular

---

## ADR-006: Usar Pinia para State Management

**Fecha:** 2026-01-20  
**Estado:** Aceptado  
**Contexto:** Necesitamos gestión de estado global en Vue 3.

### Decisión
Usar Pinia como solución de state management.

### Razones
1. **Official Recommendation**: Vue 3 recomienda Pinia sobre Vuex
2. **TypeScript Support**: Excelente soporte TypeScript
3. **DevTools**: Integración con Vue DevTools
4. **Composition API**: Diseñado para Composition API
5. **Modularity**: Stores modulares por feature

### Consecuencias

**Positivas:**
- API simple e intuitiva
- Type safety con TypeScript
- Hot module replacement
- Plugins ecosystem

**Negativas:**
- Relativamente nuevo (menos maduro que Vuex)
- Learning curve si se conoce Vuex

### Alternativas Consideradas
- **Vuex**: Legacy, no optimizado para Vue 3
- **Custom Composables**: Suficiente para estados simples, insuficiente para app completa
- **Context API**: No existe en Vue (solo React)

---

## ADR-007: Deployment en DigitalOcean con Ubuntu 24.04

**Fecha:** 2026-01-21  
**Estado:** Aceptado  
**Contexto:** Necesitamos plataforma cloud confiable y asequible.

### Decisión
Desplegar en DigitalOcean usando Droplets con Ubuntu 24.04 LTS o App Platform.

### Razones
1. **Costo**: Competitivo comparado con AWS/GCP
2. **Simplicidad**: Más simple que AWS
3. **Managed Services**: MySQL, Redis disponibles
4. **Ubuntu 24.04 LTS**: Soporte hasta 2029
5. **Spaces (S3)**: Storage object S3-compatible

### Consecuencias

**Positivas:**
- Costo predecible
- UI simple de usar
- Managed databases
- CDN integrado
- Buenos SLAs

**Negativas:**
- Menos features que AWS/GCP/Azure
- Menos regiones globales
- Vendor lock-in moderado

### Alternativas Consideradas
- **AWS**: Más complejo, caro
- **GCP**: Similar a AWS
- **Heroku**: Más caro, menos control
- **VPS traditional**: Menos managed services

---

## ADR-008: Usar Redis para Cache y Queue

**Fecha:** 2026-01-22  
**Estado:** Aceptado  
**Contexto:** Necesitamos cache rápida y sistema de queue.

### Decisión
Usar Redis para caching, sessions y queue system.

### Razones
1. **Performance**: In-memory, muy rápido
2. **Multi-Purpose**: Cache, sessions, queue, pub/sub
3. **Laravel Support**: Excelente integración Laravel
4. **Managed Option**: DigitalOcean Managed Redis
5. **Persistence**: Opciones RDB + AOF

### Consecuencias

**Positivas:**
- Cache hits muy rápidos
- Queue jobs procesados eficientemente
- Sesiones compartidas entre servers
- Reduce carga en MySQL

**Negativas:**
- Costo adicional (managed Redis)
- Datos en memoria (limita tamaño)
- Single point of failure (mitigar con clustering)

### Alternativas Consideradas
- **Memcached**: Solo cache, no queue/sessions
- **Database Queue**: Lento
- **Array Cache**: No funciona multi-server

---

## ADR-009: TypeScript Strict Mode

**Fecha:** 2026-01-23  
**Estado:** Aceptado  
**Contexto:** Decisión sobre nivel de strictness de TypeScript.

### Decisión
Habilitar TypeScript strict mode en frontend.

### Razones
1. **Type Safety**: Catch errors en compile time
2. **Better IDE Support**: Autocompletado mejorado
3. **Maintainability**: Código auto-documentado
4. **Refactoring**: Safer refactorings
5. **Team Collaboration**: Contracts claros

### Consecuencias

**Positivas:**
- Menos bugs en runtime
- Mejor developer experience
- Código más mantenible
- Safer refactorings

**Negativas:**
- Learning curve inicial
- Más código (type annotations)
- Slower development al principio

### Alternativas Consideradas
- **JavaScript**: Sin type safety
- **TypeScript loose**: Menos seguridad
- **Flow**: Menos popular, menor ecosistema

---

## ADR-010: Bootstrap 5 para UI Framework

**Fecha:** 2026-01-24  
**Estado:** Aceptado  
**Contexto:** Necesitamos framework CSS para desarrollo rápido.

### Decisión
Usar Bootstrap 5 (sin jQuery) como base UI.

### Razones
1. **No jQuery**: Bootstrap 5 es vanilla JS
2. **Responsive Grid**: Sistema grid probado
3. **Components**: Componentes pre-built
4. **Customizable**: SASS variables
5. **Familiar**: Equipo conoce Bootstrap

### Consecuencias

**Positivas:**
- Desarrollo UI rápido
- Responsive by default
- Componentes consistentes
- Bien documentado

**Negativas:**
- Look and feel genérico (mitigar con customización)
- Bundle size mayor que Tailwind
- Opinionated styles

### Alternativas Consideradas
- **Tailwind CSS**: Más moderno pero mayor learning curve
- **Material UI**: Muy opinionated
- **Custom CSS**: Mucho trabajo

---

## Template para Nuevos ADRs

```markdown
## ADR-XXX: Título de la Decisión

**Fecha:** YYYY-MM-DD  
**Estado:** Propuesto | Aceptado | Rechazado | Deprecated  
**Contexto:** Descripción del problema o situación.

### Decisión
Qué se decidió.

### Razones
1. Razón 1
2. Razón 2
3. Razón 3

### Consecuencias

**Positivas:**
- Pro 1
- Pro 2

**Negativas:**
- Con 1
- Con 2

### Alternativas Consideradas
- **Opción A**: Por qué no se eligió
- **Opción B**: Por qué no se eligió
```

---

## Estados de ADR

- **Propuesto**: En discusión
- **Aceptado**: Implementado y en uso
- **Rechazado**: No se implementará
- **Deprecated**: Ya no se usa, reemplazado por otro ADR
- **Superseded**: Reemplazado por ADR-XXX

---

**Última actualización:** 2026-02-17  
**Mantenedor:** Equipo de Arquitectura
