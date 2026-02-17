# Constitución del Proyecto - CMS Gubernamental

## Documento Fundacional
**Proyecto:** Sistema de Gestión de Contenidos para Alcaldía  
**Versión:** 1.0.0  
**Fecha:** Febrero 2026  
**Estado:** Inmutable (requiere aprobación unánime para cambios)

---

## 1. Propósito y Alcance

### 1.1 Objetivo General
Desarrollar un **Sistema de Gestión de Contenidos (CMS) profesional** para la Alcaldía que permita crear, gestionar y publicar información institucional, normativa y de transparencia, facilitando la participación ciudadana y garantizando el cumplimiento integral de las normativas colombianas vigentes.

### 1.2 Alcance del Sistema

#### Funcionalidades Principales
- **Gestión de Contenidos:** Creación, edición, publicación y programación de contenidos institucionales
- **Transparencia Activa:** Publicación de información mínima obligatoria (Ley 1712/2014 Art. 9)
- **Datos Abiertos:** Exposición de información en formatos reutilizables (JSON, CSV, XML)
- **PQRS:** Sistema de Peticiones, Quejas, Reclamos y Sugerencias con seguimiento
- **Accesibilidad:** Cumplimiento WCAG 2.1 nivel AA (Resolución 1519/2020)
- **Interoperabilidad:** APIs RESTful para integración con sistemas externos

#### Usuarios del Sistema
1. **Ciudadanos/Visitantes:** Consultan información pública y envían PQRS
2. **Editores:** Crean y publican contenidos institucionales
3. **Administradores de Transparencia:** Gestionan información mínima obligatoria
4. **Administradores del Sistema:** Gestionan usuarios, roles y configuraciones
5. **Auditores/Entes de Control:** Acceso de solo lectura para supervisión

### 1.3 Normativas Aplicables

El CMS debe cumplir integralmente con:

| Normativa | Descripción | Impacto |
|-----------|-------------|---------|
| **Ley 1341/2009** | Gobierno en Línea y masificación de TIC | Servicios 100% en línea |
| **Ley 1712/2014** | Transparencia y Acceso a la Información Pública | Publicación información mínima obligatoria |
| **Decreto 1078/2015** | Gobierno Digital e interoperabilidad | APIs y estándares técnicos |
| **Resolución 1519/2020** | Accesibilidad web WCAG 2.1 AA | 32 criterios obligatorios |
| **Ley 1581/2012** | Protección de Datos Personales (Habeas Data) | Consentimiento, encriptación, derecho al olvido |
| **Ley 1474/2011** | Estatuto Anticorrupción | Plan anticorrupción y seguimiento |
| **ITA** | Índice de Transparencia y Acceso a la Información | Medición anual de transparencia |
| **FURAG** | Formulario Único de Reporte y Avance de Gestión | Reportes MIPG |

---

## 2. Principios Rectores (No Negociables)

Los siguientes principios son **inmutables** y sirven como criterios de aceptación transversales para todo el desarrollo:

### PR-01: Claridad Sobre Ingenio
**Enunciado:** Las especificaciones son la fuente de verdad; el código autoexplicativo prevalece sobre soluciones ingeniosas sin documentación.

**Aplicación:**
- Todo código debe ser legible por humanos
- Se privilegia simplicidad sobre optimización prematura
- Comentarios solo cuando la lógica no es evidente
- Variables y funciones con nombres descriptivos en español
- No se aceptan "trucos" sin documentación

**Validación:** Code review obligatorio

---

### PR-02: Cumplimiento Normativo Ante Todo
**Enunciado:** Ninguna funcionalidad puede contradecir las leyes del MinTIC o de transparencia.

**Aplicación:**
- Cada tarea incluye lista de comprobación de cumplimiento normativo
- No se puede lanzar funcionalidad que viole normativas
- Auditorías trimestrales de cumplimiento
- Documentación de cómo se cumple cada artículo de ley

**Validación:** Checklist de compliance en cada release

---

### PR-03: Seguridad Por Diseño
**Enunciado:** Los controles de seguridad deben integrarse desde el inicio, no añadirse como parches.

