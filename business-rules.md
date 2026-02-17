# Reglas de Negocio - Portal de Configuración VPS

## Resumen Ejecutivo

Este documento establece las **30 reglas de negocio** organizadas en **10 categorías** que rigen el comportamiento del Portal de Configuración VPS. Estas reglas son imperativas y deben cumplirse en todo momento durante el desarrollo e implementación del sistema.

---

## Categoría 1: Autenticación y Seguridad de Acceso
**Dominio:** Gestión de identidad y control de acceso

### RN-001: Complejidad de Contraseñas
**Descripción:** Todas las contraseñas de usuario deben cumplir requisitos mínimos de seguridad.

**Regla:**
- Longitud mínima: 8 caracteres
- Debe contener al menos: 1 mayúscula, 1 minúscula, 1 número, 1 carácter especial
- No puede contener el email del usuario
- No puede ser una de las 10,000 contraseñas más comunes (validar contra lista)

**Impacto:** Crítico  
**Validación:** En registro y cambio de contraseña  
**Excepción:** Ninguna

---

### RN-002: Bloqueo por Intentos Fallidos
**Descripción:** El sistema debe protegerse contra ataques de fuerza bruta.

**Regla:**
- Máximo 5 intentos fallidos de login consecutivos
- Después del 5to intento, la cuenta se bloquea por 15 minutos
- Después de 10 intentos en 1 hora, bloqueo de 1 hora
- El contador se reinicia después de un login exitoso
- Se notifica al usuario por email sobre el bloqueo

**Impacto:** Alto  
**Validación:** En cada intento de login  
**Excepción:** Usuarios con rol Super Admin pueden desbloquear manualmente

---

### RN-003: Expiración de Tokens JWT
**Descripción:** Los tokens de autenticación tienen vida útil limitada.

**Regla:**
- Access token expira en 24 horas
- Refresh token expira en 7 días
- Token de reseteo de contraseña expira en 1 hora
- Tokens de verificación de email expiran en 24 horas
- Después de expiración, se requiere nueva autenticación

**Impacto:** Alto  
**Validación:** En cada request autenticado  
**Excepción:** Ninguna

---

## Categoría 2: Control de Acceso Basado en Roles (RBAC)

### RN-004: Jerarquía de Roles
**Descripción:** Los roles tienen una jerarquía estricta de permisos.

**Regla:**
```
Super Admin > Admin > Editor > Visualizador
```

- Super Admin: acceso completo, incluyendo gestión de otros admins
- Admin: puede gestionar usuarios (excepto super admins), servidores y configuraciones
- Editor: puede gestionar servidores y ejecutar comandos no críticos
- Visualizador: solo lectura de información

**Impacto:** Crítico  
**Validación:** En cada operación  
**Excepción:** Ninguna

---

### RN-005: Principio de Mínimo Privilegio
**Descripción:** Los usuarios solo deben tener los permisos estrictamente necesarios.

**Regla:**
- Nuevos usuarios se crean como Visualizador por defecto
- Los permisos se asignan explícitamente, no por defecto
- No se pueden auto-elevar privilegios
- Cambios de rol requieren aprobación de Admin o Super Admin

**Impacto:** Alto  
**Validación:** En creación y modificación de usuarios  
**Excepción:** Primer usuario del sistema se crea como Super Admin

---

### RN-006: Restricción de Auto-modificación
**Descripción:** Los usuarios no pueden modificar sus propios permisos o estado.

**Regla:**
- No se puede cambiar el propio rol
- No se puede desactivar la propia cuenta
- No se puede eliminar la propia cuenta
- Debe existir al menos 1 Super Admin activo en todo momento

**Impacto:** Crítico  
**Validación:** En operaciones de usuarios  
**Excepción:** Ninguna

---

## Categoría 3: Gestión de Servidores VPS

### RN-007: Unicidad de Servidores
**Descripción:** No pueden existir servidores duplicados.

**Regla:**
- La combinación IP + Puerto debe ser única en el sistema
- No se permiten dos servidores con la misma IP y puerto
- Al intentar crear duplicado, mostrar error específico

**Impacto:** Alto  
**Validación:** Al crear o editar servidor  
**Excepción:** Ninguna

---

### RN-008: Validación de Conexión Obligatoria
**Descripción:** Las credenciales deben validarse antes de guardar un servidor.

**Regla:**
- Al crear un servidor, se debe probar la conexión SSH
- Si falla la conexión, mostrar advertencia (no error fatal)
- Usuario puede optar por guardar de todas formas (bajo su responsabilidad)
- Servidores sin conexión validada se marcan con bandera de advertencia

