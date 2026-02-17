# Historias de Usuario - Portal de Configuración VPS

## Resumen Ejecutivo

- **Total de Historias:** 20
- **Tiempo Estimado Total:** 238 horas
- **Épicas:** 5 (Autenticación, Gestión de Usuarios, Gestión de Servidores, Operaciones SSH, Monitoreo)
- **Roles:** Super Admin, Admin, Editor, Visualizador

## Matriz de Permisos por Rol

| Funcionalidad | Super Admin | Admin | Editor | Visualizador |
|---------------|-------------|-------|--------|--------------|
| **AUTENTICACIÓN** |
| Iniciar sesión | ✅ | ✅ | ✅ | ✅ |
| Cerrar sesión | ✅ | ✅ | ✅ | ✅ |
| Cambiar contraseña propia | ✅ | ✅ | ✅ | ✅ |
| Habilitar 2FA | ✅ | ✅ | ✅ | ✅ |
| **GESTIÓN DE USUARIOS** |
| Ver lista de usuarios | ✅ | ✅ | ❌ | ❌ |
| Ver detalles de usuario | ✅ | ✅ | ❌ | ❌ |
| Crear usuarios | ✅ | ✅ | ❌ | ❌ |
| Editar usuarios | ✅ | ✅ (no admins) | ❌ | ❌ |
| Eliminar usuarios | ✅ | ❌ | ❌ | ❌ |
| Cambiar roles | ✅ | ❌ | ❌ | ❌ |
| Activar/Desactivar usuarios | ✅ | ✅ | ❌ | ❌ |
| **GESTIÓN DE SERVIDORES** |
| Ver lista de servidores | ✅ | ✅ | ✅ | ✅ |
| Ver detalles de servidor | ✅ | ✅ | ✅ | ✅ |
| Agregar servidores | ✅ | ✅ | ✅ | ❌ |
| Editar servidores | ✅ | ✅ | ✅ | ❌ |
| Eliminar servidores | ✅ | ✅ | ❌ | ❌ |
| Probar conexión | ✅ | ✅ | ✅ | ✅ |
| **LLAVES SSH** |
| Ver llaves SSH | ✅ | ✅ | ✅ | ✅ |
| Generar llaves SSH | ✅ | ✅ | ✅ | ❌ |
| Eliminar llaves SSH | ✅ | ✅ | ✅ (propias) | ❌ |
| Distribuir llaves a servidores | ✅ | ✅ | ✅ | ❌ |
| **EJECUCIÓN DE COMANDOS** |
| Ejecutar comandos básicos | ✅ | ✅ | ✅ | ❌ |
| Ejecutar comandos críticos | ✅ | ✅ | ❌ | ❌ |
| Ver historial de comandos | ✅ | ✅ | ✅ | ✅ |
| Usar terminal web | ✅ | ✅ | ✅ | ❌ |
| **PLANTILLAS** |
| Ver plantillas | ✅ | ✅ | ✅ | ✅ |
| Crear plantillas | ✅ | ✅ | ✅ | ❌ |
| Editar plantillas | ✅ | ✅ | ✅ (propias) | ❌ |
| Eliminar plantillas | ✅ | ✅ | ✅ (propias) | ❌ |
| Ejecutar plantillas | ✅ | ✅ | ✅ | ❌ |
| **MONITOREO** |
| Ver métricas de servidores | ✅ | ✅ | ✅ | ✅ |
| Ver dashboard | ✅ | ✅ | ✅ | ✅ |
| Configurar alertas | ✅ | ✅ | ❌ | ❌ |
| Ver historial de alertas | ✅ | ✅ | ✅ | ✅ |
| **DOCUMENTACIÓN** |
| Ver documentación | ✅ | ✅ | ✅ | ✅ |
| Buscar en documentación | ✅ | ✅ | ✅ | ✅ |
| Exportar documentación | ✅ | ✅ | ✅ | ✅ |
| **AUDITORÍA** |
| Ver logs de auditoría | ✅ | ✅ | ❌ | ❌ |
| Exportar logs | ✅ | ✅ | ❌ | ❌ |
| **CONFIGURACIÓN** |
| Ver configuración del sistema | ✅ | ❌ | ❌ | ❌ |
| Modificar configuración | ✅ | ❌ | ❌ | ❌ |

---

