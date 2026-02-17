# Plan de Tareas - Portal de Configuración VPS

## Resumen Ejecutivo

- **Total de Tareas:** 40
- **Fases:** 7
- **Tiempo Estimado Total:** 482 horas (~60 días laborables)
- **Equipo Requerido:** 3-4 desarrolladores

## Distribución de Esfuerzo por Fase

| Fase | Tareas | Horas | % Total |
|------|--------|-------|---------|
| Fase 1: Configuración Inicial | 5 | 40h | 8.3% |
| Fase 2: Backend Core | 8 | 112h | 23.2% |
| Fase 3: Frontend Base | 7 | 96h | 19.9% |
| Fase 4: Integración SSH y VPS | 6 | 88h | 18.3% |
| Fase 5: Features Avanzadas | 6 | 72h | 14.9% |
| Fase 6: Testing y QA | 4 | 40h | 8.3% |
| Fase 7: Deployment y Documentación | 4 | 34h | 7.1% |

---

# Fase 1: Configuración Inicial y Setup del Proyecto
**Duración:** 1 semana | **Horas:** 40h

## T001: Configuración del Repositorio y Estructura del Proyecto
**Prioridad:** CRÍTICA  
**Estimación:** 8 horas  
**Asignado a:** Tech Lead  
**Dependencias:** Ninguna

### Descripción
Establecer la estructura base del monorepo con backend y frontend, configurar Git, y definir convenciones de código.

### Tareas Específicas
- [ ] Crear estructura de carpetas (backend/, frontend/, docs/, scripts/)
- [ ] Inicializar repositorio Git con .gitignore apropiado
- [ ] Configurar Git hooks con Husky (pre-commit, pre-push)
- [ ] Crear README.md principal con guía de inicio rápido
- [ ] Configurar Conventional Commits
- [ ] Crear plantillas de issues y PRs para GitHub

### Criterios de Aceptación
- Estructura de carpetas creada y documentada
- Git hooks funcionando correctamente
- README con instrucciones claras de setup

---

## T002: Configuración del Backend (Node.js + TypeScript)
**Prioridad:** CRÍTICA  
**Estimación:** 10 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T001

### Descripción
Configurar el proyecto backend con Node.js, TypeScript, Express y todas las herramientas de desarrollo necesarias.

### Tareas Específicas
- [ ] Inicializar proyecto Node.js con TypeScript
- [ ] Instalar y configurar Express.js
- [ ] Configurar TypeScript (tsconfig.json) con strict mode
- [ ] Configurar ESLint + Prettier para backend
- [ ] Instalar dependencias base (dotenv, cors, helmet, morgan)
- [ ] Crear estructura de carpetas (src/routes, src/controllers, src/services, src/models)
- [ ] Configurar scripts npm (dev, build, start)
- [ ] Configurar nodemon para hot reload

### Criterios de Aceptación
- Servidor Express corriendo en desarrollo
- TypeScript compilando sin errores
- Linting y formatting configurados

---

## T003: Configuración del Frontend (React + TypeScript)
**Prioridad:** CRÍTICA  
**Estimación:** 10 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T001

### Descripción
Configurar el proyecto frontend con React, TypeScript, y todas las librerías necesarias.

### Tareas Específicas
- [ ] Crear proyecto con Create React App + TypeScript
- [ ] Instalar Bootstrap 5 y configurar tema
- [ ] Instalar React Router v6
- [ ] Configurar Redux Toolkit
- [ ] Instalar Axios para llamadas HTTP
- [ ] Configurar ESLint + Prettier para frontend
- [ ] Crear estructura de carpetas (components/, pages/, store/, services/, utils/)
- [ ] Configurar variables de entorno (.env)

### Criterios de Aceptación
- Aplicación React corriendo en desarrollo
- Routing básico funcionando
- Store Redux configurado

---

## T004: Configuración de Base de Datos (PostgreSQL + Prisma)
**Prioridad:** CRÍTICA  
**Estimación:** 8 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T002

### Descripción
Configurar PostgreSQL, Prisma ORM, y crear el schema inicial de la base de datos.

### Tareas Específicas
- [ ] Instalar y configurar PostgreSQL localmente
- [ ] Inicializar Prisma en el proyecto backend
- [ ] Crear schema.prisma con modelos base (User, VPSServer, SSHKey)
- [ ] Configurar conexión a base de datos en .env
- [ ] Ejecutar primera migración
- [ ] Configurar Prisma Client
- [ ] Crear scripts de seed para datos iniciales
- [ ] Configurar Redis para cache

### Criterios de Aceptación
- Base de datos PostgreSQL corriendo
- Migraciones ejecutadas correctamente
- Prisma Client generado y funcional

---

## T005: Configuración de Docker y Docker Compose
**Prioridad:** ALTA  
**Estimación:** 4 horas  
**Asignado a:** DevOps Engineer  
**Dependencias:** T002, T003, T004

### Descripción
Crear archivos Docker y Docker Compose para desarrollo y producción.

