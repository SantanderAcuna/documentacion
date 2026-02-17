# Reglas de Negocio - Portal de Configuración VPS

## Introducción
Este documento define las reglas de negocio que rigen el funcionamiento del Portal de Configuración VPS v2.0 (Laravel 12 + Vue.js 3). Estas reglas aseguran que la aplicación cumpla con los requisitos técnicos, de seguridad, usabilidad y escalabilidad necesarios.

---

## 1. Reglas de Autenticación y Sesiones

### RN-001: Autenticación Obligatoria
**Regla:** Todos los endpoints de la API (excepto login, register, password reset) requieren autenticación válida.

**Descripción:**
- Laravel Sanctum con cookies HTTP-Only
- CSRF protection habilitado
- Token debe ser válido y no expirado
- Sesión debe estar activa

**Impacto:** Crítico  
**Justificación:** Protege datos sensibles y garantiza que solo usuarios autenticados accedan al sistema

---

### RN-002: Política de Contraseñas
**Regla:** Las contraseñas deben cumplir requisitos mínimos de seguridad.

**Descripción:**
- Mínimo 8 caracteres
- Al menos 1 letra mayúscula
- Al menos 1 letra minúscula
- Al menos 1 número
- Caracteres especiales recomendados
- No puede ser igual a contraseñas anteriores (últimas 3)
- Hash con bcrypt (Laravel default)

**Impacto:** Crítico  
**Justificación:** Previene accesos no autorizados y ataques de fuerza bruta

---

### RN-003: Expiración de Sesiones
**Regla:** Las sesiones de usuario deben expirar después de un período de inactividad.

**Descripción:**
- Tiempo de inactividad: 120 minutos (configurable)
- Warning al usuario 5 minutos antes de expirar
- Logout automático tras expiración
- Opción "Recordarme" extiende a 30 días

**Impacto:** Alto  
**Justificación:** Balance entre seguridad y experiencia de usuario

---

### RN-004: Rate Limiting
**Regla:** Se debe limitar la cantidad de requests para prevenir abusos.

**Descripción:**
- Login: 5 intentos por minuto por IP
- API general: 60 requests por minuto por usuario
- Búsqueda: 20 requests por minuto por usuario
- Uploads: 10 archivos por hora por usuario
- Respuesta HTTP 429 cuando se excede

**Impacto:** Alto  
**Justificación:** Previene ataques DoS y abuso de recursos

---

## 2. Reglas de Autorización y Permisos

### RN-005: Sistema RBAC Obligatorio
**Regla:** Todas las acciones deben validar roles y permisos mediante Spatie Permission.

**Descripción:**
- Roles definidos: SuperAdmin, Admin, Editor, Viewer
- Permisos granulares por recurso y acción
- Validación en middleware de Laravel
- Validación adicional en frontend (UI)
- SuperAdmin tiene todos los permisos

**Impacto:** Crítico  
**Justificación:** Control de acceso preciso y seguridad por capas

---

### RN-006: Jerarquía de Roles
**Regla:** Los roles tienen una jerarquía que determina sus capacidades.

**Descripción:**
- SuperAdmin > Admin > Editor > Viewer
- Solo SuperAdmin puede crear/modificar roles
- Solo Admin+ puede gestionar usuarios
- Solo Editor+ puede crear/editar documentación
- Viewer solo puede leer documentación

**Impacto:** Alto  
**Justificación:** Organización clara de responsabilidades

---

### RN-007: Ownership de Recursos
**Regla:** Los Editores solo pueden modificar/eliminar sus propios recursos.

**Descripción:**
- Editor puede editar sus propios documentos
- Editor NO puede editar documentos de otros
- Admin puede editar todos los documentos
- Validación en Policy de Laravel
- UI oculta opciones no permitidas

**Impacto:** Alto  
**Justificación:** Previene modificación no autorizada de contenido

---

## 3. Reglas de Contenido y Documentación

### RN-008: Validación de Contenido
**Regla:** Todo contenido debe ser validado antes de ser guardado.