**Impacto:** Medio  
**Validación:** Al crear servidor  
**Excepción:** Usuario puede omitir validación con confirmación explícita

---

### RN-009: Encriptación de Credenciales
**Descripción:** Las credenciales SSH deben almacenarse de forma segura.

**Regla:**
- Contraseñas SSH se encriptan con AES-256 antes de almacenar
- Llaves SSH privadas se encriptan con AES-256
- La clave de encriptación se almacena en variable de entorno (ENCRYPTION_KEY)
- Las credenciales nunca se muestran en logs
- Solo se pueden desencriptar al momento de uso

**Impacto:** Crítico  
**Validación:** En almacenamiento y recuperación  
**Excepción:** Ninguna

---

## Categoría 4: Ejecución de Comandos SSH

### RN-010: Lista Negra de Comandos Peligrosos
**Descripción:** Ciertos comandos están prohibidos por riesgo de seguridad.

**Regla:**
Comandos bloqueados:
- `rm -rf /` (eliminar sistema)
- `dd if=/dev/zero of=/dev/sda` (borrar disco)
- `:(){ :|:& };:` (fork bomb)
- `mkfs.*` (formatear discos)
- `shutdown`, `reboot`, `halt` (requieren permiso especial)

**Impacto:** Crítico  
**Validación:** Antes de ejecutar comando  
**Excepción:** Super Admin puede ejecutar con confirmación de doble factor

---

### RN-011: Timeout de Comandos
**Descripción:** Los comandos SSH no pueden ejecutarse indefinidamente.

**Regla:**
- Timeout por defecto: 30 segundos
- Timeout máximo configurable: 5 minutos
- Comandos de larga duración deben ejecutarse en segundo plano
- Si se excede el timeout, se termina la conexión SSH

**Impacto:** Medio  
**Validación:** Durante ejecución  
**Excepción:** Comandos whitelisteados pueden tener timeout extendido

---

### RN-012: Auditoría de Comandos
**Descripción:** Todos los comandos ejecutados deben quedar registrados.

**Regla:**
- Se registra: usuario, servidor, comando, timestamp, resultado (éxito/error)
- Los logs son inmutables (no se pueden editar ni eliminar)
- Los logs se retienen por mínimo 90 días
- Se registra la IP desde donde se ejecutó el comando
- Output completo se almacena (limitado a 100KB)

**Impacto:** Alto  
**Validación:** Después de cada ejecución  
**Excepción:** Ninguna

---

## Categoría 5: Gestión de Llaves SSH

### RN-013: Generación Segura de Llaves
**Descripción:** Las llaves SSH deben generarse con algoritmos seguros.

**Regla:**
- Algoritmo por defecto: ed25519
- Alternativa permitida: RSA 4096 bits
- No se permiten: RSA < 2048 bits, DSA
- Passphrase es opcional pero recomendado (mínimo 8 caracteres)

**Impacto:** Alto  
**Validación:** Al generar llaves  
**Excepción:** Ninguna

---

### RN-014: Expiración de Llaves SSH
**Descripción:** Las llaves SSH tienen fecha de vencimiento.

**Regla:**
- Llaves nuevas tienen vencimiento por defecto: 1 año
- Se envía notificación 30 días antes de expirar
- Llaves expiradas no pueden usarse para autenticación
- Llaves pueden renovarse (genera nuevo par)

**Impacto:** Medio  
**Validación:** Al usar llave  
**Excepción:** Super Admin puede extender vencimiento

---

### RN-015: Descarga Única de Llave Privada
**Descripción:** La llave privada solo puede descargarse una vez.

**Regla:**
- Al crear llave, se permite 1 descarga de la llave privada
- Después de descarga, ya no se puede recuperar
- Si se pierde, debe generarse nuevo par de llaves
- Se registra en auditoría cuando se descarga

**Impacto:** Medio  
**Validación:** Al descargar llave  
**Excepción:** Ninguna

---

## Categoría 6: Monitoreo y Métricas

### RN-016: Frecuencia de Recopilación de Métricas
**Descripción:** Las métricas se recopilan en intervalos regulares.

**Regla:**
- Métricas básicas (CPU, RAM, Disco): cada 1 minuto
- Métricas de red: cada 5 minutos
- Disponibilidad (ping): cada 30 segundos
- Procesos y servicios: cada 5 minutos

**Impacto:** Medio  
**Validación:** Por scheduler  
**Excepción:** Puede ajustarse por rendimiento

---