### Tareas Específicas
- [ ] Crear Dockerfile para backend (multi-stage build)
- [ ] Crear Dockerfile para frontend
- [ ] Crear docker-compose.yml para desarrollo
- [ ] Incluir servicios: backend, frontend, postgres, redis
- [ ] Configurar volúmenes para persistencia
- [ ] Configurar networks
- [ ] Crear docker-compose.prod.yml para producción
- [ ] Documentar comandos Docker en README

### Criterios de Aceptación
- Stack completo levanta con `docker-compose up`
- Hot reload funcionando en contenedores de desarrollo
- Datos persisten entre reinicios

---

# Fase 2: Backend Core y Autenticación
**Duración:** 2.5 semanas | **Horas:** 112h

## T006: Implementar Sistema de Autenticación (JWT)
**Prioridad:** CRÍTICA  
**Estimación:** 16 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T004

### Descripción
Implementar autenticación completa con JWT, incluyendo registro, login, logout y refresh tokens.

### Tareas Específicas
- [ ] Instalar passport.js, passport-jwt, bcrypt
- [ ] Crear servicio de autenticación (AuthService)
- [ ] Implementar hash de contraseñas con bcrypt
- [ ] Implementar generación de JWT (access + refresh tokens)
- [ ] Crear middleware de autenticación
- [ ] Implementar endpoints: /auth/register, /auth/login, /auth/logout
- [ ] Implementar refresh token rotation
- [ ] Almacenar refresh tokens en Redis
- [ ] Implementar rate limiting para login

### Criterios de Aceptación
- Usuario puede registrarse con email/password
- Login devuelve access token y refresh token
- Tokens expiran correctamente
- Rate limiting previene brute force

---

## T007: Implementar Control de Acceso Basado en Roles (RBAC)
**Prioridad:** CRÍTICA  
**Estimación:** 12 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T006

### Descripción
Implementar sistema RBAC con roles y permisos granulares.

### Tareas Específicas
- [ ] Definir roles: super_admin, admin, editor, viewer
- [ ] Crear modelo Permission en base de datos
- [ ] Crear tabla intermedia Role_Permission
- [ ] Implementar middleware de autorización
- [ ] Crear decoradores para proteger rutas (@RequireRole, @RequirePermission)
- [ ] Implementar verificación de permisos por recurso
- [ ] Crear endpoints para gestión de roles y permisos
- [ ] Documentar matriz de permisos

### Criterios de Aceptación
- Diferentes roles tienen diferentes accesos
- Middleware bloquea accesos no autorizados
- Permisos verificables a nivel de ruta y recurso

---

## T008: CRUD de Usuarios
**Prioridad:** ALTA  
**Estimación:** 12 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T007

### Descripción
Implementar endpoints completos para gestión de usuarios.

### Tareas Específicas
- [ ] Crear UserController con métodos CRUD
- [ ] Implementar GET /users (con paginación, filtros, búsqueda)
- [ ] Implementar GET /users/:id
- [ ] Implementar POST /users (solo admins)
- [ ] Implementar PUT /users/:id
- [ ] Implementar DELETE /users/:id (soft delete)
- [ ] Implementar PATCH /users/:id/activate y /deactivate
- [ ] Validar inputs con Joi
- [ ] Implementar tests unitarios

### Criterios de Aceptación
- Todos los endpoints CRUD funcionando
- Validaciones correctas en inputs
- Solo usuarios autorizados pueden modificar

---

## T009: Sistema de Validación y Manejo de Errores
**Prioridad:** ALTA  
**Estimación:** 10 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T006

### Descripción
Implementar sistema robusto de validación de datos y manejo centralizado de errores.

### Tareas Específicas
- [ ] Instalar y configurar Joi para validación
- [ ] Crear middleware de validación genérico
- [ ] Crear clases de error personalizadas (AppError, ValidationError, etc.)
- [ ] Implementar middleware global de manejo de errores
- [ ] Estandarizar formato de respuestas de error
- [ ] Implementar logging de errores con Winston
- [ ] Configurar diferentes niveles de log (dev, prod)
- [ ] Integrar Sentry para tracking de errores en producción

### Criterios de Aceptación
- Inputs validados antes de procesamiento
- Errores retornados en formato consistente
- Errores loggeados apropiadamente

---

## T010: Implementar Endpoints de Recuperación de Contraseña
**Prioridad:** MEDIA  
**Estimación:** 10 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T006

### Descripción
Implementar flujo completo de "olvidé mi contraseña".

### Tareas Específicas
- [ ] Crear modelo PasswordResetToken en base de datos
- [ ] Implementar POST /auth/forgot-password
- [ ] Generar token seguro de reseteo (crypto.randomBytes)
- [ ] Enviar email con link de reseteo
- [ ] Implementar POST /auth/reset-password
- [ ] Validar token y expiración
- [ ] Permitir cambio de contraseña
- [ ] Invalidar token después de uso
- [ ] Configurar servicio de email (Nodemailer + SMTP)

### Criterios de Aceptación
- Email de recuperación enviado correctamente
- Token expira después de 1 hora
- Contraseña se actualiza correctamente

---

## T011: Implementar Sistema de Logging y Auditoría
**Prioridad:** ALTA  
**Estimación:** 14 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T006

### Descripción
Implementar sistema completo de auditoría para registrar todas las acciones críticas.