**Descripción:**
- Título: requerido, máximo 255 caracteres, único
- Slug: generado automáticamente, único, URL-friendly
- Contenido: requerido, mínimo 50 caracteres, markdown válido
- Categoría: requerida, debe existir en BD
- Tags: opcional, máximo 10 tags por documento
- Estado: draft o published

**Impacto:** Alto  
**Justificación:** Mantiene calidad y consistencia del contenido

---

### RN-009: Sanitización de HTML
**Regla:** El contenido HTML generado desde markdown debe ser sanitizado.

**Descripción:**
- Usar librería de sanitización (DOMPurify en frontend)
- Permitir solo tags seguros
- Remover scripts maliciosos
- Escape de entidades HTML
- No permitir iframes sin whitelist

**Impacto:** Crítico  
**Justificación:** Previene ataques XSS

---

### RN-010: Versionamiento Automático
**Regla:** Cada modificación de un documento debe crear una nueva versión.

**Descripción:**
- Guardar copia completa en tabla document_versions
- Registrar: user_id, contenido, timestamp
- Retener últimas 50 versiones por documento
- Versiones más antiguas se archivan/eliminan
- Solo Admin+ puede restaurar versiones

**Impacto:** Medio  
**Justificación:** Permite rastrear cambios y recuperar contenido

---

### RN-011: Categorías Obligatorias
**Regla:** Todo documento debe pertenecer a exactamente una categoría.

**Descripción:**
- Categoría es campo requerido
- No se puede eliminar categoría con documentos
- Debe reasignar documentos antes de eliminar
- Categorías predefinidas: SSH, Seguridad, Web, Linux

**Impacto:** Medio  
**Justificación:** Mantiene organización y navegación clara

---

## 4. Reglas de Almacenamiento y Uploads

### RN-012: Validación de Archivos
**Regla:** Los archivos subidos deben cumplir requisitos de seguridad.

**Descripción:**
- Tipos permitidos: jpg, jpeg, png, gif, pdf
- Tamaño máximo: 5MB por archivo
- Validar MIME type real (no solo extensión)
- Escanear archivos con antivirus (opcional)
- Renombrar con UUID para evitar conflictos
- Almacenar metadata en base de datos

**Impacto:** Alto  
**Justificación:** Previene malware y abuso de almacenamiento

---

### RN-013: Almacenamiento en S3/Spaces
**Regla:** Los archivos de producción deben almacenarse en DigitalOcean Spaces.

**Descripción:**
- Desarrollo: almacenamiento local
- Producción: DigitalOcean Spaces
- Configurar disco dinámico en Laravel
- URLs públicas para archivos aprobados
- Bucket privado, acceso controlado
- CDN habilitado para performance

**Impacto:** Medio  
**Justificación:** Escalabilidad y disponibilidad de archivos

---

### RN-014: Limpieza de Archivos Huérfanos
**Regla:** Los archivos no referenciados deben ser eliminados periódicamente.

**Descripción:**
- Job programado diariamente
- Identificar archivos sin referencias en BD
- Retener 7 días antes de eliminar
- Log de archivos eliminados
- Notificar a admins de limpieza

**Impacto:** Bajo  
**Justificación:** Optimiza uso de almacenamiento

---

## 5. Reglas de Búsqueda y Filtrado

### RN-015: Búsqueda Full-Text
**Regla:** La búsqueda debe implementarse con full-text search de MySQL.

**Descripción:**
- Índice FULLTEXT en columnas: title, content
- Búsqueda en modo NATURAL LANGUAGE
- Mínimo 3 caracteres para buscar
- Resultados ordenados por relevancia
- Paginación: 20 resultados por página

**Impacto:** Alto  
**Justificación:** Performance y relevancia en búsquedas

---

### RN-016: Filtros Acumulativos
**Regla:** Los filtros de búsqueda deben ser acumulativos (AND).

**Descripción:**
- Filtro por categoría + tags + fecha = AND
- UI debe mostrar filtros activos
- Opción de limpiar todos los filtros
- Filtros persisten en URL (query params)
- Sincronización con Vue Router