# Épica 1: Autenticación y Seguridad
**Tiempo Total:** 36 horas

## HU-001: Registro de Usuario
**Como** nuevo usuario  
**Quiero** poder registrarme en el sistema  
**Para** acceder a las funcionalidades del portal

### Criterios de Aceptación
- [ ] El formulario de registro solicita: nombre, apellido, email, contraseña
- [ ] La contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un símbolo
- [ ] El email debe ser único en el sistema
- [ ] Se envía un email de verificación al registrarse
- [ ] La cuenta queda pendiente de activación por un administrador
- [ ] Se muestra mensaje de éxito al completar el registro

### Validaciones
- Email válido y único
- Contraseña cumple requisitos de seguridad
- Todos los campos son obligatorios
- Protección contra registro automatizado (reCAPTCHA opcional)

### Tareas Técnicas
1. Crear formulario de registro en frontend
2. Implementar validaciones client-side con Formik + Yup
3. Crear endpoint POST /api/v1/auth/register
4. Implementar hash de contraseña con bcrypt
5. Crear plantilla de email de verificación
6. Implementar envío de email con Nodemailer

**Estimación:** 8 horas  
**Prioridad:** ALTA  
**Rol asignado:** Todos (auto-registro)

---

## HU-002: Inicio de Sesión
**Como** usuario registrado  
**Quiero** iniciar sesión con mi email y contraseña  
**Para** acceder a mi cuenta

### Criterios de Aceptación
- [ ] El formulario solicita email y contraseña
- [ ] Credenciales incorrectas muestran mensaje de error genérico
- [ ] Después de 5 intentos fallidos, la cuenta se bloquea temporalmente (15 minutos)
- [ ] Login exitoso redirige al dashboard
- [ ] Se genera token JWT con expiración de 24 horas
- [ ] Se guarda refresh token en cookie httpOnly
- [ ] Se registra el evento de login en auditoría

### Validaciones
- Email debe existir en el sistema
- Contraseña debe coincidir con el hash almacenado
- Usuario debe estar activo
- Rate limiting: máximo 5 intentos por 15 minutos

### Tareas Técnicas
1. Crear formulario de login en frontend
2. Crear endpoint POST /api/v1/auth/login
3. Implementar verificación de contraseña con bcrypt
4. Generar JWT con jsonwebtoken
5. Implementar refresh token
6. Configurar cookies seguras
7. Implementar rate limiting con express-rate-limit

**Estimación:** 8 horas  
**Prioridad:** CRÍTICA  
**Rol asignado:** Todos

---

## HU-003: Recuperación de Contraseña
**Como** usuario que olvidó su contraseña  
**Quiero** poder resetearla  
**Para** recuperar el acceso a mi cuenta

### Criterios de Aceptación
- [ ] Existe un link "¿Olvidaste tu contraseña?" en la página de login
- [ ] El formulario solicita el email
- [ ] Se envía un email con un link de recuperación
- [ ] El link expira después de 1 hora
- [ ] El link permite establecer una nueva contraseña
- [ ] Después de cambiar la contraseña, se invalidan todos los tokens existentes
- [ ] Se envía confirmación por email del cambio de contraseña

### Validaciones
- Email debe existir en el sistema
- Token debe ser válido y no expirado
- Nueva contraseña cumple requisitos de seguridad
- Token solo se puede usar una vez

### Tareas Técnicas
1. Crear página de "Olvidé mi contraseña"
2. Crear endpoint POST /api/v1/auth/forgot-password
3. Generar token seguro (crypto.randomBytes)
4. Almacenar token con expiración en base de datos
5. Enviar email con link de reseteo
6. Crear página de reseteo de contraseña
7. Crear endpoint POST /api/v1/auth/reset-password
8. Invalidar tokens JWT existentes

**Estimación:** 10 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Todos

---

## HU-004: Autenticación de Dos Factores (2FA)
**Como** usuario preocupado por la seguridad  
**Quiero** habilitar autenticación de dos factores  
**Para** proteger mi cuenta con una capa adicional de seguridad

### Criterios de Aceptación
- [ ] Existe opción para habilitar 2FA en configuración de perfil
- [ ] Se genera un código QR para escanear con Google Authenticator
- [ ] Se proporcionan códigos de respaldo (10 códigos únicos)
- [ ] Al habilitar 2FA, el siguiente login requiere código
- [ ] Código 2FA expira después de 30 segundos
- [ ] Se puede deshabilitar 2FA con contraseña y código actual
- [ ] Los códigos de respaldo se pueden regenerar

