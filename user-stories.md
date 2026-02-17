# Historias de Usuario - Portal de Configuración VPS

## Introducción
Este documento contiene las historias de usuario para el Portal de Configuración VPS v2.0, una aplicación web full-stack con Laravel 12 + Vue.js 3 para documentación y gestión centralizada de configuración de servidores VPS.

---

## HU-001: Registro de Usuario
**Como** visitante del portal  
**Quiero** crear una cuenta de usuario  
**Para** acceder a las funcionalidades de documentación y gestión

### Criterios de Aceptación:
- Formulario de registro con: nombre, email, contraseña, confirmación
- Validación en tiempo real con VeeValidate
- Backend valida unicidad de email
- Contraseña debe cumplir requisitos de seguridad (min 8 caracteres)
- Confirmación vía email (opcional v1.0)
- Redirección automática al dashboard tras registro exitoso
- Mensajes de error claros y específicos

### Prioridad: Alta
### Estimación: 8 horas

---

## HU-002: Inicio de Sesión
**Como** usuario registrado  
**Quiero** iniciar sesión en el portal  
**Para** acceder a mi cuenta y contenido personalizado

### Criterios de Aceptación:
- Formulario de login con email y contraseña
- Autenticación mediante Laravel Sanctum con cookies HTTP-Only
- Opción "Recordarme" (opcional)
- Validación de credenciales en backend
- Redirección al dashboard tras login exitoso
- Mensaje de error si credenciales incorrectas
- Protección contra ataques de fuerza bruta (rate limiting)

### Prioridad: Alta
### Estimación: 6 horas

---

## HU-003: Gestión de Perfil
**Como** usuario autenticado  
**Quiero** editar mi perfil  
**Para** actualizar mi información personal

### Criterios de Aceptación:
- Vista de perfil con información del usuario
- Editar: nombre, email, biografía
- Cambiar contraseña (requiere contraseña actual)
- Avatar personalizado (upload de imagen)
- Validación de datos
- Confirmación de cambios guardados
- Opción de eliminar cuenta (con confirmación)

### Prioridad: Media
### Estimación: 10 horas

---

## HU-004: Navegación en el Portal
**Como** usuario autenticado  
**Quiero** navegar fácilmente por el portal  
**Para** encontrar rápidamente la información que necesito

### Criterios de Aceptación:
- Sidebar con categorías organizadas (dinámico desde API)
- Navegación con Vue Router (SPA sin recargas)
- Breadcrumbs en todas las páginas
- Menú colapsable en desktop
- Menú hamburguesa responsive en móvil
- Transiciones suaves entre vistas
- Loading states durante navegación

### Prioridad: Alta
### Estimación: 12 horas

---

## HU-005: Visualizar Documentación
**Como** usuario autenticado  
**Quiero** visualizar artículos de documentación  
**Para** aprender sobre configuración de VPS

### Criterios de Aceptación:
- Vista de documentación con markdown renderizado
- Tabla de contenidos automática
- Categoría y tags visibles
- Información de autor y fecha
- Contador de vistas
- Botón de favorito
- Navegación prev/next entre documentos
- Syntax highlighting en bloques de código
- Responsive en todos los dispositivos

### Prioridad: Alta
### Estimación: 8 horas

---

## HU-006: Crear Documentación (Rol: Editor/Admin)
**Como** editor o administrador  
**Quiero** crear nuevos artículos de documentación  
**Para** compartir conocimiento con otros usuarios

### Criterios de Aceptación:
- Formulario de creación accesible según rol
- Editor markdown con vista previa en tiempo real
- Campos: título, slug, contenido, resumen, categoría, tags
- Upload de imágenes arrastrando o seleccionando
- Auto-guardado cada 30 segundos (opcional)
- Validación de campos requeridos
- Preview antes de publicar
- Estado: borrador o publicado
- Redirección al artículo tras guardar

### Prioridad: Alta
### Estimación: 16 horas

---