**Impacto:** Medio  
**Justificación:** Búsquedas más precisas y navegación compartible

---

## 6. Reglas de Performance y Cache

### RN-017: Cache de Queries Frecuentes
**Regla:** Las queries frecuentes deben cachearse en Redis.

**Descripción:**
- Cache de listados: 5 minutos
- Cache de documentos individuales: 15 minutos
- Cache de categorías: 1 hora
- Invalidar cache al modificar datos
- Usar cache tags para invalidación selectiva

**Impacto:** Alto  
**Justificación:** Reduce carga en base de datos y mejora performance

---

### RN-018: Eager Loading Obligatorio
**Regla:** Se debe usar eager loading para evitar problema N+1.

**Descripción:**
- Cargar relaciones con `with()` en Eloquent
- Evitar lazy loading en loops
- Usar query builder cuando sea más eficiente
- Monitorear queries con Laravel Debugbar (dev)
- Alertar si queries > 50 en una página

**Impacto:** Alto  
**Justificación:** Performance crítica de la aplicación

---

### RN-019: Paginación Obligatoria
**Regla:** Todos los listados deben estar paginados.

**Descripción:**
- Máximo 50 items por página
- Default: 20 items por página
- Usar cursor pagination para grandes datasets
- Incluir meta information (total, pages)
- Links de navegación en respuesta

**Impacto:** Alto  
**Justificación:** Evita sobrecarga de memoria y mejora UX

---

## 7. Reglas de Seguridad

### RN-020: HTTPS Obligatorio en Producción
**Regla:** Toda comunicación en producción debe ser sobre HTTPS.

