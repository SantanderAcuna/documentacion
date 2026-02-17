# Política de Seguridad

## Versiones Soportadas

| Versión | Soportada          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Reportar una Vulnerabilidad

La seguridad del CMS Gubernamental es nuestra máxima prioridad. Agradecemos su ayuda en mantener el sistema seguro.

### ⚠️ NO Reportar Vulnerabilidades Públicamente

**Por favor, NO abra issues públicos de GitHub para reportar vulnerabilidades de seguridad.**

### 🔒 Cómo Reportar de Forma Segura

1. **Email:** Envíe un correo a `security@alcaldia.gov.co` con:
   - Descripción detallada de la vulnerabilidad
   - Pasos para reproducir
   - Posible impacto
   - Sugerencias de solución (si tiene)

2. **Respuesta:** Recibirá confirmación en 48 horas

3. **Coordinación:** Trabajaremos con usted para:
   - Validar la vulnerabilidad
   - Desarrollar un parche
   - Coordinar la divulgación responsable

### 📋 Qué Reportar

Reportar vulnerabilidades relacionadas con:

- ✅ Inyección SQL
- ✅ Cross-Site Scripting (XSS)
- ✅ Cross-Site Request Forgery (CSRF)
- ✅ Autenticación y autorización
- ✅ Exposición de datos sensibles
- ✅ Configuraciones inseguras
- ✅ Dependencias vulnerables

### ⏱️ Tiempos de Respuesta

| Severidad | Tiempo de Respuesta | Tiempo de Parche |
|-----------|---------------------|------------------|
| Crítica   | 24 horas           | 7 días          |
| Alta      | 48 horas           | 14 días         |
| Media     | 7 días             | 30 días         |
| Baja      | 14 días            | 60 días         |

### 🏆 Reconocimientos

Agradecemos a los investigadores de seguridad que reportan vulnerabilidades de manera responsable. Con su permiso, los incluiremos en nuestro [Hall of Fame de Seguridad](#hall-of-fame).

## Medidas de Seguridad Implementadas

### Autenticación
- Laravel Sanctum con cookies HTTP-Only
- Rate limiting: 5 intentos / 15 minutos
- Contraseñas hasheadas con bcrypt (cost 12)

### Prevención de Ataques
- SQL Injection: Eloquent ORM (prepared statements)
- XSS: Blade auto-escaping
- CSRF: Tokens en todos los formularios
- CORS: Configuración estricta

### Datos
- Encriptación de datos sensibles
- Cumplimiento Ley 1581/2012
- Auditoría completa con spatie/laravel-activitylog
- Retención de logs: 1 año

### Infraestructura
- HTTPS obligatorio con HSTS
- Headers de seguridad configurados
- Firewall (UFW) en producción
- Docker con least privilege

### Dependencias
- Actualizaciones regulares
- Escaneo con Trivy en CI/CD
- Composer audit en backend
- npm audit en frontends

## Cumplimiento Normativo

- ✅ Ley 1581/2012 - Protección de Datos Personales
- ✅ Ley 1712/2014 - Transparencia
- ✅ Decreto 1078/2015 - Gobierno Digital

## Auditorías

- Auditoría de seguridad trimestral
- Análisis estático en cada PR (PHPStan, ESLint)
- Tests de seguridad automatizados
- Penetration testing anual (recomendado)

## Contacto

- **Email de Seguridad:** security@alcaldia.gov.co
- **PGP Key:** [Pendiente]

## Hall of Fame

Investigadores que han contribuido a la seguridad del proyecto:

<!-- Los nombres se agregarán aquí -->

---

**Gracias por ayudarnos a mantener el CMS Gubernamental seguro.** 🔒