### Tareas Específicas
- [ ] Crear modelo AuditLog en base de datos
- [ ] Implementar servicio de auditoría (AuditService)
- [ ] Crear middleware para capturar IP y User-Agent
- [ ] Registrar eventos: LOGIN, LOGOUT, CREATE, UPDATE, DELETE
- [ ] Almacenar logs en MongoDB (opcional) o PostgreSQL
- [ ] Implementar endpoints para consultar logs
- [ ] Implementar filtros (usuario, fecha, acción, recurso)
- [ ] Implementar exportación de logs (CSV, JSON)
- [ ] Configurar rotación de logs (mantener 90 días)

### Criterios de Aceptación
- Todas las acciones críticas registradas
- Logs consultables vía API
- Datos de auditoría incluyen contexto completo

---

## T012: Implementar API de Documentación (Swagger)
**Prioridad:** MEDIA  
**Estimación:** 8 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T008

### Descripción
Documentar todas las APIs con Swagger/OpenAPI.

### Tareas Específicas
- [ ] Instalar swagger-jsdoc y swagger-ui-express
- [ ] Configurar Swagger en Express
- [ ] Documentar schemas de modelos
- [ ] Documentar todos los endpoints con anotaciones JSDoc
- [ ] Incluir ejemplos de request/response
- [ ] Documentar códigos de error
- [ ] Configurar autenticación en Swagger UI (JWT)
- [ ] Generar especificación OpenAPI 3.0

### Criterios de Aceptación
- Swagger UI accesible en /api-docs
- Todos los endpoints documentados
- Se pueden probar endpoints desde Swagger UI

---

## T013: Configurar Testing Unitario y de Integración (Backend)
**Prioridad:** ALTA  
**Estimación:** 16 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T008

### Descripción
Configurar framework de testing y escribir tests para funcionalidades críticas.

### Tareas Específicas
- [ ] Instalar Jest y supertest
- [ ] Configurar Jest para TypeScript
- [ ] Crear base de datos de testing
- [ ] Escribir tests para AuthService
- [ ] Escribir tests para UserController
- [ ] Escribir tests de integración para endpoints de auth
- [ ] Configurar coverage reporting
- [ ] Integrar tests en CI/CD
- [ ] Objetivo: >70% de cobertura

### Criterios de Aceptación
- Tests ejecutables con `npm test`
- Cobertura >70% en servicios críticos
- Tests de integración pasan

---

## T014: Implementar Rate Limiting y Protección DDoS
**Prioridad:** ALTA  
**Estimación:** 6 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T006

### Descripción
Implementar protección contra abuso y ataques DDoS.

### Tareas Específicas
- [ ] Instalar express-rate-limit
- [ ] Configurar rate limiting global (100 req/min)
- [ ] Configurar rate limiting estricto para auth (5 intentos/15min)
- [ ] Implementar rate limiting por IP y por usuario
- [ ] Configurar Redis como store para rate limiting
- [ ] Implementar headers de rate limit en respuestas
- [ ] Configurar helmet.js para headers de seguridad
- [ ] Implementar CORS configurado

### Criterios de Aceptación
- Rate limiting funciona correctamente
- Headers de seguridad presentes
- CORS configurado apropiadamente

---

## T015: Implementar WebSocket para Real-time
**Prioridad:** MEDIA  
**Estimación:** 8 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T006

### Descripción
Configurar Socket.io para comunicación en tiempo real.

### Tareas Específicas
- [ ] Instalar socket.io
- [ ] Integrar Socket.io con Express
- [ ] Implementar autenticación de sockets con JWT
- [ ] Crear rooms por usuario
- [ ] Implementar eventos: server_status, command_output, notification
- [ ] Crear servicio de notificaciones real-time
- [ ] Documentar protocolo de WebSocket
- [ ] Implementar reconexión automática

### Criterios de Aceptación
- WebSocket conecta correctamente
- Autenticación funciona
- Eventos se transmiten en tiempo real

---

# Fase 3: Frontend Base e Interfaz de Usuario
**Duración:** 2 semanas | **Horas:** 96h

## T016: Diseñar Sistema de Diseño y Componentes Base
**Prioridad:** ALTA  
**Estimación:** 14 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T003

### Descripción
Crear sistema de diseño consistente y biblioteca de componentes reutilizables.

### Tareas Específicas
- [ ] Definir paleta de colores basada en documentación existente
- [ ] Crear tema de Bootstrap personalizado (variables SCSS)
- [ ] Crear componentes base: Button, Input, Card, Modal, Alert
- [ ] Implementar componente Loading/Spinner
- [ ] Implementar componente Toast para notificaciones
- [ ] Crear layout base (Header, Sidebar, Footer, MainContent)
- [ ] Implementar responsive design
- [ ] Documentar componentes en Storybook (opcional)

### Criterios de Aceptación
- Componentes reutilizables creados
- Diseño consistente en toda la app
- Responsive en mobile, tablet, desktop

---

## T017: Implementar Sistema de Routing y Navegación
**Prioridad:** ALTA  
**Estimación:** 8 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T016

### Descripción
Configurar React Router con rutas protegidas y navegación.