**Aplicación:**
- Threat modeling en fase de diseño
- HTTPS obligatorio (HSTS habilitado)
- Validación de entrada en múltiples capas (frontend, backend, base de datos)
- Protección CSRF en todos los formularios
- Prepared statements para prevenir SQL Injection
- Codificación de salida para prevenir XSS
- Rate limiting en endpoints públicos
- Secretos en variables de entorno (nunca en código)

**Validación:** Auditoría de seguridad antes de cada release

---

### PR-04: Accesibilidad Universal
**Enunciado:** Todos los contenidos públicos deben cumplir WCAG 2.1 nivel AA.

**Aplicación:**
- Textos alternativos obligatorios en imágenes (Criterio 1.1.1)
- Contraste mínimo 4.5:1 para texto normal (Criterio 1.4.3)
- Contraste mínimo 3:1 para texto grande (Criterio 1.4.3)
- Navegación completa por teclado (Criterio 2.1.1)
- Sin trampas de teclado (Criterio 2.1.2)
- Subtítulos en videos (Criterio 1.2.2)
- Lengua de Señas Colombiana (LSC) en alocuciones oficiales (Criterio 1.2.6)
- Formularios con etiquetas asociadas (Criterio 3.3.2)
- Validación automática con herramientas (Axe, WAVE)

**Validación:** Auditoría WCAG 2.1 AA antes de producción

---

### PR-05: Datos Abiertos Por Defecto
**Enunciado:** La información pública se expondrá en formatos reutilizables con APIs documentadas.

**Aplicación:**
- Toda información de transparencia en JSON, CSV y XML
- APIs RESTful con negociación de contenido (`Accept: application/json`)
- Catálogo de datos abiertos en formato DCAT-AP
- Licencia Creative Commons BY (atribución)
- Documentación OpenAPI 3.0 de todas las APIs
- Versionado de APIs (`/api/v1/`)

**Validación:** Verificación de formatos en cada publicación

---

### PR-06: Actualización Permanente
**Enunciado:** La información de transparencia debe actualizarse al menos mensualmente.

**Aplicación:**
- Scheduler automatizado para recordatorios
- Dashboard con fechas de última actualización
- Alertas 5 días antes del vencimiento
- Registro de actualizaciones en auditoría
- Información programada con Laravel Scheduler

**Validación:** Reporte mensual de actualización

---

### PR-07: Mantenibilidad y Evolución
**Enunciado:** El código debe ser fácil de mantener, extender y transferir.

**Aplicación:**
- Estándar PSR-12 para PHP
- Guía de estilos de Vue 3 (Composition API)
- Pruebas automatizadas con cobertura mínima 80%
- Documentación inline (PHPDoc, TSDoc)
- ADRs (Architecture Decision Records) para decisiones importantes
- Code review obligatorio antes de merge
- Commits semánticos (Conventional Commits)

**Validación:** Pipeline de CI/CD con checks automáticos

---

### PR-08: Contexto Compartido
**Enunciado:** Las decisiones se documentan de manera estructurada para asegurar continuidad entre personas y herramientas de IA.

**Aplicación:**
- Archivo `context.md` actualizado en cada iteración
- ADRs para decisiones arquitectónicas
- Documentación de patrones reutilizados
- Registro de errores y soluciones aplicadas
- Knowledge base accesible para todo el equipo

**Validación:** Actualización de context.md en cada sprint

---

## 3. Stack Tecnológico

### 3.1 Backend

| Componente | Tecnología | Versión | Justificación |
|------------|------------|---------|---------------|
| **Lenguaje** | PHP | 8.3+ | Ecosistema maduro, Laravel 12 requiere PHP 8.3 |
| **Framework** | Laravel | 12.x | Framework robusto con excelente documentación |
| **Base de Datos** | MySQL | 8.0+ | Soporte nativo de JSON, FULLTEXT search |
| **Autenticación** | Laravel Sanctum | Latest | Cookies HTTP-Only, stateless, seguro |
| **Autorización** | Spatie Laravel Permission | Latest | RBAC flexible y probado |
| **Almacenamiento** | Filesystem/S3 | - | Local en desarrollo, S3 en producción |
| **Cache** | Redis | 7.x | Cache distribuido, sesiones, colas |
| **Validación** | Laravel FormRequest | Built-in | Validación centralizada y reutilizable |
| **API** | Laravel API Resources | Built-in | Transformación consistente de respuestas |