## HU-007: Editar Documentación (Rol: Editor/Admin)
**Como** editor o administrador  
**Quiero** editar artículos existentes  
**Para** mantener la documentación actualizada

### Criterios de Aceptación:
- Formulario de edición pre-poblado con datos actuales
- Editor markdown con preview
- Guardar cambios crea nueva versión (historial)
- Validación de permisos (solo creador o admin)
- Opción de eliminar artículo (con confirmación)
- Notificación de éxito/error
- Historial de cambios visible

### Prioridad: Alta
### Estimación: 14 horas

---

## HU-008: Búsqueda Avanzada
**Como** usuario autenticado  
**Quiero** buscar documentación específica  
**Para** encontrar rápidamente la información que necesito

### Criterios de Aceptación:
- Barra de búsqueda global visible en header
- Búsqueda full-text en título y contenido
- Autocompletado con sugerencias
- Filtros: categoría, tags, fecha, autor
- Resultados paginados
- Highlight de términos buscados
- Ordenamiento: relevancia, fecha, popularidad
- Vista de resultados responsive
- Historial de búsquedas (local)

### Prioridad: Alta
### Estimación: 16 horas

---

## HU-009: Sistema de Favoritos
**Como** usuario autenticado  
**Quiero** marcar documentos como favoritos  
**Para** acceder rápidamente a contenido que uso frecuentemente

### Criterios de Aceptación:
- Botón de favorito (corazón) en cada documento
- Toggle favorito con feedback visual
- Sincronización con backend API
- Vista de "Mis Favoritos" con todos los marcados
- Contador de favoritos visible
- Organización por categorías o colecciones
- Búsqueda dentro de favoritos
- Funciona offline con sincronización posterior

### Prioridad: Media
### Estimación: 12 horas

---

## HU-010: Dashboard Personalizado
**Como** usuario autenticado  
**Quiero** ver un dashboard personalizado  
**Para** tener una vista general de mi actividad

### Criterios de Aceptación:
- Tarjetas de estadísticas según rol
- Documentos recientes visitados
- Documentos favoritos destacados
- Actividad reciente
- Accesos rápidos personalizados
- Diferentes vistas según rol (viewer vs admin)
- Gráficos de actividad (opcional)
- Responsive y optimizado

### Prioridad: Alta
### Estimación: 14 horas

---

## HU-011: Gestión de Categorías (Rol: Admin)
**Como** administrador  
**Quiero** gestionar categorías de documentación  
**Para** mantener el contenido organizado

### Criterios de Aceptación:
- Vista de listado de categorías
- Crear nueva categoría (nombre, slug, descripción, icono)
- Editar categoría existente
- Eliminar categoría (solo si no tiene documentos)
- Ordenar categorías (drag & drop opcional)
- Asignar color a cada categoría
- API CRUD completa
- Validación de permisos

### Prioridad: Media
### Estimación: 10 horas

---

## HU-012: Gestión de Usuarios (Rol: Admin)
**Como** administrador  
**Quiero** gestionar usuarios del sistema  
**Para** controlar el acceso y los roles

### Criterios de Aceptación:
- Vista de listado de usuarios con filtros
- Ver detalles de usuario
- Editar usuario (nombre, email, rol)
- Suspender/activar usuario
- Eliminar usuario (con confirmación)
- Asignar/cambiar roles
- Ver actividad del usuario
- Búsqueda por nombre o email
- Paginación de resultados

### Prioridad: Alta
### Estimación: 16 horas

---

## HU-013: Gestión de Roles y Permisos (Rol: Super Admin)
**Como** super administrador  
**Quiero** configurar roles y permisos  
**Para** establecer niveles de acceso personalizados

### Criterios de Aceptación:
- Vista de roles existentes (SuperAdmin, Admin, Editor, Viewer)
- Crear nuevo rol personalizado
- Asignar permisos a roles (matriz de permisos)
- Permisos granulares: ver, crear, editar, eliminar documentos
- Permisos de administración
- Editar rol existente
- No se puede eliminar roles en uso
- Validación de permisos en frontend y backend