### Tareas Específicas
- [ ] Configurar React Router v6
- [ ] Crear rutas públicas: /login, /register, /forgot-password
- [ ] Crear rutas privadas: /dashboard, /servers, /users, /docs
- [ ] Implementar ProtectedRoute component
- [ ] Implementar navegación con Sidebar
- [ ] Implementar breadcrumbs
- [ ] Configurar redirección post-login
- [ ] Implementar página 404

### Criterios de Aceptación
- Navegación entre páginas funciona
- Rutas protegidas requieren autenticación
- Sidebar muestra ruta activa

---

## T018: Implementar Páginas de Autenticación
**Prioridad:** CRÍTICA  
**Estimación:** 12 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T017

### Descripción
Crear páginas de login, registro y recuperación de contraseña.

### Tareas Específicas
- [ ] Crear página de Login con formulario
- [ ] Crear página de Registro con validación
- [ ] Crear página de Forgot Password
- [ ] Crear página de Reset Password
- [ ] Implementar validación de formularios con Formik + Yup
- [ ] Integrar con API de autenticación
- [ ] Guardar tokens en localStorage/cookies
- [ ] Implementar auto-logout al expirar token
- [ ] Mostrar mensajes de error apropiados

### Criterios de Aceptación
- Usuario puede iniciar sesión
- Usuario puede registrarse
- Recuperación de contraseña funciona
- Validaciones client-side funcionan

---

## T019: Implementar Estado Global con Redux
**Prioridad:** ALTA  
**Estimación:** 12 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T018

### Descripción
Configurar Redux Toolkit para manejo de estado global.

### Tareas Específicas
- [ ] Crear slices: authSlice, userSlice, serverSlice, uiSlice
- [ ] Implementar actions y reducers para autenticación
- [ ] Implementar selectors para acceder al estado
- [ ] Configurar Redux DevTools
- [ ] Implementar persistencia de auth state
- [ ] Crear middleware para refresh token automático
- [ ] Implementar loading states
- [ ] Documentar estructura del store

### Criterios de Aceptación
- Estado global accesible en componentes
- Autenticación persiste entre recargas
- Redux DevTools funciona

---

## T020: Implementar Dashboard Principal
**Prioridad:** ALTA  
**Estimación:** 14 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T019

### Descripción
Crear dashboard principal con resumen de servidores y estadísticas.

### Tareas Específicas
- [ ] Diseñar layout del dashboard
- [ ] Crear componentes: StatCard, ServerList, RecentActivity
- [ ] Implementar gráficos con Chart.js o Recharts
- [ ] Mostrar métricas clave: servidores online, comandos ejecutados, alertas
- [ ] Implementar lista de servidores recientes
- [ ] Implementar lista de actividad reciente
- [ ] Implementar refresh automático de datos
- [ ] Hacer responsive

### Criterios de Aceptación
- Dashboard muestra información relevante
- Datos se actualizan automáticamente
- Gráficos visualizan correctamente

---

## T021: Implementar Gestión de Usuarios (Frontend)
**Prioridad:** MEDIA  
**Estimación:** 14 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T019

### Descripción
Crear interfaz para CRUD de usuarios.

### Tareas Específicas
- [ ] Crear página de lista de usuarios con tabla
- [ ] Implementar búsqueda y filtros
- [ ] Implementar paginación
- [ ] Crear modal para crear usuario
- [ ] Crear modal para editar usuario
- [ ] Implementar confirmación para eliminar
- [ ] Mostrar badge de rol y estado
- [ ] Implementar botones de activar/desactivar
- [ ] Integrar con API de usuarios

### Criterios de Aceptación
- Lista de usuarios funcional
- CRUD completo desde UI
- Validaciones y confirmaciones apropiadas

---

## T022: Implementar Sistema de Notificaciones
**Prioridad:** MEDIA  
**Estimación:** 10 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T019

### Descripción
Implementar sistema de notificaciones en tiempo real con WebSocket.

### Tareas Específicas
- [ ] Integrar Socket.io client
- [ ] Crear NotificationProvider con Context API
- [ ] Implementar componente NotificationCenter
- [ ] Mostrar notificaciones toast
- [ ] Implementar badge de notificaciones no leídas
- [ ] Crear lista de notificaciones históricas
- [ ] Implementar marcar como leída
- [ ] Configurar sonidos/vibración para notificaciones
- [ ] Permitir configurar preferencias

### Criterios de Aceptación
- Notificaciones en tiempo real funcionan
- Toast aparece con nuevas notificaciones
- Historial consultable

---

## T023: Implementar Página de Documentación Interactiva
**Prioridad:** ALTA  
**Estimación:** 12 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T017

### Descripción
Convertir la documentación HTML existente en componente React interactivo.

### Tareas Específicas
- [ ] Migrar contenido de docuemntacion.html a componentes React
- [ ] Implementar Sidebar con índice navegable
- [ ] Implementar búsqueda en documentación
- [ ] Implementar syntax highlighting con Prism.js
- [ ] Implementar botones "copiar código"
- [ ] Implementar scrollspy para resaltar sección actual
- [ ] Implementar marcadores/favoritos
- [ ] Implementar modo oscuro
- [ ] Agregar botón "back to top"

