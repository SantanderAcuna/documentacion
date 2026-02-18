# FASE 1: CONSTITUCIÓN DEL PROYECTO
## CMS Gubernamental - Alcaldía Distrital de Santa Marta

---

## 📋 Tabla de Contenidos

1. [Propósito y Alcance](#11-propósito-y-alcance)
2. [Principios Rectores](#12-principios-rectores-no-negociables)
3. [Stack Tecnológico](#13-stack-tecnológico)

---

## 1.1 Propósito y Alcance

### Objetivo General

Desarrollar un **Sistema de Gestión de Contenidos (CMS) profesional** para la **Alcaldía Distrital de Santa Marta** que permita:

- **Crear, gestionar y publicar** información institucional
- **Gestionar normativa** y documentación oficial
- **Garantizar transparencia** y acceso a la información pública
- **Facilitar participación ciudadana** mediante PQRS digital
- **Cumplir integralmente** con las normativas colombianas vigentes

---

### Normativas Colombianas a Cumplir

El CMS debe garantizar el cumplimiento integral de:

#### 1. **Ley 1341/2009** - Gobierno en Línea
- Portal institucional accesible 24/7
- Servicios en línea para ciudadanos
- Sistema PQRS digital
- Información institucional completa

#### 2. **Ley 1712/2014** - Transparencia y Acceso a la Información
- Transparencia activa (publicación proactiva)
- Acceso a información pública
- Actualización periódica (mínimo mensual)
- Información mínima obligatoria

#### 3. **Decreto 1078/2015** - Gobierno Digital
- Arquitectura TI del Estado
- Interoperabilidad
- Seguridad y privacidad
- Servicios ciudadanos digitales

#### 4. **Resolución 1519/2020** - Accesibilidad Web
- Cumplimiento **WCAG 2.1 nivel AA**
- Accesibilidad para personas con discapacidad
- Navegación por teclado
- Lectores de pantalla
- Contraste mínimo 4.5:1

#### 5. **ITA** - Índice de Transparencia y Acceso a la Información
- Indicadores de transparencia
- Publicación de:
  - Decretos, gacetas, circulares, actas
  - Contratos y contratación
  - Presupuesto y ejecución
  - Estructura organizacional
  - Normatividad vigente

#### 6. **FURAG** - Función Administrativa y Resultados de la Gestión (MIPG)
- Modelo Integrado de Planeación y Gestión
- Gestión documental
- Transparencia y acceso
- Rendición de cuentas
- Auditoría y seguimiento

#### 7. **Ley 1581/2012** - Protección de Datos Personales (Habeas Data)
- Consentimiento previo informado
- Finalidad legítima del tratamiento
- Seguridad de la información
- Derecho a acceso, rectificación, actualización
- Derecho al olvido (cancelación)

---

### Perfiles de Usuario

El sistema contempla **5 perfiles principales**:

#### 1. 👥 Ciudadanos/Visitantes
**Acceso:** Público (no requiere autenticación)

**Puede:**
- Consultar información pública
- Descargar documentos (decretos, gacetas, etc.)
- Enviar solicitudes PQRS
- Hacer seguimiento a PQRS con número de radicado
- Descargar datos abiertos
- Consultar contratos y presupuesto
- Acceder sin restricciones a transparencia

**No puede:**
- Crear o modificar contenidos
- Acceder a panel administrativo

---

#### 2. ✍️ Editores
**Acceso:** Autenticado (usuario + contraseña)

**Puede:**
- Crear y editar contenidos
- Publicar noticias, blogs, posts
- Gestionar decretos, gacetas, circulares, actas
- Subir archivos multimedia
- Asignar categorías y etiquetas
- Ver borradores propios
- Enviar contenidos a revisión

**No puede:**
- Publicar sin aprobación (según workflow)
- Eliminar contenidos publicados
- Gestionar usuarios
- Modificar configuración del sistema

---

#### 3. 🔍 Administradores de Transparencia
**Acceso:** Autenticado con permisos especiales

**Puede:**
- Todo lo que pueden los editores
- Gestionar información ITA
- Publicar contratos SECOP
- Gestionar presupuestos y ejecución
- Publicar datos abiertos
- Generar reportes FURAG
- Aprobar publicaciones de transparencia
- Programar actualizaciones mensuales

**No puede:**
- Gestionar usuarios del sistema
- Modificar configuración global
- Acceder a logs del sistema

---

#### 4. ⚙️ Administradores del Sistema
**Acceso:** Autenticado con máximo nivel

**Puede:**
- Todo lo que pueden editores y admins transparencia
- Crear y gestionar usuarios
- Asignar roles y permisos
- Configurar tipos de contenido
- Configurar campos personalizables
- Gestionar workflow y estados
- Ver logs de auditoría
- Configurar sistema (cache, backups, etc.)
- Acceder a todas las funcionalidades

**No puede:**
- Violar principios de seguridad
- Saltarse logs de auditoría
- Comprometer normativas

---

#### 5. 🔎 Auditores / Entes de Control
**Acceso:** Solo lectura (autenticado)

**Puede:**
- Ver toda la información pública
- Ver toda la información de transparencia
- Descargar reportes de auditoría
- Ver logs de actividad
- Verificar cumplimiento normativo
- Acceder a datos abiertos
- Generar informes

**No puede:**
- Modificar información
- Crear contenidos
- Eliminar registros
- Gestionar usuarios

---

## 1.2 Principios Rectores (No Negociables)

Los siguientes principios son **fundamentales e innegociables** en el desarrollo del CMS:

---

### 1. 📖 Claridad sobre Ingenio

**Principio:** *Las especificaciones son la fuente de verdad*

- El **código debe ser autoexplicativo**
- Se privilegia la claridad sobre soluciones "ingeniosas"
- **Documentación obligatoria** para lógica compleja
- Los nombres de variables, funciones y clases deben ser descriptivos
- Evitar "magia" sin documentación

**Aplicación:**
```php
// ❌ MAL - Ingenioso pero confuso
$d = fn($x) => array_reduce($x, fn($c, $i) => $c + ($i['a'] ?? 0), 0);

// ✅ BIEN - Claro y autoexplicativo
public function calcularTotalActivos(array $elementos): int
{
    $total = 0;
    foreach ($elementos as $elemento) {
        if (isset($elemento['activo']) && $elemento['activo']) {
            $total++;
        }
    }
    return $total;
}
```

---

### 2. ⚖️ Cumplimiento Normativo Ante Todo

**Principio:** *Ninguna funcionalidad puede contradecir las leyes MinTIC o de transparencia*

- **Checklist de cumplimiento** en cada tarea
- Validación automática de requisitos legales
- **Zero tolerancia** a incumplimientos
- Documentación de evidencias de cumplimiento

**Aplicación:**
- Antes de cada feature: revisar checklist normativo
- Tests automatizados de cumplimiento
- Auditoría continua
- Documentar en `NORMATIVAS_CUMPLIMIENTO.md`

---

### 3. 🔒 Seguridad por Diseño

**Principio:** *Los controles de seguridad se integran desde el inicio, no como parches*

#### Medidas Obligatorias:

**1. HTTPS Obligatorio**
- Certificado SSL/TLS válido
- Redirección automática HTTP → HTTPS
- HSTS (HTTP Strict Transport Security)

**2. Validación de Entradas**
- Validar TODAS las entradas de usuario
- FormRequest en Laravel
- Sanitización de datos
- Escape de salidas

**3. Protección CSRF**
- Token CSRF en todos los formularios
- Verificación automática Laravel
- Sanctum para APIs

**4. Prevención Inyección SQL**
- Usar Eloquent ORM (prepared statements)
- NUNCA concatenar SQL raw
- Validar parámetros de consultas

**5. Encriptación de Datos Sensibles**
- Datos personales PQRS encriptados
- Contraseñas con bcrypt cost=12
- Secrets en variables de entorno (.env)

**Aplicación:**
```php
// ✅ BIEN - Protegido
public function store(StoreContenidoRequest $request)
{
    // Request validado automáticamente
    $contenido = Contenido::create($request->validated());
    // Eloquent usa prepared statements
}

// ❌ MAL - Vulnerable
public function store(Request $request)
{
    $contenido = DB::insert("INSERT INTO contenidos VALUES ('{$request->titulo}')");
    // Vulnerable a SQL injection
}
```

---

### 4. ♿ Accesibilidad Universal

**Principio:** *WCAG 2.1 nivel AA en TODO el contenido público*

#### Requisitos Obligatorios:

**1. Textos Alternativos**
- Alt text en TODAS las imágenes
- Campo `texto_alternativo` en tabla `medios`
- Validación obligatoria en formularios

**2. Contraste de Color**
- Contraste mínimo **4.5:1** para texto normal
- Contraste mínimo **3:1** para texto grande
- Validación con herramientas (axe DevTools)

**3. Navegación por Teclado**
- Tab order lógico
- Skip links
- Focus visible
- Keyboard traps prohibidos

**4. HTML Semántico**
- `<header>`, `<nav>`, `<main>`, `<footer>`, `<article>`, `<section>`
- Jerarquía de headings correcta (h1-h6)
- `<button>` para acciones, `<a>` para enlaces

**5. ARIA Attributes**
- Labels descriptivos en formularios
- `aria-label`, `aria-describedby` cuando necesario
- Roles ARIA apropiados

**6. Transcripciones**
- Videos y audios requieren transcripción
- Campo `transcripcion` en tabla `contenidos`
- Subtítulos para videos

**Aplicación:**
```html
<!-- ✅ BIEN - Accesible -->
<img src="decreto.jpg" alt="Firma del Decreto 001-2026 por el Alcalde" />

<button type="button" aria-label="Cerrar modal">
    <i class="fa fa-times"></i>
</button>

<!-- ❌ MAL - Inaccesible -->
<img src="decreto.jpg" />
<div onclick="cerrar()">X</div>
```

---

### 5. 📊 Datos Abiertos por Defecto

**Principio:** *La información pública se expone en formatos reutilizables*

#### Formatos Soportados:
- **JSON** (por defecto)
- **CSV** (para tablas)
- **XML** (compatibilidad)
- **RDF** (datos enlazados - opcional)

#### APIs Públicas:
- `/api/v1/decretos` → JSON
- `/api/v1/decretos?format=csv` → CSV
- `/api/v1/contratos` → JSON
- `/api/v1/presupuesto` → JSON

#### Negociación de Contenido:
```http
GET /api/v1/decretos HTTP/1.1
Accept: application/json
Accept: text/csv
Accept: application/xml
```

#### Documentación:
- OpenAPI/Swagger para todas las APIs
- Ejemplos de uso
- Rate limiting documentado

---

### 6. 🔄 Actualización Permanente

**Principio:** *Transparencia actualizada al menos mensualmente*

#### Automatización:
- Scheduler de Laravel para tareas programadas
- Notificaciones de vencimiento
- Recordatorios automáticos

#### Frecuencias Mínimas:
- **Decretos/Gacetas:** Al emitirse
- **Contratos:** Mensual (sincronización SECOP)
- **Presupuesto:** Mensual (ejecución)
- **Datos abiertos:** Mensual
- **Noticias:** Semanal recomendado

#### Validación:
```php
// Scheduler en app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Recordatorio actualización mensual
    $schedule->command('transparencia:recordar-actualizacion')
             ->monthly();
    
    // Sincronización SECOP
    $schedule->command('contratos:sincronizar-secop')
             ->monthlyOn(1, '02:00');
}
```

---

### 7. 🔧 Mantenibilidad y Evolución

**Principio:** *Código mantenible hoy y mañana*

#### Estándares de Código:

**Backend (PHP):**
- **PSR-12** estricto
- PHPStan level 8+
- PHP_CodeSniffer
- Nombres descriptivos en español

**Frontend (TypeScript/Vue):**
- ESLint + Prettier
- TypeScript strict mode
- Vue style guide oficial
- Componentes reutilizables

#### Tests Automatizados:
- **Coverage mínimo:** 80% backend, 70% frontend
- Tests unitarios obligatorios
- Tests de integración para APIs
- Tests E2E para flujos críticos

#### Code Review:
- Pull Requests obligatorios
- Aprobación de al menos 1 revisor
- Checklist de revisión
- CI/CD automático (tests + linting)

#### Documentación:
- README actualizado
- Comentarios en lógica compleja
- ADRs para decisiones arquitectónicas
- Changelog actualizado

---

### 8. 🧠 Contexto Compartido

**Principio:** *Asegurar continuidad entre personas y herramientas de IA*

#### Documentación Estructurada:
- **CONTEXT.md** actualizado en cada iteración
- **ADRs** (Architecture Decision Records) para decisiones importantes
- **README** con setup actualizado
- Diagrams de arquitectura

#### Formato ADR:
```markdown
# ADR-XXX: [Título de la Decisión]

**Fecha:** 2026-02-17
**Estado:** Aceptado | Rechazado | Deprecado
**Contexto:** Qué problema se resuelve
**Decisión:** Qué se decidió
**Consecuencias:** Pros y contras
**Alternativas:** Qué otras opciones había
```

#### Actualización de CONTEXT.md:
- Estado actual del proyecto
- Decisiones recientes
- Próximos pasos
- Deuda técnica conocida
- Problemas sin resolver

---

## 1.3 Stack Tecnológico

### Backend

#### Framework y Lenguaje
- **Framework:** Laravel 12
- **PHP:** 8.3.1 o superior
- **Características:** Type hints estrictos, enums, atributos

#### Base de Datos
- **Motor:** MySQL 8.0+
- **Storage Engine:** InnoDB
- **Charset:** utf8mb4 (soporte emojis)
- **Normalización:** 4FN (Cuarta Forma Normal)
- **Tablas:** 57 tablas optimizadas
- **Índices:** 80+ índices estratégicos

#### Autenticación
- **Package:** Laravel Sanctum
- **Tipo:** Token-based con cookies HTTP-Only
- **Sesiones:** Stateless para API
- **MFA:** Opcional (futuro)

#### Autorización
- **Package:** Spatie Laravel Permission
- **Modelo:** RBAC (Role-Based Access Control)
- **Roles:** Super Admin, Administrador, Editor, Autor, Funcionario, Ciudadano
- **Permisos:** Granulares por recurso y acción

#### Auditoría
- **Package:** Spatie Activity Log
- **Registro:** Todas las acciones CRUD
- **Datos:** Usuario, IP, timestamp, cambios
- **Retención:** Permanente (tabla `audit_logs`)

#### Almacenamiento
- **Local:** Sistema de archivos (`storage/app/`)
- **Patrón:** `storage/{tipo}/{año}/{archivo}`
- **Cloud:** S3-compatible (opcional, producción)
- **Públicos:** En `/storage` con symlink

#### Caché
- **Driver:** Redis
- **Uso:** Query caching, session caching, application caching
- **TTL:** Configurable por tipo de dato
- **Invalidación:** Automática en cambios

#### Validación
- **Método:** FormRequest classes
- **Reglas:** Declarativas, reutilizables
- **Mensajes:** En español
- **Custom:** Validators personalizados cuando necesario

#### API
- **Tipo:** RESTful
- **Versión:** v1 (`/api/v1/`)
- **Formato:** JSON (por defecto)
- **Paginación:** Obligatoria (máx 100 items)
- **Rate Limiting:** Configurado por role
- **CORS:** Configurado apropiadamente

---

### Frontend

#### Framework y Lenguaje
- **Framework:** Vue 3
- **API:** Composition API (setup script)
- **Lenguaje:** TypeScript
- **Mode:** Strict mode
- **Version:** Vue 3.4+

#### Build Tool
- **Tool:** Vite 5+
- **Features:** HMR, code splitting, tree shaking
- **Plugins:** Vue, TypeScript

#### State Management
- **Library:** Pinia
- **Stores:** Por dominio (auth, content, ui, etc.)
- **Persistencia:** localStorage para sesión
- **DevTools:** Integrado

#### HTTP Client
- **Library:** Axios
- **Interceptors:** Auth token, error handling
- **Base URL:** Configurable
- **Timeout:** Configurado

#### Validación
- **Library:** VeeValidate 4+
- **Schema:** Yup
- **Mensajes:** En español
- **Validación:** Client-side + server-side

#### UI Framework
- **CSS:** Bootstrap 5.3+
- **Icons:** Font Awesome Free 6+
- **Responsivo:** Mobile-first
- **Tema:** Personalizable (colores alcaldía)

#### Routing
- **Library:** Vue Router 4+
- **Mode:** History mode
- **Guards:** Authentication, permissions
- **Lazy Loading:** Por ruta

#### Testing
- **Unit:** Vitest
- **Components:** @vue/test-utils
- **E2E:** Cypress (opcional)
- **Coverage:** 70% mínimo

---

### Infraestructura

#### Servidor Web
- **Software:** Nginx 1.24+
- **Configuración:** Optimizada para Laravel
- **Gzip:** Habilitado
- **HTTP/2:** Habilitado

#### PHP
- **Version:** PHP-FPM 8.3+
- **Pool:** Configurado para carga
- **Memory:** 256MB mínimo
- **Max execution time:** 60s

#### Base de Datos
- **Version:** MySQL 8.0+
- **Engine:** InnoDB
- **Buffer Pool:** Optimizado
- **Slow Query Log:** Habilitado

#### Caché y Queue
- **Redis:** v7+
- **Uso:** Cache + Queue + Session
- **Persistencia:** Configurada

#### Storage
- **Local:** Para desarrollo y staging
- **S3:** Para producción (AWS/DigitalOcean Spaces)
- **CDN:** Opcional (CloudFlare)

#### SSL/TLS
- **Provider:** Let's Encrypt
- **Auto-renewal:** Certbot
- **Grade:** A+ en SSL Labs
- **Protocols:** TLS 1.2, TLS 1.3

#### Logs
- **Aplicación:** Laravel logs (`storage/logs/`)
- **Servidor:** Nginx access/error logs
- **Centralización:** Opcional (Graylog/ELK)
- **Rotación:** Diaria

#### Backups
- **Frecuencia:** Diaria
- **Retención:** 30 días
- **Tipo:** Base de datos + archivos
- **Storage:** Offsite (S3)
- **Tests:** Restauración mensual

---

### DevOps

#### Control de Versiones
- **Sistema:** Git
- **Hosting:** GitHub
- **Branching:** GitFlow
- **Commits:** Conventional commits

#### CI/CD
- **Platform:** GitHub Actions
- **Pipelines:**
  - Lint (ESLint, PHP_CodeSniffer)
  - Tests (PHPUnit, Vitest)
  - Build (Vite)
  - Deploy (staging/production)

#### Testing
- **Backend:** PHPUnit
- **Frontend:** Vitest + Cypress
- **Coverage:** Coveralls
- **Quality:** SonarQube (opcional)

#### Code Quality
- **PHP:** PHPStan level 8+, PHP_CodeSniffer (PSR-12)
- **TypeScript:** ESLint + Prettier
- **Pre-commit:** Husky + lint-staged

#### Documentación
- **Código:** Inline comments
- **API:** OpenAPI/Swagger
- **Arquitectura:** ADRs + Diagrams
- **Usuario:** Markdown en `/docs`

#### Monitoreo
- **Uptime:** UptimeRobot (opcional)
- **Errors:** Sentry (opcional)
- **Performance:** New Relic/Datadog (opcional)
- **Logs:** Centralizados

---

## ✅ Checklist de Constitución

### Propósito y Alcance
- [x] Objetivo general definido
- [x] 7 normativas identificadas
- [x] 5 perfiles de usuario especificados
- [x] Alcance del proyecto claro

### Principios Rectores
- [x] 8 principios no negociables definidos
- [x] Ejemplos de aplicación proporcionados
- [x] Criterios de aceptación establecidos

### Stack Tecnológico
- [x] Backend: Laravel 12 + MySQL
- [x] Frontend: Vue 3 + TypeScript
- [x] Infraestructura definida
- [x] DevOps configurado

---

## 📚 Referencias

- [CONTEXT.md](./CONTEXT.md) - Contexto compartido del proyecto
- [NORMATIVAS_CUMPLIMIENTO.md](./NORMATIVAS_CUMPLIMIENTO.md) - Checklist de cumplimiento
- [ADRs/](./ADRs/) - Architecture Decision Records
- [DATABASE_ARCHITECTURE.md](./DATABASE_ARCHITECTURE.md) - Arquitectura de base de datos

---

## 📝 Historial de Cambios

| Fecha | Versión | Cambio |
|-------|---------|--------|
| 2026-02-17 | 1.0.0 | Creación del documento - Fase 1 completa |

---

**Documento:** FASE_1_CONSTITUCION_PROYECTO.md  
**Versión:** 1.0.0  
**Fecha:** 2026-02-17  
**Estado:** ✅ Aprobado  
**Autor:** Equipo de Desarrollo CMS Gubernamental