### Prioridad: Media
### Estimación: 14 horas

---

## HU-014: Versionamiento de Documentos
**Como** editor o administrador  
**Quiero** ver el historial de versiones de un documento  
**Para** rastrear cambios y restaurar versiones anteriores

### Criterios de Aceptación:
- Cada edición crea nueva versión automáticamente
- Vista de historial con lista de versiones
- Información de cada versión: fecha, autor, resumen de cambios
- Comparación visual entre versiones (diff)
- Restaurar versión anterior
- Versión actual claramente identificada
- No se pueden editar versiones antiguas directamente

### Prioridad: Baja
### Estimación: 14 horas

---

## HU-015: Upload de Archivos
**Como** editor o administrador  
**Quiero** subir imágenes y archivos  
**Para** enriquecer la documentación

### Criterios de Aceptación:
- Upload mediante drag & drop o selector
- Tipos permitidos: jpg, png, gif, pdf (configurable)
- Tamaño máximo: 5MB por archivo
- Preview de imagen antes de upload
- Progress bar durante upload
- Validación de tipo y tamaño
- Almacenamiento en DigitalOcean Spaces (S3)
- URL pública para insertar en markdown
- Gestión de archivos subidos (galería)

### Prioridad: Media
### Estimación: 12 horas

---

## HU-016: Notificaciones en Tiempo Real
**Como** usuario autenticado  
**Quiero** recibir notificaciones de acciones importantes  
**Para** estar informado de cambios relevantes

### Criterios de Aceptación:
- Toast notifications para: éxito, error, advertencia, info
- Notificaciones persisten 3-5 segundos
- Posición configurable (default: top-right)
- No bloquean la interfaz
- Pueden ser cerradas manualmente
- Diferentes iconos según tipo
- Animaciones suaves de entrada/salida

### Prioridad: Baja
### Estimación: 4 horas

---

## HU-017: Visualización Responsive
**Como** usuario en cualquier dispositivo  
**Quiero** usar el portal desde móvil, tablet o desktop  
**Para** acceder desde cualquier lugar

### Criterios de Aceptación:
- Diseño responsive con breakpoints: 320px, 768px, 992px, 1200px
- Sidebar colapsable en mobile (menú hamburguesa)
- Tablas con scroll horizontal en mobile
- Imágenes responsive
- Botones y elementos touch-friendly en mobile
- Formularios optimizados para mobile
- Testing en Chrome, Firefox, Safari mobile
- Performance optimizado en mobile

### Prioridad: Alta
### Estimación: 12 horas

---

## HU-018: Analytics y Estadísticas (Rol: Admin)
**Como** administrador  
**Quiero** ver estadísticas de uso del portal  
**Para** entender el comportamiento de los usuarios

### Criterios de Aceptación:
- Dashboard de analytics
- Total: usuarios, documentos, categorías, visitas
- Gráficos de actividad (últimos 30 días)
- Documentos más vistos
- Usuarios más activos
- Categorías más populares
- Exportación de reportes (CSV/PDF opcional)
- Filtros por rango de fechas

### Prioridad: Baja
### Estimación: 16 horas

---

## HU-019: Logs de Actividad (Rol: Admin)
**Como** administrador  
**Quiero** ver logs de actividad del sistema  
**Para** auditar acciones y detectar problemas

### Criterios de Aceptación:
- Vista de logs con tabla paginada
- Información: usuario, acción, recurso, IP, timestamp
- Filtros: usuario, tipo de acción, fecha
- Búsqueda en logs
- Acciones registradas: login, logout, CRUD documentos, cambios de roles
- Exportación de logs
- Paginación eficiente
- Retención de logs: 90 días

### Prioridad: Baja
### Estimación: 10 horas

---

## HU-020: Recuperación de Contraseña
**Como** usuario que olvidó su contraseña  
**Quiero** recuperar acceso a mi cuenta  
**Para** poder ingresar nuevamente