### Criterios de Aceptación
- Documentación navegable y búsqueable
- Código con syntax highlighting
- Copiar código funciona
- Responsive y accesible

---

# Fase 4: Integración SSH y Gestión de VPS
**Duración:** 2 semanas | **Horas:** 88h

## T024: Implementar Conexión SSH Segura
**Prioridad:** CRÍTICA  
**Estimación:** 16 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T011

### Descripción
Implementar sistema de conexión SSH a servidores VPS usando ssh2.

### Tareas Específicas
- [ ] Instalar librería ssh2
- [ ] Crear servicio SSHService
- [ ] Implementar autenticación por password
- [ ] Implementar autenticación por SSH key
- [ ] Implementar pool de conexiones
- [ ] Implementar timeout de conexiones
- [ ] Validar fingerprint del servidor
- [ ] Implementar retry logic
- [ ] Manejar errores de conexión apropiadamente
- [ ] Implementar logs de conexiones

### Criterios de Aceptación
- Conexión SSH exitosa a servidor real
- Autenticación por key funciona
- Errores manejados correctamente

---

## T025: CRUD de Servidores VPS
**Prioridad:** ALTA  
**Estimación:** 12 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T024

### Descripción
Implementar endpoints para gestión de servidores VPS.

### Tareas Específicas
- [ ] Crear modelo VPSServer en Prisma
- [ ] Implementar VPSServerController
- [ ] Implementar POST /servers (con test de conexión)
- [ ] Implementar GET /servers (lista con filtros)
- [ ] Implementar GET /servers/:id
- [ ] Implementar PUT /servers/:id
- [ ] Implementar DELETE /servers/:id
- [ ] Implementar POST /servers/:id/test-connection
- [ ] Encriptar credenciales antes de guardar (AES-256)
- [ ] Implementar validaciones

### Criterios de Aceptación
- CRUD completo de servidores
- Credenciales almacenadas de forma segura
- Test de conexión funciona

---

## T026: Implementar Ejecución de Comandos SSH
**Prioridad:** ALTA  
**Estimación:** 14 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T024

### Descripción
Permitir ejecución de comandos en servidores remotos vía SSH.

### Tareas Específicas
- [ ] Crear endpoint POST /ssh/execute
- [ ] Validar comando antes de ejecutar (whitelist/blacklist)
- [ ] Ejecutar comando y capturar output
- [ ] Implementar timeout para comandos (30s default)
- [ ] Transmitir output en tiempo real vía WebSocket
- [ ] Guardar historial de comandos ejecutados
- [ ] Implementar permisos por comando (RBAC)
- [ ] Sanitizar inputs para prevenir injection
- [ ] Implementar modo interactivo (stdin)

### Criterios de Aceptación
- Comandos se ejecutan correctamente
- Output visible en tiempo real
- Comandos peligrosos bloqueados

---

## T027: Implementar Sistema de Plantillas de Scripts
**Prioridad:** MEDIA  
**Estimación:** 12 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T026

### Descripción
Crear sistema de plantillas de scripts reutilizables.

### Tareas Específicas
- [ ] Crear modelo Template en base de datos
- [ ] Implementar CRUD de plantillas
- [ ] Implementar sistema de parámetros/variables
- [ ] Implementar renderizado de plantillas (handlebars o similar)
- [ ] Crear categorías: setup, maintenance, security, monitoring
- [ ] Implementar endpoint para ejecutar plantilla
- [ ] Crear librería de plantillas por defecto
- [ ] Implementar versionado de plantillas
- [ ] Permitir compartir plantillas entre usuarios

### Criterios de Aceptación
- Plantillas creables y editables
- Parámetros se reemplazan correctamente
- Plantillas ejecutables en servidores

---

## T028: Implementar Gestión de Llaves SSH
**Prioridad:** ALTA  
**Estimación:** 12 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T024

### Descripción
Sistema para generar, almacenar y gestionar llaves SSH.

### Tareas Específicas
- [ ] Crear modelo SSHKey en base de datos
- [ ] Implementar generación de pares de llaves (ssh-keygen via child_process)
- [ ] Almacenar llaves privadas encriptadas (AES-256)
- [ ] Implementar endpoints CRUD para llaves
- [ ] Calcular y almacenar fingerprint
- [ ] Implementar distribución de llave pública a servidor
- [ ] Implementar endpoint para probar llave
- [ ] Implementar expiración de llaves
- [ ] Permitir importar llaves existentes

### Criterios de Aceptación
- Llaves generadas correctamente
- Llaves almacenadas de forma segura
- Distribución a servidor funciona

---

## T029: Implementar Frontend de Gestión de Servidores
**Prioridad:** ALTA  
**Estimación:** 14 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T025

### Descripción
Crear interfaz para gestionar servidores VPS.

### Tareas Específicas
- [ ] Crear página de lista de servidores con cards
- [ ] Mostrar estado (online/offline) con indicador visual
- [ ] Implementar modal para agregar servidor
- [ ] Implementar formulario de edición
- [ ] Implementar confirmación de eliminación
- [ ] Mostrar tags/labels
- [ ] Implementar filtros por estado, tags
- [ ] Implementar botón "Test Connection"
- [ ] Mostrar últimas métricas del servidor