### Validaciones
- Usuario debe confirmar contraseña antes de habilitar/deshabilitar 2FA
- Código 2FA debe ser válido (6 dígitos)
- Códigos de respaldo solo se pueden usar una vez
- Máximo 3 intentos de código incorrecto antes de bloqueo temporal

### Tareas Técnicas
1. Instalar speakeasy para TOTP
2. Crear endpoint POST /api/v1/auth/2fa/enable
3. Generar secret y QR code (qrcode library)
4. Generar códigos de respaldo
5. Modificar flujo de login para solicitar código 2FA
6. Crear endpoint POST /api/v1/auth/2fa/verify
7. Implementar UI para habilitar/deshabilitar 2FA

**Estimación:** 10 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Todos

---

# Épica 2: Gestión de Usuarios
**Tiempo Total:** 40 horas

## HU-005: Ver Lista de Usuarios
**Como** administrador  
**Quiero** ver una lista de todos los usuarios del sistema  
**Para** gestionar los accesos

### Criterios de Aceptación
- [ ] La tabla muestra: nombre, email, rol, estado (activo/inactivo), último login
- [ ] La lista está paginada (20 usuarios por página)
- [ ] Se puede filtrar por rol
- [ ] Se puede filtrar por estado (activo/inactivo)
- [ ] Se puede buscar por nombre o email
- [ ] Se puede ordenar por nombre, email, fecha de creación
- [ ] Cada fila tiene botones de acción: editar, activar/desactivar, eliminar

### Validaciones
- Solo usuarios con rol Admin o Super Admin pueden acceder
- Los resultados se cargan de forma eficiente (lazy loading)

### Tareas Técnicas
1. Crear endpoint GET /api/v1/users con paginación
2. Implementar filtros y búsqueda en backend
3. Crear componente UserList en frontend
4. Implementar tabla con react-table o similar
5. Implementar paginación
6. Crear filtros de búsqueda
7. Agregar badges para roles y estados

**Estimación:** 8 horas  
**Prioridad:** ALTA  
**Rol asignado:** Admin, Super Admin

---

## HU-006: Crear Nuevo Usuario
**Como** administrador  
**Quiero** crear manualmente nuevos usuarios  
**Para** dar acceso a personas de mi equipo sin esperar auto-registro

### Criterios de Aceptación
- [ ] Formulario modal para crear usuario
- [ ] Campos: nombre, apellido, email, rol
- [ ] La contraseña inicial se genera automáticamente
- [ ] Se envía email al nuevo usuario con credenciales temporales
- [ ] El usuario debe cambiar la contraseña en el primer login
- [ ] La cuenta se crea activa por defecto
- [ ] Se registra la acción en auditoría

### Validaciones
- Email debe ser único
- Rol seleccionado debe ser válido
- Solo Admin puede crear usuarios con rol Editor o Visualizador
- Solo Super Admin puede crear usuarios con rol Admin

### Tareas Técnicas
1. Crear modal de creación de usuario
2. Crear endpoint POST /api/v1/users
3. Generar contraseña temporal segura
4. Enviar email con credenciales
5. Implementar flag de "debe cambiar contraseña"
6. Agregar validación de primer login

**Estimación:** 8 horas  
**Prioridad:** ALTA  
**Rol asignado:** Admin, Super Admin

---

## HU-007: Editar Usuario Existente
**Como** administrador  
**Quiero** modificar la información de un usuario  
**Para** actualizar sus datos o permisos

### Criterios de Aceptación
- [ ] Formulario modal pre-poblado con datos actuales
- [ ] Se puede modificar: nombre, apellido, email, rol
- [ ] Admin no puede modificar usuarios con rol Admin o Super Admin
- [ ] Admin no puede asignar rol Admin
- [ ] Se envía notificación al usuario si cambia su rol
- [ ] Los cambios se guardan inmediatamente
- [ ] Se registra la acción en auditoría

### Validaciones
- Email debe ser único (si se modifica)
- No se puede auto-degradar el propio rol
- Debe existir al menos un Super Admin activo