### RN-017: Retención de Métricas
**Descripción:** Las métricas históricas se almacenan por tiempo limitado.

**Regla:**
- Datos de última hora: granularidad de 1 minuto
- Datos de últimas 24 horas: granularidad de 5 minutos
- Datos de últimos 7 días: granularidad de 15 minutos
- Datos de últimos 30 días: granularidad de 1 hora
- Datos > 30 días: se eliminan o archivan

**Impacto:** Bajo  
**Validación:** Daily cleanup job  
**Excepción:** Datos críticos pueden retenerse más tiempo

---

### RN-018: Umbrales de Alertas
**Descripción:** Las alertas se disparan según umbrales predefinidos.

**Regla:**
Umbrales por defecto:
- CPU > 80% por 5 minutos → Advertencia
- CPU > 95% por 2 minutos → Crítico
- RAM > 90% por 5 minutos → Advertencia
- Disco > 85% → Advertencia
- Disco > 95% → Crítico
- Servidor offline > 2 minutos → Crítico

**Impacto:** Medio  
**Validación:** Cada minuto  
**Excepción:** Umbrales configurables por servidor

---

## Categoría 7: Plantillas y Automatización

### RN-019: Validación de Plantillas
**Descripción:** Las plantillas de scripts deben validarse antes de ejecutar.

**Regla:**
- Plantillas deben declarar todos los parámetros requeridos
- Parámetros deben tener tipo (string, number, boolean)
- Se debe validar que todos los parámetros estén provistos antes de ejecutar
- Plantillas no pueden contener comandos de la lista negra

**Impacto:** Alto  
**Validación:** Antes de ejecutar plantilla  
**Excepción:** Ninguna

---

### RN-020: Ejecución en Lote
**Descripción:** Las plantillas pueden ejecutarse en múltiples servidores simultáneamente.

**Regla:**
- Máximo 10 servidores en paralelo
- Si falla en un servidor, continúa en los demás
- Se muestra progreso en tiempo real
- Resultados se almacenan individualmente por servidor

**Impacto:** Medio  
**Validación:** Durante ejecución en lote  
**Excepción:** Super Admin puede aumentar límite

---

## Categoría 8: Notificaciones

### RN-021: Canales de Notificación
**Descripción:** Las notificaciones se envían por múltiples canales.

**Regla:**
- Notificaciones in-app: siempre habilitadas
- Notificaciones por email: configurables por usuario
- Tipos de notificaciones:
  - Alertas de servidor (críticas, advertencias)
  - Cambios de permisos
  - Operaciones de seguridad
  - Vencimiento de llaves SSH

**Impacto:** Bajo  
**Validación:** Al generar notificación  
**Excepción:** Notificaciones críticas siempre se envían

---

### RN-022: Cooldown de Alertas
**Descripción:** Las alertas no deben spammear a los usuarios.

**Regla:**
- Misma alerta no se repite en menos de 15 minutos
- Máximo 10 notificaciones del mismo tipo por hora
- Alertas críticas tienen cooldown de 5 minutos
- Si problema persiste, se agrupa en "alerta continua"

**Impacto:** Bajo  
**Validación:** Al enviar alerta  
**Excepción:** Ninguna

---

## Categoría 9: Datos y Privacidad

### RN-023: Anonimización de Datos
**Descripción:** Los datos sensibles deben anonimizarse en reportes.

**Regla:**
- IPs de usuarios se muestran parcialmente (192.168.1.XXX)
- Emails se ofuscan en logs públicos (u***@example.com)
- Contraseñas nunca se muestran ni en logs
- Datos de auditoría completos solo para Admin/Super Admin

**Impacto:** Alto  
**Validación:** Al generar reportes  
**Excepción:** Auditoría interna tiene acceso completo

---

### RN-024: Derecho al Olvido
**Descripción:** Los usuarios pueden solicitar eliminación de sus datos.

**Regla:**
- Usuario puede solicitar eliminación de cuenta
- Proceso de eliminación toma 30 días (período de gracia)
- Después de 30 días, datos personales se anonimizan
- Logs de auditoría se mantienen pero con datos anonimizados
- La eliminación es irreversible

**Impacto:** Medio  
**Validación:** Al solicitar eliminación  
**Excepción:** Datos requeridos por ley se mantienen anonimizados

---

## Categoría 10: Integridad y Respaldo

### RN-025: Backups Automáticos
**Descripción:** El sistema debe respaldar datos críticos automáticamente.