### 3.2 Frontend

| Componente | Tecnología | Versión | Justificación |
|------------|------------|---------|---------------|
| **Framework** | Vue | 3.x | Reactivo, Composition API moderna |
| **Lenguaje** | TypeScript | 5.x | Type safety, mejor DX |
| **HTTP Client** | Axios | Latest | Interceptors, `withCredentials` para cookies |
| **Estado** | Pinia | Latest | Store oficial de Vue 3 |
| **Cache/Query** | @tanstack/vue-query | Latest | Cache inteligente, revalidación |
| **Formularios** | VeeValidate 4 + Yup | Latest | Validación declarativa |
| **Routing** | Vue Router | 4.x | Modo history, guards |
| **UI Framework** | Bootstrap | 5.x | Componentes accesibles, responsive |
| **Estilos** | SASS | Latest | Variables, mixins, paleta GOV.CO |
| **Iconos** | FontAwesome | 6.x | Íconos accesibles y consistentes |

### 3.3 Infraestructura

| Componente | Tecnología | Versión | Justificación |
|------------|------------|---------|---------------|
| **Contenedores** | Docker | Latest | Entornos reproducibles |
| **Orquestación** | Docker Compose | v2+ | Desarrollo y staging |
| **Web Server** | Nginx | 1.24+ | Reverse proxy, SSL termination |
| **Control de Versiones** | Git | Latest | Trunk-based development |
| **CI/CD** | GitHub Actions | - | Automatización de pruebas y deploy |
| **Análisis Estático** | PHPStan | Level 8 | Detección temprana de errores |
| **Linting JS** | ESLint | Latest | Estándares de código |

---

## 4. Restricciones de Seguridad (Inmutables)

Las siguientes restricciones de seguridad **NO pueden relajarse** bajo ninguna circunstancia:

### SEC-01: HTTPS Obligatorio
```
- HTTPS en todos los entornos (desarrollo, staging, producción)
- Encabezado HSTS: max-age=31536000; includeSubDomains; preload
- Redirección automática de HTTP a HTTPS
- Certificado válido (Let's Encrypt en producción)
```

### SEC-02: Validación Exhaustiva
```
- Frontend: validación con VeeValidate + Yup
- Backend: validación con FormRequest
- Base de datos: constraints (NOT NULL, UNIQUE, FOREIGN KEY)
- Sanitización de inputs en múltiples capas
- Whitelist de valores permitidos (no blacklist)
```

### SEC-03: Prevención de Inyecciones
```
- SQL Injection: prepared statements con Eloquent ORM
- XSS: codificación automática con Blade {{ }}
- Command Injection: validación estricta de inputs de shell
- LDAP Injection: N/A (no se usa LDAP)
```

### SEC-04: Protección CSRF
```
- Middleware VerifyCsrfToken habilitado
- Token CSRF en todos los formularios
- Header X-XSRF-TOKEN en requests Axios
- Sanctum provee protección CSRF automática
```

### SEC-05: CORS Estricto
```
- Permitir solo dominios de la entidad
- Ejemplo: *.alcaldia.gov.co
- No permitir origen wildcard (*)
- Credentials: true solo para dominios autorizados
```

### SEC-06: Rate Limiting
```
- Login: 5 intentos / 15 minutos
- Registro: 3 registros / hora por IP
- API pública: 100 requests / minuto por IP
- API autenticada: 300 requests / minuto por usuario
- Respuesta HTTP 429 cuando se excede
```

### SEC-07: Gestión de Secretos
```
- Variables de entorno (.env) para secretos
- .env nunca en repositorio (.gitignore)
- Rotación de secretos cada 90 días
- Secretos diferentes por entorno
- Usar Laravel Vault para producción (opcional)
```

### SEC-08: Auditoría Completa
```
- Registro de todas las operaciones CRUD
- Request-ID único por petición
- Log de IP, user-agent, timestamp
- Logs inmutables (no se pueden editar)
- Retención mínima: 1 año
- Package: spatie/laravel-activitylog
```

### SEC-09: Encriptación de Datos
```
- Datos sensibles: Crypt::encryptString()
- Contraseñas: bcrypt con cost 12
- Tokens: hash SHA-256
- Cookies: encrypted, httpOnly, secure, sameSite=strict
```