### Tareas Técnicas
1. Crear modal de edición de usuario
2. Crear endpoint PUT /api/v1/users/:id
3. Implementar validaciones de permisos
4. Enviar notificación si cambia rol
5. Actualizar lista después de editar

**Estimación:** 8 horas  
**Prioridad:** ALTA  
**Rol asignado:** Admin (limitado), Super Admin

---

## HU-008: Desactivar/Activar Usuario
**Como** administrador  
**Quiero** desactivar temporalmente usuarios  
**Para** bloquear acceso sin eliminar la cuenta

### Criterios de Aceptación
- [ ] Botón de toggle activo/inactivo en lista de usuarios
- [ ] Usuario inactivo no puede iniciar sesión
- [ ] Se invalidan todos los tokens del usuario al desactivar
- [ ] Se puede reactivar usuarios inactivos
- [ ] Se muestra confirmación antes de desactivar
- [ ] Se registra la acción en auditoría
- [ ] No se puede auto-desactivar

### Validaciones
- No se puede desactivar el último Super Admin activo
- No se puede desactivar la propia cuenta
- Solo Admin y Super Admin pueden desactivar usuarios

### Tareas Técnicas
1. Crear endpoint PATCH /api/v1/users/:id/activate
2. Crear endpoint PATCH /api/v1/users/:id/deactivate
3. Implementar invalidación de tokens
4. Agregar validación en login
5. Crear toggle en UI
6. Implementar confirmación modal

**Estimación:** 6 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Admin, Super Admin

---

## HU-009: Eliminar Usuario
**Como** super administrador  
**Quiero** eliminar permanentemente usuarios  
**Para** limpiar cuentas no utilizadas o problemáticas

### Criterios de Aceptación
- [ ] Solo Super Admin puede eliminar usuarios
- [ ] Se muestra confirmación que requiere escribir "ELIMINAR"
- [ ] Eliminación es soft delete (se marca como eliminado, no se borra físicamente)
- [ ] Los datos del usuario se mantienen en logs de auditoría
- [ ] Los recursos creados por el usuario se reasignan
- [ ] No se puede eliminar la propia cuenta
- [ ] No se puede eliminar el último Super Admin

### Validaciones
- Usuario debe confirmar acción escribiendo palabra clave
- No se puede eliminar la propia cuenta
- Debe existir al menos un Super Admin activo

### Tareas Técnicas
1. Crear endpoint DELETE /api/v1/users/:id
2. Implementar soft delete
3. Reasignar recursos huérfanos
4. Crear modal de confirmación con input
5. Implementar validaciones de seguridad

**Estimación:** 10 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Super Admin

---

# Épica 3: Gestión de Servidores VPS
**Tiempo Total:** 64 horas

## HU-010: Agregar Servidor VPS
**Como** editor  
**Quiero** registrar un nuevo servidor VPS en el sistema  
**Para** poder administrarlo desde el portal

### Criterios de Aceptación
- [ ] Formulario modal para agregar servidor
- [ ] Campos: nombre, dirección IP, puerto SSH (default 22), usuario, método de autenticación
- [ ] Métodos de autenticación: contraseña o llave SSH
- [ ] Se puede probar la conexión antes de guardar
- [ ] Se pueden agregar tags/etiquetas
- [ ] Se guarda quién creó el servidor y cuándo
- [ ] Las credenciales se almacenan encriptadas (AES-256)

### Validaciones
- IP debe ser válida (IPv4 o IPv6)
- Puerto debe estar en rango 1-65535
- Combinación IP+Puerto debe ser única
- Test de conexión debe ser exitoso antes de guardar (opcional)
- Si se usa llave SSH, debe seleccionar una existente

### Tareas Técnicas
1. Crear formulario de agregar servidor
2. Implementar selector de método de autenticación
3. Implementar botón "Probar conexión"
4. Crear endpoint POST /api/v1/servers
5. Implementar encriptación de credenciales
6. Validar conexión SSH
7. Almacenar en base de datos

**Estimación:** 12 horas  
**Prioridad:** CRÍTICA  
**Rol asignado:** Editor, Admin, Super Admin

---

## HU-011: Ver Lista de Servidores
**Como** visualizador  
**Quiero** ver todos los servidores VPS registrados  
**Para** conocer la infraestructura disponible