### Criterios de Aceptación:
- Enlace "¿Olvidaste tu contraseña?" en login
- Formulario solicita email
- Backend envía email con token de reset
- Link en email lleva a formulario de nueva contraseña
- Token expira en 1 hora
- Nueva contraseña debe cumplir requisitos
- Confirmación de contraseña
- Mensaje de éxito tras resetear
- Redirección a login

### Prioridad: Media
### Estimación: 10 horas

---

## Resumen de Prioridades

### Alta Prioridad (Críticas) - 12 historias
- HU-001: Registro de Usuario
- HU-002: Inicio de Sesión
- HU-004: Navegación en el Portal
- HU-005: Visualizar Documentación
- HU-006: Crear Documentación
- HU-007: Editar Documentación
- HU-008: Búsqueda Avanzada
- HU-010: Dashboard Personalizado
- HU-012: Gestión de Usuarios
- HU-017: Visualización Responsive

### Media Prioridad (Importantes) - 6 historias
- HU-003: Gestión de Perfil
- HU-009: Sistema de Favoritos
- HU-011: Gestión de Categorías
- HU-013: Gestión de Roles y Permisos
- HU-015: Upload de Archivos
- HU-020: Recuperación de Contraseña

### Baja Prioridad (Deseables) - 4 historias
- HU-014: Versionamiento de Documentos
- HU-016: Notificaciones en Tiempo Real
- HU-018: Analytics y Estadísticas
- HU-019: Logs de Actividad

---

## Matriz de Roles

| Historia de Usuario | Viewer | Editor | Admin | SuperAdmin |
|-------------------|--------|--------|-------|------------|
| HU-001: Registro | ✅ | ✅ | ✅ | ✅ |
| HU-002: Login | ✅ | ✅ | ✅ | ✅ |
| HU-003: Perfil | ✅ | ✅ | ✅ | ✅ |
| HU-004: Navegación | ✅ | ✅ | ✅ | ✅ |
| HU-005: Ver Docs | ✅ | ✅ | ✅ | ✅ |
| HU-006: Crear Docs | ❌ | ✅ | ✅ | ✅ |
| HU-007: Editar Docs | ❌ | ✅* | ✅ | ✅ |
| HU-008: Búsqueda | ✅ | ✅ | ✅ | ✅ |
| HU-009: Favoritos | ✅ | ✅ | ✅ | ✅ |
| HU-010: Dashboard | ✅ | ✅ | ✅ | ✅ |
| HU-011: Categorías | ❌ | ❌ | ✅ | ✅ |
| HU-012: Usuarios | ❌ | ❌ | ✅ | ✅ |
| HU-013: Roles | ❌ | ❌ | ❌ | ✅ |
| HU-014: Versiones | ❌ | ✅ | ✅ | ✅ |
| HU-015: Uploads | ❌ | ✅ | ✅ | ✅ |
| HU-016: Notificaciones | ✅ | ✅ | ✅ | ✅ |
| HU-017: Responsive | ✅ | ✅ | ✅ | ✅ |
| HU-018: Analytics | ❌ | ❌ | ✅ | ✅ |
| HU-019: Logs | ❌ | ❌ | ✅ | ✅ |
| HU-020: Reset Password | ✅ | ✅ | ✅ | ✅ |

*Editor solo puede editar sus propios documentos

---

## Estimación Total

- **Total de Historias:** 20
- **Estimación Total:** 238 horas
- **Alta Prioridad:** 142 horas
- **Media Prioridad:** 66 horas
- **Baja Prioridad:** 30 horas

---

## Notas de Implementación

1. **Autenticación**: Implementar primero (HU-001, HU-002) como base
2. **CRUD Básico**: Implementar HU-005, HU-006, HU-007 en secuencia
3. **Roles**: HU-013 debe implementarse antes de HU-012
4. **Testing**: Cada historia requiere tests unitarios y de integración
5. **Responsive**: HU-017 es transversal a todas las historias
6. **Seguridad**: Validar permisos en backend para todas las acciones