### Criterios de Aceptación
- Lista de servidores visual y funcional
- CRUD completo desde UI
- Estado de servidores visible

---

## T030: Implementar Terminal Web
**Prioridad:** MEDIA  
**Estimación:** 8 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T026

### Descripción
Crear terminal web interactiva para ejecutar comandos.

### Tareas Específicas
- [ ] Integrar librería xterm.js
- [ ] Crear componente WebTerminal
- [ ] Conectar con WebSocket para output en tiempo real
- [ ] Implementar historial de comandos (flecha arriba/abajo)
- [ ] Implementar auto-complete básico
- [ ] Personalizar tema de terminal
- [ ] Implementar múltiples tabs (multi-terminal)
- [ ] Guardar sesión de terminal

### Criterios de Aceptación
- Terminal funcional y responsive
- Comandos ejecutables
- Output en tiempo real

---

# Fase 5: Features Avanzadas y Monitoreo
**Duración:** 1.5 semanas | **Horas:** 72h

## T031: Implementar Monitoreo de Métricas de Servidores
**Prioridad:** MEDIA  
**Estimación:** 14 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T026

### Descripción
Recopilar y almacenar métricas de servidores (CPU, RAM, disco, red).

### Tareas Específicas
- [ ] Crear scripts para recopilar métricas vía SSH
- [ ] Implementar scheduler con node-cron para polling periódico
- [ ] Almacenar métricas en time-series (TimescaleDB o InfluxDB)
- [ ] Crear endpoint GET /servers/:id/metrics
- [ ] Implementar agregaciones (promedio, máximo, mínimo)
- [ ] Configurar intervalos de recopilación (1min, 5min, 15min)
- [ ] Implementar retención de datos (30 días)
- [ ] Transmitir métricas en tiempo real vía WebSocket

### Criterios de Aceptación
- Métricas recopiladas automáticamente
- Datos consultables vía API
- Métricas en tiempo real disponibles

---

## T032: Implementar Sistema de Alertas
**Prioridad:** MEDIA  
**Estimación:** 12 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T031

### Descripción
Sistema de alertas basado en umbrales de métricas.

### Tareas Específicas
- [ ] Crear modelo Alert y AlertRule en base de datos
- [ ] Implementar evaluador de reglas
- [ ] Configurar umbrales: CPU>80%, RAM>90%, Disk>85%
- [ ] Implementar notificaciones por email
- [ ] Implementar notificaciones in-app (WebSocket)
- [ ] Crear endpoints CRUD para reglas de alerta
- [ ] Implementar cooldown para evitar spam
- [ ] Implementar escalamiento de alertas
- [ ] Crear dashboard de alertas

### Criterios de Aceptación
- Alertas se disparan correctamente
- Notificaciones enviadas
- Reglas configurables

---

## T033: Implementar Dashboard de Métricas
**Prioridad:** MEDIA  
**Estimación:** 12 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T031

### Descripción
Crear dashboard visual para métricas de servidores.

### Tareas Específicas
- [ ] Integrar librería de gráficos (Chart.js o Recharts)
- [ ] Crear gráficos de línea para CPU, RAM, Disco
- [ ] Implementar selector de rango de fechas
- [ ] Crear vista de comparación multi-servidor
- [ ] Implementar auto-refresh de gráficos
- [ ] Mostrar métricas actuales vs. históricas
- [ ] Implementar exportación de gráficos (PNG)
- [ ] Hacer responsive

### Criterios de Aceptación
- Gráficos visualizan correctamente
- Datos actualizados automáticamente
- Interfaz intuitiva

---

## T034: Implementar Autenticación de Dos Factores (2FA)
**Prioridad:** MEDIA  
**Estimación:** 10 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T006

### Descripción
Agregar capa adicional de seguridad con 2FA.

### Tareas Específicas
- [ ] Instalar speakeasy para TOTP
- [ ] Implementar generación de secret 2FA
- [ ] Implementar endpoint POST /auth/2fa/enable
- [ ] Generar QR code para escanear (qrcode library)
- [ ] Implementar verificación de código 2FA en login
- [ ] Implementar códigos de backup (recovery codes)
- [ ] Implementar endpoint POST /auth/2fa/disable
- [ ] Guardar estado 2FA en modelo User
- [ ] Actualizar frontend de login

### Criterios de Aceptación
- Usuario puede habilitar 2FA
- Login requiere código 2FA si está habilitado
- Códigos de backup funcionan

---

## T035: Implementar Exportación de Reportes
**Prioridad:** BAJA  
**Estimación:** 10 horas  
**Asignado a:** Backend Developer  
**Dependencias:** T031

### Descripción
Permitir exportar reportes de actividad y métricas.

### Tareas Específicas
- [ ] Implementar generación de PDF (puppeteer o pdfkit)
- [ ] Crear templates de reportes
- [ ] Implementar endpoint POST /reports/generate
- [ ] Incluir métricas, logs, actividad reciente
- [ ] Implementar exportación CSV de datos
- [ ] Permitir programar reportes periódicos
- [ ] Enviar reportes por email automáticamente
- [ ] Almacenar reportes generados

### Criterios de Aceptación
- Reportes PDF generados correctamente
- CSV exporta datos estructurados
- Reportes programados funcionan

