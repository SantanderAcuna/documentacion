# Reglas de Negocio - Portal de Configuración VPS

## Introducción
Este documento define las reglas de negocio que rigen el funcionamiento del Portal de Configuración VPS. Estas reglas aseguran que el portal cumpla con los requisitos técnicos, de seguridad y de usabilidad necesarios.

---

## 1. Reglas de Acceso y Navegación

### RN-001: Estructura de Navegación
**Regla:** El portal debe mantener una estructura de navegación consistente en todas las páginas.

**Descripción:**
- Todas las páginas deben incluir el mismo menú lateral (sidebar)
- El sidebar debe estar visible en todo momento (excepto en dispositivos móviles)
- La navegación debe ser jerárquica y organizada por categorías

**Impacto:** Crítico  
**Justificación:** Garantiza una experiencia de usuario coherente y facilita la navegación

---

### RN-002: Responsive Design Obligatorio
**Regla:** El portal debe ser totalmente funcional en dispositivos móviles, tablets y desktop.

**Descripción:**
- El diseño debe adaptarse automáticamente a diferentes tamaños de pantalla
- En dispositivos móviles (< 992px), el sidebar debe ocultarse por defecto
- Debe existir un botón toggle para mostrar/ocultar el sidebar en móviles
- El contenido debe ser legible sin zoom horizontal

**Impacto:** Alto  
**Justificación:** Los administradores necesitan acceso desde cualquier dispositivo

---

### RN-003: Accesibilidad del Contenido
**Regla:** Todo el contenido debe ser accesible mediante navegación estándar.

**Descripción:**
- No se requiere autenticación para acceder al portal
- Los enlaces deben funcionar correctamente
- No debe haber contenido oculto sin razón funcional
- El portal debe ser indexable por buscadores

**Impacto:** Medio  
**Justificación:** Facilita el acceso rápido a la información

---

## 2. Reglas de Contenido y Documentación

### RN-004: Calidad de Documentación
**Regla:** Toda la documentación debe ser precisa, actualizada y verificable.

**Descripción:**
- Los comandos documentados deben ser funcionales
- Se debe indicar la versión de software cuando sea relevante
- Las instrucciones deben ser paso a paso
- Se deben incluir ejemplos prácticos

**Impacto:** Crítico  
**Justificación:** Información incorrecta puede causar fallos en producción

---

### RN-005: Organización del Contenido
**Regla:** El contenido debe organizarse en categorías lógicas y coherentes.

**Descripción:**
- Las secciones principales son: SSH, Seguridad, Servicios Web
- Cada sección debe tener subsecciones cuando sea necesario
- Los temas relacionados deben estar agrupados
- Debe existir una tabla de contenidos en páginas extensas

**Impacto:** Alto  
**Justificación:** Facilita la búsqueda y comprensión de la información

---

### RN-006: Idioma del Portal
**Regla:** El portal debe estar completamente en español.

**Descripción:**
- Toda la interfaz debe estar en español
- La documentación debe estar en español
- Los ejemplos de código pueden incluir comandos en inglés (estándar técnico)
- Los comentarios en código deben estar en español

**Impacto:** Medio  
**Justificación:** Facilita la comprensión para usuarios hispanohablantes

---

## 3. Reglas de Diseño y UI/UX

### RN-007: Consistencia Visual
**Regla:** El portal debe mantener un diseño visual consistente.

**Descripción:**
- Se debe usar la paleta de colores definida en variables CSS
- Colores principales: #1a365d (dark), #2b6cb0 (blue), #ed8936 (orange)
- Tipografía: "Segoe UI" o fuentes del sistema
- Los iconos deben ser de Bootstrap Icons

**Impacto:** Medio  
**Justificación:** Mejora la profesionalidad y usabilidad

---

### RN-008: Interactividad y Feedback
**Regla:** Los elementos interactivos deben proporcionar feedback visual.

**Descripción:**
- Los enlaces y botones deben cambiar de apariencia al hacer hover
- Las transiciones deben ser suaves (0.2-0.3s)
- El enlace activo debe estar claramente identificado
- Los elementos clickeables deben tener cursor pointer

**Impacto:** Medio  
**Justificación:** Mejora la experiencia de usuario y claridad de la interfaz

---

### RN-009: Jerarquía Visual
**Regla:** El contenido debe tener una jerarquía visual clara.

**Descripción:**
- Los títulos principales deben ser más grandes que los subtítulos
- Se deben usar diferentes tamaños de fuente para establecer jerarquía
- Los elementos importantes deben destacarse visualmente
- El espacio en blanco debe usarse para separar secciones

**Impacto:** Medio  
**Justificación:** Facilita la lectura y comprensión del contenido

---

## 4. Reglas de Seguridad y Buenas Prácticas

### RN-010: Seguridad en Ejemplos
**Regla:** Los ejemplos de código no deben incluir credenciales reales o información sensible.

**Descripción:**
- Usar placeholders para contraseñas (ej: "your_password")
- Usar IPs de ejemplo (ej: 192.0.2.1)
- No incluir claves privadas reales
- Advertir sobre riesgos de seguridad cuando sea relevante

**Impacto:** Crítico  
**Justificación:** Previene fugas de información sensible

---

### RN-011: Recomendaciones de Seguridad
**Regla:** El portal debe promover prácticas de seguridad sólidas.