### Criterios de Aceptación
- [ ] Vista de cards o tabla con todos los servidores
- [ ] Cada servidor muestra: nombre, IP, estado (online/offline), tags, última verificación
- [ ] Indicador visual de estado (verde=online, rojo=offline, gris=desconocido)
- [ ] Se puede filtrar por tags
- [ ] Se puede filtrar por estado
- [ ] Se puede buscar por nombre o IP
- [ ] Se actualiza el estado automáticamente cada 5 minutos

### Validaciones
- Todos los roles pueden ver la lista
- Los datos se cargan eficientemente
- El estado se actualiza en tiempo real (WebSocket)

### Tareas Técnicas
1. Crear componente ServerList
2. Implementar vista de cards responsive
3. Crear endpoint GET /api/v1/servers
4. Implementar filtros y búsqueda
5. Implementar verificación periódica de estado
6. Conectar WebSocket para actualizaciones en tiempo real

**Estimación:** 10 horas  
**Prioridad:** ALTA  
**Rol asignado:** Todos

---

## HU-012: Ver Detalles de Servidor
**Como** usuario  
**Quiero** ver información detallada de un servidor  
**Para** conocer su configuración y estado actual

### Criterios de Aceptación
- [ ] Página de detalles muestra toda la información del servidor
- [ ] Se muestra: nombre, IP, puerto, usuario, método de auth, tags, fecha de creación
- [ ] Se muestran métricas actuales: CPU, RAM, disco, red (si están disponibles)
- [ ] Se muestra historial de conexiones
- [ ] Se muestra quién creó el servidor
- [ ] Se muestra última vez que estuvo online
- [ ] Botones de acción según permisos: editar, eliminar, probar conexión

### Validaciones
- Todos los roles pueden ver detalles
- Las credenciales no se muestran (solo método de autenticación)
- Las métricas se actualizan automáticamente

### Tareas Técnicas
1. Crear página ServerDetails
2. Crear endpoint GET /api/v1/servers/:id
3. Mostrar información del servidor
4. Integrar métricas si están disponibles
5. Mostrar historial de conexiones
6. Implementar botones de acción con permisos

**Estimación:** 10 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Todos

---

## HU-013: Editar Servidor VPS
**Como** editor  
**Quiero** modificar la configuración de un servidor  
**Para** actualizar credenciales o información

### Criterios de Aceptación
- [ ] Formulario modal pre-poblado con datos actuales
- [ ] Se puede modificar: nombre, IP, puerto, usuario, tags
- [ ] Se puede cambiar el método de autenticación
- [ ] Al cambiar credenciales, se prueba la conexión
- [ ] Se registra quién modificó y cuándo
- [ ] Se notifica a usuarios que tengan el servidor en favoritos (opcional)

### Validaciones
- IP+Puerto debe seguir siendo único
- Si se cambian credenciales, debe probar conexión
- Solo Editor, Admin y Super Admin pueden editar

### Tareas Técnicas
1. Crear modal de edición de servidor
2. Crear endpoint PUT /api/v1/servers/:id
3. Implementar actualización de credenciales encriptadas
4. Validar nueva conexión si cambian credenciales
5. Registrar cambios en auditoría

**Estimación:** 8 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Editor, Admin, Super Admin

---

## HU-014: Eliminar Servidor VPS
**Como** administrador  
**Quiero** eliminar servidores que ya no se usan  
**Para** mantener la lista actualizada

### Criterios de Aceptación
- [ ] Botón de eliminar en lista y detalles de servidor
- [ ] Confirmación requiere escribir nombre del servidor
- [ ] Se verifica que no hay operaciones en curso
- [ ] Se eliminan también las credenciales asociadas
- [ ] El historial de comandos ejecutados se mantiene (para auditoría)
- [ ] Se registra la eliminación en auditoría

### Validaciones
- Solo Admin y Super Admin pueden eliminar
- Usuario debe confirmar escribiendo nombre exacto
- No se puede eliminar servidor con operaciones activas

### Tareas Técnicas
1. Crear endpoint DELETE /api/v1/servers/:id
2. Implementar validación de operaciones activas
3. Eliminar credenciales de forma segura
4. Mantener historial en auditoría
5. Crear modal de confirmación

**Estimación:** 6 horas  
**Prioridad:** BAJA  
**Rol asignado:** Admin, Super Admin

---

## HU-015: Probar Conexión a Servidor
**Como** usuario  
**Quiero** verificar que un servidor está accesible  
**Para** confirmar que las credenciales son correctas