**Regla:**
- Backup de base de datos: diario a las 2 AM
- Backup de archivos de configuración: diario
- Retención de backups: 7 días diarios, 4 semanales, 3 mensuales
- Backups se almacenan encriptados
- Se verifica integridad de backups semanalmente

**Impacto:** Crítico  
**Validación:** Job nocturno  
**Excepción:** Ninguna

---

### RN-026: Recuperación ante Desastres
**Descripción:** El sistema debe poder recuperarse de fallos.

**Regla:**
- RTO (Recovery Time Objective): 4 horas
- RPO (Recovery Point Objective): 24 horas
- Plan de recuperación documentado y probado trimestralmente
- Backup offsite en ubicación geográfica diferente
- Procedimiento de failover documentado

**Impacto:** Alto  
**Validación:** Pruebas trimestrales  
**Excepción:** Ninguna

---

### RN-027: Versionado de Configuración
**Descripción:** Los cambios de configuración deben versionarse.

**Regla:**
- Cada cambio de configuración genera nueva versión
- Se puede revertir a versión anterior
- Se registra quién hizo el cambio y cuándo
- Máximo 50 versiones históricas por recurso

**Impacto:** Medio  
**Validación:** Al modificar configuración  
**Excepción:** Ninguna

---

## Categoría Especial: Performance y Escalabilidad

### RN-028: Límites de Uso
**Descripción:** El sistema tiene límites para prevenir abuso.

**Regla:**
- Máximo 100 servidores por cuenta
- Máximo 50 llaves SSH por usuario
- Máximo 1000 comandos ejecutados por día por usuario
- Máximo 100 plantillas por usuario
- Límites pueden aumentarse por Super Admin

**Impacto:** Bajo  
**Validación:** Al crear recursos  
**Excepción:** Super Admin puede aumentar límites

---

### RN-029: Rate Limiting de API
**Descripción:** Las APIs tienen límites de tasa para prevenir abuso.

**Regla:**
- Endpoints públicos: 100 requests/minuto por IP
- Endpoints autenticados: 300 requests/minuto por usuario
- Ejecución de comandos: 30 requests/minuto por usuario
- Subida de archivos: 10 requests/minuto por usuario
- Respuesta HTTP 429 cuando se excede límite

**Impacto:** Alto  
**Validación:** Por middleware  
**Excepción:** Ninguna

---

### RN-030: Tiempo de Respuesta SLA
**Descripción:** El sistema debe responder en tiempo razonable.

**Regla:**
- API debe responder en < 500ms (percentil 95)
- Página web debe cargar en < 2 segundos
- Conexión SSH debe establecerse en < 3 segundos
- Queries de base de datos < 100ms (percentil 95)
- Si se excede, generar alerta para equipo técnico

**Impacto:** Alto  
**Validación:** Monitoreo continuo  
**Excepción:** Operaciones de larga duración (backups, etc.)

---

## Matriz de Impacto

| Categoría | Reglas Críticas | Reglas Altas | Reglas Medias | Reglas Bajas |
|-----------|----------------|--------------|---------------|--------------|
| Autenticación y Seguridad | 2 | 1 | 0 | 0 |
| RBAC | 2 | 1 | 0 | 0 |
| Gestión de Servidores | 1 | 1 | 1 | 0 |
| Ejecución de Comandos | 1 | 1 | 1 | 0 |
| Gestión de Llaves SSH | 0 | 1 | 2 | 0 |
| Monitoreo y Métricas | 0 | 0 | 2 | 1 |
| Plantillas | 0 | 1 | 1 | 0 |
| Notificaciones | 0 | 0 | 0 | 2 |
| Datos y Privacidad | 0 | 1 | 1 | 0 |
| Integridad y Respaldo | 1 | 1 | 1 | 0 |
| Performance | 0 | 2 | 0 | 1 |

**Total:** 7 Críticas, 10 Altas, 9 Medias, 4 Bajas

---

## Proceso de Validación de Reglas

### En Desarrollo
1. Cada regla debe tener tests automatizados
2. Code review debe verificar cumplimiento
3. No se puede hacer merge si viola reglas críticas

### En Producción
1. Monitoreo continuo de cumplimiento
2. Alertas cuando se detecta violación
3. Revisión mensual de reglas

### Actualización de Reglas
1. Propuesta de cambio debe justificarse
2. Requiere aprobación de Tech Lead y Security Officer
3. Se documenta en changelog
4. Se comunica a todos los stakeholders

---

**Versión:** 1.0.0  
**Última actualización:** Febrero 2026  
**Aprobado por:** Tech Lead, Security Officer, Product Manager  
**Próxima revisión:** Mayo 2026