---

## T036: Implementar Búsqueda Global
**Prioridad:** BAJA  
**Estimación:** 8 horas  
**Asignado a:** Full Stack Developer  
**Dependencias:** T023

### Descripción
Sistema de búsqueda global en toda la aplicación.

### Tareas Específicas
- [ ] Implementar endpoint GET /search?q=query
- [ ] Buscar en: servidores, documentación, usuarios, comandos
- [ ] Implementar búsqueda fuzzy (Fuse.js o similar)
- [ ] Implementar highlights de resultados
- [ ] Crear componente SearchBar en frontend
- [ ] Implementar modal de resultados
- [ ] Implementar navegación con teclado
- [ ] Implementar historial de búsquedas

### Criterios de Aceptación
- Búsqueda encuentra resultados relevantes
- Resultados agrupados por tipo
- Interfaz rápida y responsive

---

## T037: Implementar Temas y Personalización
**Prioridad:** BAJA  
**Estimación:** 6 horas  
**Asignado a:** Frontend Developer  
**Dependencias:** T016

### Descripción
Permitir personalización de tema (claro/oscuro) y preferencias de UI.

### Tareas Específicas
- [ ] Implementar tema oscuro con CSS variables
- [ ] Crear toggle de tema en UI
- [ ] Guardar preferencia en localStorage
- [ ] Implementar detección automática (prefers-color-scheme)
- [ ] Crear settings page para preferencias
- [ ] Permitir configurar idioma (i18n básico)
- [ ] Permitir configurar densidad de UI
- [ ] Sincronizar preferencias con backend (opcional)

### Criterios de Aceptación
- Tema oscuro funcional
- Preferencias persisten
- Transición suave entre temas

---

# Fase 6: Testing y Quality Assurance
**Duración:** 1 semana | **Horas:** 40h

## T038: Testing End-to-End (E2E)
**Prioridad:** ALTA  
**Estimación:** 16 horas  
**Asignado a:** QA Engineer  
**Dependencias:** T030

### Descripción
Implementar tests E2E para flujos críticos de usuario.

### Tareas Específicas
- [ ] Instalar y configurar Cypress o Playwright
- [ ] Escribir tests para flujo de autenticación
- [ ] Escribir tests para CRUD de servidores
- [ ] Escribir tests para ejecución de comandos
- [ ] Escribir tests para gestión de usuarios
- [ ] Configurar CI para ejecutar E2E tests
- [ ] Implementar screenshots en caso de fallo
- [ ] Crear base de datos de testing con datos seed

### Criterios de Aceptación
- Tests E2E cubren flujos críticos
- Tests ejecutables en CI
- Tasa de éxito >95%

---

## T039: Auditoría de Seguridad
**Prioridad:** CRÍTICA  
**Estimación:** 12 horas  
**Asignado a:** Security Expert  
**Dependencias:** T038

### Descripción
Realizar auditoría completa de seguridad y corregir vulnerabilidades.

### Tareas Específicas
- [ ] Ejecutar npm audit y corregir vulnerabilidades
- [ ] Revisar autenticación y autorización
- [ ] Verificar encriptación de datos sensibles
- [ ] Probar inyecciones (SQL, command injection)
- [ ] Verificar protección CSRF
- [ ] Verificar headers de seguridad
- [ ] Probar rate limiting
- [ ] Revisar configuración de CORS
- [ ] Ejecutar OWASP ZAP o similar
- [ ] Documentar hallazgos y fixes

### Criterios de Aceptación
- Sin vulnerabilidades críticas
- Recomendaciones implementadas
- Reporte de seguridad documentado

---

## T040: Optimización de Performance
**Prioridad:** MEDIA  
**Estimación:** 8 horas  
**Asignado a:** Full Stack Developer  
**Dependencias:** T038

### Descripción
Optimizar rendimiento de frontend y backend.

### Tareas Específicas
- [ ] Ejecutar Lighthouse audit
- [ ] Implementar code splitting en React
- [ ] Optimizar bundle size (tree shaking, lazy loading)
- [ ] Implementar caching de assets estáticos
- [ ] Optimizar queries de base de datos (índices)
- [ ] Implementar caching con Redis
- [ ] Comprimir respuestas HTTP (gzip/brotli)
- [ ] Optimizar imágenes
- [ ] Medir y documentar mejoras

### Criterios de Aceptación
- Lighthouse score >90
- Tiempo de carga <2s
- Bundle size reducido >30%

---

## T041: Pruebas de Carga y Stress Testing
**Prioridad:** MEDIA  
**Estimación:** 4 horas  
**Asignado a:** DevOps Engineer  
**Dependencias:** T040

### Descripción
Validar que el sistema soporta la carga esperada.

### Tareas Específicas
- [ ] Instalar k6 o Artillery
- [ ] Crear scripts de carga para endpoints críticos
- [ ] Simular 100 usuarios concurrentes
- [ ] Medir tiempos de respuesta bajo carga
- [ ] Identificar cuellos de botella
- [ ] Documentar resultados
- [ ] Implementar mejoras si es necesario

### Criterios de Aceptación
- Sistema soporta 100 usuarios concurrentes
- Tiempos de respuesta <500ms (p95)
- Sin errores bajo carga