### Criterios de Aceptación
- [ ] Botón "Probar conexión" disponible en detalles de servidor
- [ ] Se muestra spinner mientras se prueba
- [ ] Resultado exitoso muestra mensaje verde con checkmark
- [ ] Resultado fallido muestra mensaje rojo con detalle del error
- [ ] Se registra el intento de conexión
- [ ] Se actualiza el estado del servidor después de probar

### Validaciones
- Todos los roles pueden probar conexión
- Timeout de 10 segundos
- Se maneja correctamente errores de red

### Tareas Técnicas
1. Crear endpoint POST /api/v1/servers/:id/test-connection
2. Implementar conexión SSH con timeout
3. Manejar diferentes tipos de errores
4. Actualizar estado del servidor
5. Crear UI del botón con feedback visual

**Estimación:** 6 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Todos

---

## HU-016: Generar y Gestionar Llaves SSH
**Como** editor  
**Quiero** generar llaves SSH desde el portal  
**Para** facilitar la autenticación sin contraseñas

### Criterios de Aceptación
- [ ] Formulario para generar nueva llave SSH
- [ ] Campos: nombre de la llave, passphrase (opcional), tipo (ed25519/rsa)
- [ ] Se genera el par de llaves (pública y privada)
- [ ] Se muestra la llave pública para copiar
- [ ] La llave privada se almacena encriptada
- [ ] Se puede descargar la llave privada (una sola vez)
- [ ] Se calcula y muestra el fingerprint
- [ ] Se puede configurar fecha de expiración

### Validaciones
- Nombre de llave debe ser único por usuario
- Passphrase mínimo 8 caracteres (si se usa)
- Llave privada solo se muestra una vez al crear

### Tareas Técnicas
1. Crear formulario de generación de llaves
2. Implementar generación con ssh-keygen (child_process)
3. Crear endpoint POST /api/v1/ssh/keys
4. Encriptar llave privada antes de almacenar
5. Calcular fingerprint
6. Implementar descarga segura de llave privada
7. Implementar expiración de llaves

**Estimación:** 12 horas  
**Prioridad:** ALTA  
**Rol asignado:** Editor, Admin, Super Admin

---

# Épica 4: Operaciones SSH y Terminal
**Tiempo Total:** 54 horas

## HU-017: Ejecutar Comandos SSH
**Como** editor  
**Quiero** ejecutar comandos en servidores remotos  
**Para** realizar tareas de administración

### Criterios de Aceptación
- [ ] Formulario para ejecutar comando ad-hoc
- [ ] Selector de servidor destino
- [ ] Campo de texto para escribir comando
- [ ] Validación de comando antes de ejecutar
- [ ] Comandos peligrosos requieren confirmación adicional
- [ ] Se muestra output en tiempo real
- [ ] Se puede detener ejecución si tarda mucho
- [ ] Se guarda en historial de comandos

### Validaciones
- Usuario debe tener permisos según tipo de comando
- Comandos de la lista negra están bloqueados (rm -rf /, dd, etc.)
- Timeout de 30 segundos por defecto
- Se sanitiza el input para prevenir injection

### Tareas Técnicas
1. Crear formulario de ejecución de comandos
2. Crear endpoint POST /api/v1/ssh/execute
3. Implementar whitelist/blacklist de comandos
4. Ejecutar comando vía SSH
5. Transmitir output en tiempo real (WebSocket)
6. Implementar timeout y cancelación
7. Guardar en historial

**Estimación:** 14 horas  
**Prioridad:** ALTA  
**Rol asignado:** Editor, Admin, Super Admin

---

## HU-018: Usar Terminal Web Interactiva
**Como** editor  
**Quiero** tener una terminal interactiva en el navegador  
**Para** trabajar como si estuviera en SSH local

### Criterios de Aceptación
- [ ] Terminal web que emula experiencia de consola
- [ ] Se puede escribir comandos y ver output
- [ ] Soporta colores ANSI
- [ ] Funciona el historial con flechas arriba/abajo
- [ ] Se puede abrir múltiples terminales (tabs)
- [ ] La sesión se mantiene mientras el navegador está abierto
- [ ] Se puede copiar y pegar texto