**Descripción:**
- Recomendar autenticación con claves SSH sobre contraseñas
- Sugerir uso de firewall siempre
- Promover actualizaciones regulares
- Advertir sobre comandos peligrosos

**Impacto:** Alto  
**Justificación:** Protege los sistemas de los usuarios

---

### RN-012: Versionado de Información
**Regla:** Se debe indicar claramente la versión del portal y fecha de actualización.

**Descripción:**
- El footer debe mostrar la versión actual (ej: v1.0.0)
- Se debe actualizar el año en el copyright automáticamente
- Las actualizaciones importantes deben documentarse
- Debe existir un changelog o historial de cambios

**Impacto:** Bajo  
**Justificación:** Permite rastrear cambios y mantener documentación actualizada

---

## 5. Reglas Técnicas

### RN-013: Compatibilidad de Navegadores
**Regla:** El portal debe funcionar en navegadores modernos.

**Descripción:**
- Soporte para Chrome, Firefox, Safari, Edge (últimas 2 versiones)
- Uso de Bootstrap 5 para garantizar compatibilidad
- No se requiere soporte para Internet Explorer
- Debe funcionar con JavaScript habilitado

**Impacto:** Alto  
**Justificación:** Cubre la mayoría de usuarios actuales

---

### RN-014: Rendimiento y Optimización
**Regla:** El portal debe cargar rápidamente y ser eficiente.

**Descripción:**
- El tiempo de carga inicial debe ser < 3 segundos
- Las imágenes deben estar optimizadas
- CSS/JS debe estar minificado en producción
- Usar CDN para librerías externas (Bootstrap, Icons)

**Impacto:** Medio  
**Justificación:** Mejora la experiencia de usuario

---

### RN-015: Dependencias Externas
**Regla:** Las dependencias externas deben ser mínimas y confiables.

**Descripción:**
- Usar CDN oficiales (Bootstrap, Font Awesome)
- Preferir CSS/JS vanilla sobre frameworks pesados
- Documentar todas las dependencias
- Tener plan de contingencia si un CDN falla

**Impacto:** Medio  
**Justificación:** Reduce riesgos y mejora mantenibilidad

---

## 6. Reglas de Mantenimiento

### RN-016: Actualización de Contenido
**Regla:** El contenido debe revisarse y actualizarse periódicamente.

**Descripción:**
- Revisar documentación cada 6 meses
- Actualizar versiones de software mencionadas
- Verificar que comandos sigan funcionando
- Incorporar nuevas mejores prácticas

**Impacto:** Alto  
**Justificación:** Mantiene la relevancia y utilidad del portal

---

### RN-017: Control de Cambios
**Regla:** Los cambios al portal deben ser rastreables y documentados.

**Descripción:**
- Usar control de versiones (Git)
- Documentar cambios significativos
- Mantener historial de commits claro
- Usar branches para desarrollo

**Impacto:** Medio  
**Justificación:** Facilita mantenimiento y reversión de cambios

---

### RN-018: Testing y Validación
**Regla:** Los cambios deben ser probados antes de publicarse.

**Descripción:**
- Validar HTML/CSS
- Probar en diferentes navegadores
- Verificar responsive design
- Revisar enlaces rotos

**Impacto:** Alto  
**Justificación:** Previene errores en producción

---

## 7. Reglas de Funcionalidades Opcionales

### RN-019: Sistema de Búsqueda
**Regla:** Si se implementa búsqueda, debe ser funcional y precisa.

**Descripción:**
- Debe indexar todo el contenido visible
- Los resultados deben ser relevantes
- Debe soportar búsqueda en español
- Debe resaltar términos encontrados

**Impacto:** Medio  
**Justificación:** Mejora significativamente la usabilidad

---

### RN-020: Sistema de Favoritos
**Regla:** Si se implementan favoritos, deben persistir entre sesiones.

**Descripción:**
- Usar localStorage para almacenamiento
- Sincronizar entre páginas del portal
- Permitir agregar y eliminar favoritos fácilmente
- Mostrar contador de favoritos

**Impacto:** Bajo  
**Justificación:** Mejora la experiencia para usuarios frecuentes

---

## Matriz de Impacto

| Impacto | Número de Reglas | Reglas |
|---------|------------------|--------|
| Crítico | 3 | RN-001, RN-004, RN-010 |
| Alto | 6 | RN-002, RN-005, RN-011, RN-013, RN-016, RN-018 |
| Medio | 9 | RN-003, RN-006, RN-007, RN-008, RN-009, RN-014, RN-015, RN-017, RN-019 |
| Bajo | 2 | RN-012, RN-020 |

---

## Consideraciones Especiales

### Prioridad de Implementación
Las reglas marcadas como "Crítico" deben implementarse primero, seguidas por las de "Alto" impacto. Las reglas de "Medio" y "Bajo" impacto pueden implementarse según disponibilidad de recursos.

### Excepciones
Cualquier excepción a estas reglas debe ser documentada y justificada. Las excepciones a reglas críticas requieren aprobación del responsable del proyecto.

### Actualización de Reglas
Este documento debe revisarse y actualizarse conforme evoluciona el proyecto. Las reglas obsoletas deben marcarse como tal pero mantenerse para referencia histórica.
