# Portal de Configuración VPS - Documentación del Proyecto

Portal web de documentación y gestión centralizada para administradores de sistemas que trabajan con servidores VPS.

## 📋 Descripción

Este repositorio contiene un portal web estático que centraliza la documentación técnica, guías y recursos para la administración y configuración de servidores VPS. El portal incluye información sobre SSH, seguridad, servicios web y otras herramientas esenciales para administradores de sistemas.

## 🚀 Características

- ✅ Interfaz web moderna y responsive
- ✅ Navegación intuitiva con menú lateral
- ✅ Diseño adaptable para móviles y tablets
- ✅ Documentación organizada por categorías
- ✅ Accesos rápidos a secciones frecuentes
- 🔄 Sistema de búsqueda (en desarrollo)
- 🔄 Gestión de favoritos (planificado)

## 📁 Estructura del Proyecto

```
documentacion/
├── index.html              # Página principal del portal
├── docuemntacion.html      # Página de documentación técnica
├── README.md               # Este archivo
├── user-stories.md         # Historias de usuario del proyecto
├── tasks.md                # Tareas y roadmap del proyecto
├── business-rules.md       # Reglas de negocio
└── project-specs.md        # Especificaciones técnicas completas
```

## 📚 Documentación del Proyecto

### [Historias de Usuario](user-stories.md)
Contiene 10 historias de usuario que definen las funcionalidades desde la perspectiva del administrador de sistemas:
- HU-001 a HU-004: Funcionalidades críticas (navegación, SSH, seguridad, servicios web)
- HU-005 a HU-007: Funcionalidades importantes (búsqueda, accesos directos, móvil)
- HU-008 a HU-010: Funcionalidades deseables (favoritos, recursos, soporte)

### [Tareas del Proyecto](tasks.md)
Lista de 20 tareas organizadas en 6 fases:
- ✅ Fase 1: Estructura y Diseño Base (completada)
- ✅ Fase 2: Página de Inicio (completada)
- 🔄 Fase 3: Página de Documentación (en progreso)
- ⏳ Fase 4: Funcionalidades Avanzadas (pendiente)
- ⏳ Fase 5: Contenido Adicional (pendiente)
- ⏳ Fase 6: Mejoras y Optimización (pendiente)

### [Reglas de Negocio](business-rules.md)
20 reglas de negocio organizadas en 7 categorías:
- Acceso y navegación (RN-001 a RN-003)
- Contenido y documentación (RN-004 a RN-006)
- Diseño y UI/UX (RN-007 a RN-009)
- Seguridad y buenas prácticas (RN-010 a RN-012)
- Aspectos técnicos (RN-013 a RN-015)
- Mantenimiento (RN-016 a RN-018)
- Funcionalidades opcionales (RN-019 a RN-020)

### [Especificaciones del Proyecto](project-specs.md)
Documento completo con:
- Información general y objetivos
- Alcance y arquitectura del sistema
- Diseño y UX (paleta de colores, tipografía, componentes)
- Requisitos funcionales y no funcionales
- Plan de implementación y métricas de éxito
- Riesgos y mitigación

## 🛠️ Tecnologías Utilizadas

- **HTML5**: Estructura del contenido
- **CSS3**: Estilos personalizados con variables CSS
- **JavaScript**: Interactividad (vanilla JS)
- **Bootstrap 5.3.0**: Framework CSS responsive
- **Bootstrap Icons**: Librería de iconos
- **Font Awesome**: Iconos adicionales

## 🎨 Paleta de Colores

```css
--primary-dark: #1a365d      /* Azul oscuro principal */
--primary-blue: #2b6cb0      /* Azul principal */
--primary-light-blue: #4299e1 /* Azul claro */
--accent-orange: #ed8936     /* Color de acento */
--sidebar-bg: #1a202c        /* Fondo del sidebar */
```

## 📱 Responsive Design

El portal se adapta a diferentes tamaños de pantalla:
- **Desktop**: > 992px (sidebar visible)
- **Tablet**: 768px - 992px (sidebar oculto por defecto)
- **Mobile**: < 768px (layout de una columna)

## 🔧 Instalación y Uso

### Opción 1: Servidor Web Local
```bash
# Clonar el repositorio
git clone https://github.com/SantanderAcuna/documentacion.git

# Navegar al directorio
cd documentacion

# Servir con Python (opción 1)
python -m http.server 8000

# Servir con Node.js (opción 2)
npx http-server -p 8000

# Abrir en navegador
# http://localhost:8000
```

### Opción 2: Abrir Directamente
Simplemente abre `index.html` en tu navegador preferido.

## 📊 Estado del Proyecto

### Completado ✅
- Estructura HTML base
- Sistema de navegación
- Diseño responsive
- Página de inicio con tarjetas
- Estilos personalizados

### En Progreso 🔄
- Documentación SSH
- Documentación de seguridad
- Documentación de servicios web

### Pendiente ⏳
- Sistema de búsqueda
- Sistema de favoritos
- Comandos esenciales
- Gestión de procesos y usuarios
- Modo oscuro
- Optimización de rendimiento

## 🤝 Contribuciones

Este es un proyecto de documentación. Para contribuir:
1. Revisa las [tareas pendientes](tasks.md)
2. Consulta las [reglas de negocio](business-rules.md)
3. Sigue las [especificaciones del proyecto](project-specs.md)

## 📄 Licencia

Todos los derechos reservados © 2023

## 📞 Contacto y Soporte

Para preguntas o soporte, consulta la sección de ayuda en el portal.

---

**Versión**: 1.0.0  
**Última actualización**: 2026-02-17