### Validaciones
- Solo Editor, Admin y Super Admin pueden usar terminal
- Sesión expira después de 30 minutos de inactividad
- Se registran todos los comandos ejecutados

### Tareas Técnicas
1. Integrar xterm.js
2. Crear componente WebTerminal
3. Implementar conexión WebSocket para I/O
4. Implementar shell interactivo en backend (ssh2)
5. Manejar múltiples sesiones concurrentes
6. Implementar historial de comandos
7. Registrar actividad en auditoría

**Estimación:** 16 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Editor, Admin, Super Admin

---

## HU-019: Ver Historial de Comandos
**Como** visualizador  
**Quiero** ver el historial de comandos ejecutados  
**Para** auditar las acciones realizadas

### Criterios de Aceptación
- [ ] Tabla con historial de todos los comandos ejecutados
- [ ] Columnas: fecha/hora, usuario, servidor, comando, resultado (éxito/error)
- [ ] Se puede filtrar por usuario
- [ ] Se puede filtrar por servidor
- [ ] Se puede filtrar por fecha
- [ ] Se puede buscar por texto del comando
- [ ] Se puede ver el output completo de un comando
- [ ] Paginación de resultados

### Validaciones
- Todos los roles pueden ver historial
- Visualizador solo ve comandos, no puede ejecutar
- Se muestran máximo 100 resultados por página

### Tareas Técnicas
1. Crear tabla de historial de comandos
2. Crear endpoint GET /api/v1/ssh/history
3. Implementar filtros múltiples
4. Implementar búsqueda
5. Crear modal para ver output completo
6. Implementar paginación

**Estimación:** 10 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Todos

---

## HU-020: Crear y Ejecutar Plantillas de Scripts
**Como** editor  
**Quiero** crear scripts reutilizables  
**Para** automatizar tareas repetitivas

### Criterios de Aceptación
- [ ] Formulario para crear plantilla de script
- [ ] Campos: nombre, descripción, categoría, script, parámetros
- [ ] Se pueden definir parámetros variables (ej: {{hostname}})
- [ ] Al ejecutar plantilla, se solicitan los parámetros
- [ ] Se puede ejecutar en uno o múltiples servidores
- [ ] Se muestra progreso de ejecución
- [ ] Se guardan resultados de cada ejecución
- [ ] Se pueden compartir plantillas con otros usuarios

### Validaciones
- Nombre de plantilla único por usuario
- Script debe ser válido
- Parámetros deben estar definidos correctamente
- Solo usuarios autorizados pueden ejecutar según categoría

### Tareas Técnicas
1. Crear modelo Template en base de datos
2. Crear formulario de creación de plantillas
3. Implementar sistema de parámetros (handlebars)
4. Crear endpoint POST /api/v1/templates
5. Crear endpoint POST /api/v1/templates/:id/execute
6. Implementar ejecución en lote
7. Guardar resultados de ejecuciones
8. Implementar compartir plantillas

**Estimación:** 14 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Editor, Admin, Super Admin

---

# Épica 5: Monitoreo y Alertas
**Tiempo Total:** 44 horas

## HU-021: Ver Dashboard de Monitoreo
**Como** visualizador  
**Quiero** ver un dashboard con el estado general  
**Para** tener una visión global de la infraestructura

### Criterios de Aceptación
- [ ] Dashboard muestra métricas clave en cards
- [ ] Total de servidores (online/offline)
- [ ] Comandos ejecutados hoy/esta semana
- [ ] Alertas activas
- [ ] Gráfico de uso de CPU promedio
- [ ] Gráfico de uso de RAM promedio
- [ ] Lista de últimas actividades
- [ ] Se actualiza automáticamente cada minuto

### Validaciones
- Todos los roles pueden ver dashboard
- Datos se cargan rápidamente
- Gráficos son responsive

### Tareas Técnicas
1. Crear componente Dashboard
2. Crear cards de métricas
3. Integrar librería de gráficos (Chart.js)
4. Crear endpoint GET /api/v1/dashboard/stats
5. Implementar auto-refresh
6. Hacer responsive

**Estimación:** 12 horas  
**Prioridad:** ALTA  
**Rol asignado:** Todos

---

## HU-022: Ver Métricas Detalladas de Servidor
**Como** usuario  
**Quiero** ver métricas detalladas de un servidor  
**Para** monitorear su rendimiento