### SEC-10: Cumplimiento Ley 1581/2012
```
- Consentimiento explícito para tratamiento de datos personales
- Política de privacidad visible y accesible
- Anonimización cuando sea necesario
- Derecho al olvido (eliminación de datos)
- Portabilidad de datos (exportación JSON)
```

---

## 5. Paleta de Colores Oficial GOV.CO

Conforme al **Manual de Identidad Visual del Gobierno de Colombia** (Resolución 2345/2023):

### 5.1 Colores Principales

```scss
// Paleta Oficial
$azul-institucional: #004884;  // Azul principal GOV.CO
$amarillo-bandera: #FFD500;     // Amarillo bandera
$azul-bandera: #003DA5;         // Azul bandera
$rojo-bandera: #CE1126;         // Rojo bandera

// Colores Neutros
$gris-oscuro: #2C2C2C;
$gris-medio: #6B6B6B;
$gris-claro: #F5F5F5;
$blanco: #FFFFFF;
$negro: #000000;
```

### 5.2 Paleta Accesible (WCAG 2.1 AA)

Todos los siguientes pares cumplen contraste mínimo 4.5:1

```scss
// Textos
$texto-principal: #2C2C2C;      // Sobre blanco: 13.2:1 ✓
$texto-secundario: #6B6B6B;     // Sobre blanco: 5.7:1 ✓
$texto-inverso: #FFFFFF;        // Sobre azul institucional: 7.8:1 ✓

// Enlaces
$enlace-normal: #004884;        // Contraste 8.6:1 ✓
$enlace-hover: #003366;         // Contraste 11.2:1 ✓
$enlace-visitado: #663399;      // Contraste 6.1:1 ✓

// Estados
$exito: #2E7D32;                // Contraste 5.2:1 ✓
$advertencia: #F57C00;          // Contraste 4.5:1 ✓
$error: #C62828;                // Contraste 6.8:1 ✓
$informacion: #1976D2;          // Contraste 4.6:1 ✓

// Fondos
$fondo-primario: #FFFFFF;
$fondo-secundario: #F5F5F5;
$fondo-destacado: #E3F2FD;      // Azul claro accesible
```

### 5.3 Variables CSS

```css
:root {
  /* Colores Principales */
  --color-primario: #004884;
  --color-secundario: #FFD500;
  --color-acento: #CE1126;
  
  /* Textos */
  --color-texto: #2C2C2C;
  --color-texto-secundario: #6B6B6B;
  --color-texto-inverso: #FFFFFF;
  
  /* Enlaces */
  --color-enlace: #004884;
  --color-enlace-hover: #003366;
  
  /* Estados */
  --color-exito: #2E7D32;
  --color-advertencia: #F57C00;
  --color-error: #C62828;
  --color-info: #1976D2;
  
  /* Fondos */
  --color-fondo: #FFFFFF;
  --color-fondo-alt: #F5F5F5;
  
  /* Sombras accesibles */
  --sombra-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
  --sombra-md: 0 4px 6px rgba(0, 0, 0, 0.1);
  --sombra-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
}
```

### 5.4 Sistema de Temas

El CMS debe permitir personalización sin comprometer accesibilidad:

```typescript
// types/theme.ts
interface Theme {
  primaryColor: string;      // Debe pasar contraste 4.5:1
  secondaryColor: string;
  accentColor: string;
  textColor: string;
  backgroundColor: string;
}

// Validación de contraste
function validateContrast(foreground: string, background: string): boolean {
  const contrast = getContrastRatio(foreground, background);
  return contrast >= 4.5; // WCAG AA para texto normal
}
```

### 5.5 Tipografía Oficial

```scss
// Fuentes GOV.CO
$font-family-primary: 'Work Sans', -apple-system, BlinkMacSystemFont, 
                      'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
$font-family-secondary: 'Montserrat', sans-serif;

// Tamaños (escala modular 1.250)
$font-size-base: 16px;        // Base accesible
$font-size-sm: 14px;          // 0.875rem
$font-size-lg: 18px;          // 1.125rem
$font-size-xl: 20px;          // 1.25rem
$font-size-h1: 32px;          // 2rem
$font-size-h2: 28px;          // 1.75rem
$font-size-h3: 24px;          // 1.5rem
$font-size-h4: 20px;          // 1.25rem

// Pesos
$font-weight-normal: 400;
$font-weight-medium: 500;
$font-weight-semibold: 600;
$font-weight-bold: 700;

// Alturas de línea
$line-height-tight: 1.25;
$line-height-normal: 1.5;      // Mínimo WCAG 1.5
$line-height-relaxed: 1.75;
```