**Descripción:**
- Certificados SSL/TLS válidos (Let's Encrypt)
- HSTS header habilitado
- Redirección automática HTTP → HTTPS
- Cookies con flag Secure
- Mixed content no permitido

**Impacto:** Crítico  
**Justificación:** Protege datos en tránsito

---

### RN-021: CORS Configurado Restrictivamente
**Regla:** CORS debe permitir solo orígenes autorizados.

**Descripción:**
- Whitelist de dominios permitidos
- Credentials: true (para cookies)
- Métodos: GET, POST, PUT, DELETE, PATCH
- Headers permitidos definidos
- No usar wildcard (*) en producción

**Impacto:** Alto  
**Justificación:** Previene requests no autorizados

---

### RN-022: SQL Injection Prevention
**Regla:** Todas las queries deben usar prepared statements.

**Descripción:**
- Usar Eloquent ORM o Query Builder
- NUNCA concatenar SQL manualmente
- Usar bindings para parámetros
- Validar input antes de queries
- Escapar output cuando necesario

**Impacto:** Crítico  
**Justificación:** Previene uno de los ataques más comunes

---

### RN-023: Logs de Seguridad
**Regla:** Los eventos de seguridad deben ser registrados.

**Descripción:**
- Log de login exitoso/fallido
- Log de cambios de permisos
- Log de accesos denegados
- Log de cambios en configuración
- Incluir: user_id, IP, timestamp, acción
- Retención: 90 días

**Impacto:** Alto  
**Justificación:** Auditoría y detección de amenazas

---

## 8. Reglas de API y Comunicación

### RN-024: Formato de Respuestas API
**Regla:** Todas las respuestas de API deben seguir formato estándar.

**Descripción:**
```json
{
  "success": true|false,
  "data": {...} | [...],
  "message": "string",
  "errors": {...},
  "meta": {
    "total": 100,
    "current_page": 1,
    "per_page": 20
  }
}
```
- Status codes HTTP apropiados
- Mensajes descriptivos
- Estructura consistente

**Impacto:** Alto  
**Justificación:** Facilita consumo de API y debugging

---

### RN-025: Versionamiento de API
**Regla:** La API debe tener versionamiento para cambios breaking.

**Descripción:**
- Versión en URL: /api/v1/
- Mantener v1 mientras haya clientes
- Documentar cambios en changelog
- Deprecation notice 3 meses antes
- Versión actual siempre: /api/

**Impacto:** Medio  
**Justificación:** Permite evolución sin romper clientes

---

## 9. Reglas de Frontend

### RN-026: Validación Dual (Frontend + Backend)
**Regla:** Toda validación en frontend debe replicarse en backend.

**Descripción:**
- Frontend: VeeValidate + Yup (UX rápida)
- Backend: Form Requests (seguridad)
- NUNCA confiar solo en validación frontend
- Mensajes de error consistentes
- Códigos de validación estandarizados

**Impacto:** Crítico  
**Justificación:** Frontend puede ser bypasseado

---

### RN-027: Loading States Obligatorios
**Regla:** Toda operación asíncrona debe mostrar feedback visual.

**Descripción:**
- Spinners durante fetch de datos
- Skeleton screens en cargas iniciales
- Progress bars en uploads
- Disable buttons durante submit
- Mensajes de éxito/error tras completar

**Impacto:** Medio  
**Justificación:** Mejora percepción de performance y UX

---

### RN-028: Optimistic Updates con Rollback
**Regla:** Updates pueden ser optimistas pero deben tener rollback.

**Descripción:**
- Actualizar UI inmediatamente (optimistic)
- Enviar request a backend
- Si falla, revertir cambio en UI
- Mostrar mensaje de error
- Reintentar automático opcional

**Impacto:** Medio  
**Justificación:** UX fluida sin sacrificar consistencia

---

## 10. Reglas de Testing

### RN-029: Coverage Mínimo
**Regla:** El código debe mantener coverage mínimo de tests.

**Descripción:**
- Backend: 70% coverage mínimo
- Frontend: 70% coverage mínimo
- Tests unitarios para lógica de negocio
- Tests de integración para APIs
- Tests E2E para flujos críticos
- CI falla si coverage < 70%

**Impacto:** Alto  
**Justificación:** Garantiza calidad y previene regresiones

---

### RN-030: Tests Automáticos en CI
**Regla:** Todo push debe ejecutar suite de tests automáticos.

**Descripción:**
- GitHub Actions en cada push
- Tests de linting (PHP CS Fixer, ESLint)
- Tests unitarios backend + frontend
- No permitir merge si tests fallan
- Deploy solo si tests pasan

**Impacto:** Alto  
**Justificación:** Previene bugs en producción

---

## Matriz de Impacto

| Impacto | Número de Reglas | IDs |
|---------|------------------|-----|
| Crítico | 7 | RN-001, RN-002, RN-005, RN-009, RN-020, RN-022, RN-026 |
| Alto | 15 | RN-003, RN-004, RN-006, RN-007, RN-008, RN-012, RN-015, RN-017, RN-018, RN-019, RN-021, RN-023, RN-024, RN-029, RN-030 |
| Medio | 7 | RN-010, RN-011, RN-013, RN-016, RN-025, RN-027, RN-028 |
| Bajo | 1 | RN-014 |

**Total:** 30 reglas de negocio

---

## Consideraciones Especiales

### Prioridad de Implementación
Las reglas marcadas como "Crítico" son requisitos de seguridad fundamentales y deben implementarse desde el inicio. Las reglas de "Alto" impacto deben implementarse en las primeras fases del proyecto.

### Excepciones
Cualquier excepción a estas reglas debe ser documentada, justificada y aprobada por el arquitecto del proyecto. Las excepciones a reglas críticas requieren aprobación adicional del líder técnico.

### Actualización de Reglas
Este documento debe revisarse y actualizarse cada 3 meses o cuando haya cambios significativos en la arquitectura, requisitos de seguridad o regulaciones aplicables.

### Enforcement
- Reglas Críticas: Validadas en code review + CI/CD
- Reglas Alto: Validadas en code review
- Reglas Medio/Bajo: Recomendaciones fuertes

---

**Última actualización:** 2026-02-17  
**Versión del documento:** 2.0