### Criterios de Aceptación
- [ ] Gráficos de CPU, RAM, Disco, Red en tiempo real
- [ ] Se puede seleccionar rango de tiempo (1h, 6h, 24h, 7d, 30d)
- [ ] Se muestran valores mínimo, máximo y promedio
- [ ] Se pueden ver múltiples métricas simultáneamente
- [ ] Se actualiza automáticamente
- [ ] Se puede exportar gráfico como imagen

### Validaciones
- Datos se recopilan cada 1 minuto
- Historial se mantiene por 30 días
- Gráficos se renderizan eficientemente

### Tareas Técnicas
1. Implementar recopilación de métricas vía SSH
2. Almacenar en time-series database
3. Crear endpoint GET /api/v1/servers/:id/metrics
4. Crear componente ServerMetrics
5. Implementar gráficos interactivos
6. Implementar selector de rango
7. Implementar exportación

**Estimación:** 14 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Todos

---

## HU-023: Configurar Alertas
**Como** administrador  
**Quiero** configurar alertas automáticas  
**Para** ser notificado de problemas

### Criterios de Aceptación
- [ ] Formulario para crear regla de alerta
- [ ] Se puede seleccionar métrica: CPU, RAM, Disco, Estado
- [ ] Se puede definir umbral (ej: CPU > 80%)
- [ ] Se puede seleccionar duración (ej: 5 minutos)
- [ ] Se puede configurar método de notificación (email, in-app)
- [ ] Se puede configurar cooldown entre alertas
- [ ] Se pueden desactivar alertas temporalmente

### Validaciones
- Solo Admin y Super Admin pueden configurar alertas
- Umbral debe ser un valor válido
- Duración mínima es 1 minuto

### Tareas Técnicas
1. Crear modelo AlertRule en base de datos
2. Crear formulario de configuración de alertas
3. Crear endpoint POST /api/v1/alerts/rules
4. Implementar evaluador de reglas
5. Implementar notificaciones
6. Implementar cooldown

**Estimación:** 12 horas  
**Prioridad:** MEDIA  
**Rol asignado:** Admin, Super Admin

---

## HU-024: Ver Historial de Alertas
**Como** usuario  
**Quiero** ver el historial de alertas  
**Para** analizar incidentes pasados

### Criterios de Aceptación
- [ ] Tabla con todas las alertas disparadas
- [ ] Columnas: fecha, servidor, tipo de alerta, valor, estado (activa/resuelta)
- [ ] Se puede filtrar por servidor
- [ ] Se puede filtrar por tipo de alerta
- [ ] Se puede filtrar por estado
- [ ] Se puede marcar como resuelta manualmente
- [ ] Se puede agregar notas a una alerta

### Validaciones
- Todos los roles pueden ver alertas
- Solo Admin puede marcar como resuelta

### Tareas Técnicas
1. Crear modelo Alert en base de datos
2. Crear endpoint GET /api/v1/alerts
3. Crear tabla de alertas
4. Implementar filtros
5. Implementar marcar como resuelta
6. Implementar agregar notas

**Estimación:** 10 horas  
**Prioridad:** BAJA  
**Rol asignado:** Todos

---

## Resumen por Rol

### Super Admin (238 horas)
- Acceso total a todas las funcionalidades
- Gestión de usuarios sin restricciones
- Configuración del sistema
- Eliminación de usuarios
- Todas las operaciones de Editor, Admin y Visualizador

### Admin (222 horas)
- Gestión de usuarios (excepto otros admins)
- Gestión completa de servidores
- Ejecución de comandos (incluyendo críticos)
- Configuración de alertas
- Ver auditoría
- Todas las operaciones de Editor y Visualizador

### Editor (182 horas)
- Gestión de servidores (agregar, editar)
- Generar y gestionar llaves SSH
- Ejecutar comandos básicos
- Crear y ejecutar plantillas
- Terminal web
- Todas las operaciones de Visualizador

### Visualizador (98 horas)
- Ver servidores y su estado
- Ver métricas y dashboard
- Ver historial de comandos
- Ver documentación
- Ver alertas
- Probar conexión a servidores

---

**Versión:** 1.0.0  
**Última actualización:** Febrero 2026  
**Total de Puntos de Historia:** 238 horas  
**Velocidad estimada:** 40 horas/sprint (2 semanas)  
**Sprints necesarios:** 6 sprints