---

# Fase 7: Deployment, Documentación y Lanzamiento
**Duración:** 0.75 semanas | **Horas:** 34h

## T042: Configuración de Servidor de Producción
**Prioridad:** CRÍTICA  
**Estimación:** 10 horas  
**Asignado a:** DevOps Engineer  
**Dependencias:** T041

### Descripción
Configurar servidor VPS para producción.

### Tareas Específicas
- [ ] Provisionar VPS (DigitalOcean/AWS)
- [ ] Configurar SSH con autenticación por llave
- [ ] Configurar firewall (UFW): solo 22, 80, 443
- [ ] Instalar Docker y Docker Compose
- [ ] Configurar NGINX como reverse proxy
- [ ] Obtener certificado SSL (Let's Encrypt)
- [ ] Configurar auto-renovación de SSL
- [ ] Configurar backups automáticos
- [ ] Instalar fail2ban
- [ ] Configurar monitoreo básico

### Criterios de Aceptación
- Servidor accesible vía HTTPS
- SSL configurado correctamente
- Backups programados

---

## T043: Pipeline de CI/CD con GitHub Actions
**Prioridad:** ALTA  
**Estimación:** 8 horas  
**Asignado a:** DevOps Engineer  
**Dependencias:** T042

### Descripción
Automatizar deployment con GitHub Actions.

### Tareas Específicas
- [ ] Crear workflow para testing (.github/workflows/test.yml)
- [ ] Crear workflow para deployment (.github/workflows/deploy.yml)
- [ ] Ejecutar linting y tests en cada PR
- [ ] Build y push de imágenes Docker
- [ ] Deploy automático a producción en merge a main
- [ ] Configurar secrets en GitHub
- [ ] Implementar health check post-deployment
- [ ] Configurar notificaciones de deployment

### Criterios de Aceptación
- Tests ejecutan automáticamente en PRs
- Deploy automático funciona
- Rollback posible si falla

---

## T044: Documentación Técnica Completa
**Prioridad:** ALTA  
**Estimación:** 12 horas  
**Asignado a:** Tech Writer  
**Dependencias:** T043

### Descripción
Crear documentación completa para desarrolladores y usuarios.

### Tareas Específicas
- [ ] Completar README.md con guía de inicio
- [ ] Documentar arquitectura del sistema
- [ ] Documentar APIs (complementar Swagger)
- [ ] Crear guía de contribución (CONTRIBUTING.md)
- [ ] Documentar proceso de deployment
- [ ] Crear runbooks para operaciones comunes
- [ ] Documentar troubleshooting
- [ ] Crear changelog
- [ ] Documentar variables de entorno
- [ ] Crear wiki en GitHub

### Criterios de Aceptación
- Documentación completa y clara
- Nuevo desarrollador puede hacer setup en <1h
- Operaciones documentadas

---

## T045: Lanzamiento y Monitoreo Post-Launch
**Prioridad:** CRÍTICA  
**Estimación:** 4 horas  
**Asignado a:** Product Manager + DevOps  
**Dependencias:** T044

### Descripción
Lanzar aplicación y monitorear estabilidad inicial.

### Tareas Específicas
- [ ] Hacer deployment final a producción
- [ ] Verificar que todos los servicios estén running
- [ ] Crear usuario administrador inicial
- [ ] Configurar monitoreo con Prometheus/Grafana
- [ ] Configurar alertas de uptime
- [ ] Configurar error tracking (Sentry)
- [ ] Realizar smoke tests en producción
- [ ] Monitorear logs primeras 24h
- [ ] Crear plan de soporte post-launch

### Criterios de Aceptación
- Aplicación accesible públicamente
- Monitoreo activo
- Sin errores críticos primeras 24h

---

## Resumen de Hitos

| Hito | Fecha Estimada | Entregables |
|------|----------------|-------------|
| **M1: Setup Completo** | Semana 1 | Proyecto configurado, Docker funcionando |
| **M2: Backend Core** | Semana 3.5 | APIs de auth y usuarios funcionando |
| **M3: Frontend Base** | Semana 5.5 | UI completa con autenticación |
| **M4: VPS Integration** | Semana 7.5 | Conexión SSH y gestión de servidores |
| **M5: Features Avanzadas** | Semana 9 | Monitoreo y alertas funcionando |
| **M6: QA Complete** | Semana 10 | Tests pasando, seguridad validada |
| **M7: Production Ready** | Semana 10.75 | Desplegado en producción |

## Riesgos y Mitigaciones

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Problemas de conexión SSH a VPS reales | Media | Alto | Usar VPS de testing, implementar mocks |
| Vulnerabilidades de seguridad | Media | Crítico | Auditoría continua, code review estricto |
| Performance bajo carga | Baja | Medio | Load testing temprano, optimización incremental |
| Complejidad de WebSocket real-time | Media | Medio | PoC temprano, fallback a polling |
| Retrasos en testing | Alta | Medio | Testing continuo desde fase 2 |

---

**Versión:** 1.0.0  
**Última actualización:** Febrero 2026  
**Aprobado por:** Tech Lead  
**Próxima revisión:** Post Fase 2