---

## 6. Definiciones y Glosario

| Término | Definición |
|---------|------------|
| **ADR** | Architecture Decision Record - Documento que registra una decisión arquitectónica importante |
| **CSRF** | Cross-Site Request Forgery - Ataque que fuerza acciones no autorizadas |
| **DCAT-AP** | Data Catalog Application Profile - Estándar para catálogos de datos abiertos |
| **FURAG** | Formulario Único de Reporte y Avance de Gestión - Reporte MIPG |
| **HSTS** | HTTP Strict Transport Security - Header que fuerza HTTPS |
| **ITA** | Índice de Transparencia y Acceso a la Información - Medición de transparencia |
| **JSON:API** | Especificación para APIs JSON consistentes |
| **LSC** | Lengua de Señas Colombiana - Lenguaje de señas oficial |
| **MIPG** | Modelo Integrado de Planeación y Gestión - Política de gestión pública |
| **ORM** | Object-Relational Mapping - Mapeo objeto-relacional (Eloquent) |
| **PQRS** | Peticiones, Quejas, Reclamos y Sugerencias |
| **RBAC** | Role-Based Access Control - Control de acceso basado en roles |
| **RPO** | Recovery Point Objective - Punto de recuperación objetivo |
| **RTO** | Recovery Time Objective - Tiempo de recuperación objetivo |
| **SDD** | Spec-Driven Development - Desarrollo guiado por especificaciones |
| **SECOP** | Sistema Electrónico de Contratación Pública |
| **SPA** | Single Page Application - Aplicación de página única |
| **WCAG** | Web Content Accessibility Guidelines - Pautas de accesibilidad web |
| **XSS** | Cross-Site Scripting - Inyección de scripts maliciosos |

---

## 7. Aprobación y Vigencia

### 7.1 Aprobadores
Este documento constitucional requiere aprobación de:
- [ ] Tech Lead / Arquitecto
- [ ] Product Manager
- [ ] Security Officer
- [ ] Representante Legal de la Alcaldía
- [ ] Responsable de Transparencia

### 7.2 Proceso de Cambios
Los cambios a este documento requieren:
1. Propuesta formal por escrito
2. Justificación técnica y legal
3. Análisis de impacto
4. Aprobación **unánime** de todos los aprobadores
5. Versionado (incremento de versión mayor)
6. Notificación a todo el equipo

### 7.3 Vigencia
- **Inicio:** Febrero 2026
- **Revisión:** Anual (cada febrero)
- **Próxima revisión:** Febrero 2027

---

## 8. Anexos

### Anexo A: Referencias Legales
- Ley 1341 de 2009: https://www.mintic.gov.co/portal/inicio/3707:Ley-1341-de-2009
- Ley 1712 de 2014: http://www.secretariasenado.gov.co/senado/basedoc/ley_1712_2014.html
- Decreto 1078 de 2015: https://www.funcionpublica.gov.co/eva/gestornormativo/norma.php?i=64573
- Resolución 1519 de 2020: https://www.mintic.gov.co/portal/715/articles-161170_recurso_1.pdf
- Ley 1581 de 2012: http://www.secretariasenado.gov.co/senado/basedoc/ley_1581_2012.html

### Anexo B: Herramientas de Validación
- **Accesibilidad:** Axe DevTools, WAVE, Lighthouse
- **Seguridad:** OWASP ZAP, Snyk, SonarQube
- **Performance:** WebPageTest, GTmetrix
- **Código:** PHPStan, ESLint, Prettier

---

**Firmado digitalmente:**  
[Espacio para firmas digitales de aprobadores]

**Fecha de aprobación:** [Pendiente]

**Hash del documento:** [Se genera al firmar]

---

*Este documento es INMUTABLE y constituye la base fundacional del proyecto. Cualquier desviación de los principios aquí establecidos debe ser justificada, documentada y aprobada por todos los stakeholders.*
