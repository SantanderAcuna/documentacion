# Portal de Gestión de Servidores VPS - Frontend

Sistema CRUD completo y profesional para la gestión de servidores VPS, desarrollado con Vue 3, TypeScript y las mejores prácticas de desarrollo.

## 📋 Tabla de Contenidos

- [Características](#características)
- [Stack Tecnológico](#stack-tecnológico)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Instalación](#instalación)
- [Desarrollo](#desarrollo)
- [Arquitectura](#arquitectura)
- [Componentes Principales](#componentes-principales)
- [Validaciones](#validaciones)
- [API Integration](#api-integration)
- [Despliegue](#despliegue)

## ✨ Características

### Funcionalidades Implementadas

- ✅ **CRUD Completo de Servidores VPS**
  - Listado con paginación, búsqueda y filtros
  - Creación de nuevos servidores
  - Edición de servidores existentes (carga de datos para editar)
  - Eliminación con confirmación
  - Prueba de conexión SSH

- ✅ **Validación de Formularios**
  - Validación en tiempo real con Yup
  - Mensajes de error específicos y profesionales
  - Validación de IPs, hostnames, puertos y usuarios Unix

- ✅ **Gestión de Estado**
  - TanStack Query (Vue Query) para caché inteligente
  - Invalidación automática de caché
  - Estados de carga y error bien manejados

- ✅ **UX Profesional**
  - Notificaciones toast para feedback inmediato
  - Indicadores de carga durante operaciones
  - Confirmaciones para acciones destructivas
  - Diseño responsive con Bootstrap 5

- ✅ **TypeScript Strict Mode**
  - Tipado completo de toda la aplicación
  - Interfaces para todas las entidades del backend
  - Type safety en formularios y API calls

## 🛠 Stack Tecnológico

```
Frontend Framework:     Vue.js 3.4+ (Composition API)
Lenguaje:              TypeScript 5.3+ (strict mode)
Build Tool:            Vite 5.0+
Router:                Vue Router 4.2+
State Management:      Pinia 2.1+ (para estado global)
Data Fetching:         TanStack Query (Vue Query) 5.17+
HTTP Client:           Axios 1.6+
Validación:            Yup 1.3+
UI Framework:          Bootstrap 5.3+
Iconos:                FontAwesome 6 (FREE)
Notificaciones:        Vue Toastification 2.0+
```

## 📁 Estructura del Proyecto

```
frontend/
├── public/                  # Archivos estáticos
├── src/
│   ├── assets/             # Estilos y recursos
│   │   └── main.css        # Estilos globales
│   ├── components/         # Componentes reutilizables
│   ├── composables/        # Composables de Vue
│   ├── router/             # Configuración de rutas
│   │   └── index.ts        # Router principal
│   ├── services/           # Servicios de API
│   │   ├── api.ts          # Cliente Axios configurado
│   │   └── vpsServerService.ts  # Servicio CRUD de servidores
│   ├── stores/             # Stores de Pinia
│   ├── types/              # Tipos e interfaces TypeScript
│   │   ├── entities.ts     # Interfaces de entidades del backend
│   │   └── validations.ts  # Esquemas de validación Yup
│   ├── views/              # Vistas/Páginas
│   │   ├── ServerList.vue  # Lista de servidores
│   │   ├── ServerForm.vue  # Formulario crear/editar
│   │   ├── SSHKeys.vue     # Vista de llaves SSH
│   │   └── NotFound.vue    # Página 404
│   ├── App.vue             # Componente raíz
│   └── main.ts             # Punto de entrada
├── .env                    # Variables de entorno
├── .env.example            # Ejemplo de variables de entorno
├── index.html              # HTML base
├── package.json            # Dependencias
├── tsconfig.json           # Configuración TypeScript
├── tsconfig.node.json      # TypeScript para Vite
└── vite.config.ts          # Configuración Vite
```

## 🚀 Instalación

### Requisitos Previos

- Node.js 18.x LTS o superior
- npm 9.x o superior
- Backend Laravel en ejecución (puerto 3000)

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/SantanderAcuna/documentacion.git
cd documentacion/frontend
```

2. **Instalar dependencias**
```bash
npm install
```

3. **Configurar variables de entorno**
```bash
cp .env.example .env
# Editar .env con la URL de tu API
```

4. **Iniciar servidor de desarrollo**
```bash
npm run dev
```

La aplicación estará disponible en `http://localhost:5173`

## 💻 Desarrollo

### Scripts Disponibles

```bash
# Desarrollo
npm run dev              # Inicia servidor de desarrollo con hot reload

# Build
npm run build            # Compila para producción
npm run preview          # Preview del build de producción

# Type Checking
npm run type-check       # Verifica tipos TypeScript sin compilar
```

### Flujo de Desarrollo

1. **Crear una nueva rama**
```bash
git checkout -b feature/nueva-funcionalidad
```

2. **Desarrollar la funcionalidad**
   - Seguir la estructura de carpetas existente
   - Usar TypeScript strict mode
   - Validar formularios con Yup
   - Documentar código con JSDoc

3. **Probar localmente**
```bash
npm run type-check  # Verificar tipos
npm run dev         # Probar en navegador
```

4. **Commit y Push**
```bash
git add .
git commit -m "feat: descripción de la funcionalidad"
git push origin feature/nueva-funcionalidad
```

## 🏗 Arquitectura

### Patrón de Arquitectura

El proyecto sigue una arquitectura de **capas separadas**:

```
┌─────────────────────────────────────┐
│         VIEWS (Páginas)             │  ← Componentes de nivel superior
├─────────────────────────────────────┤
│      COMPONENTS (Reutilizables)     │  ← Componentes compartidos
├─────────────────────────────────────┤
│     COMPOSABLES (Lógica)            │  ← Lógica reutilizable
├─────────────────────────────────────┤
│       SERVICES (API)                │  ← Comunicación con backend
├─────────────────────────────────────┤
│     TYPES (Interfaces)              │  ← Contratos de datos
└─────────────────────────────────────┘
```

### Gestión de Estado

1. **Estado Local**: `ref()`, `reactive()` para estado de componente
2. **Estado de Servidor**: TanStack Query para datos de API
3. **Estado Global**: Pinia stores para estado compartido

### Data Fetching con TanStack Query

```typescript
// Query para leer datos
const { data, isLoading, error } = useQuery({
  queryKey: ['servers'],
  queryFn: () => vpsServerService.getAll()
})

// Mutation para modificar datos
const mutation = useMutation({
  mutationFn: (data) => vpsServerService.create(data),
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['servers'] })
  }
})
```

## 🧩 Componentes Principales

### ServerList.vue

**Responsabilidad**: Mostrar lista de servidores con acciones CRUD

**Características**:
- Tabla responsive con Bootstrap
- Paginación del lado del servidor
- Búsqueda y filtros en tiempo real
- Botones de acción: Editar, Eliminar, Probar Conexión
- Modal de confirmación para eliminar
- Estados de carga y error

**Tecnologías**:
- Vue Query para fetch de datos
- Vue Router para navegación
- Vue Toastification para notificaciones

### ServerForm.vue

**Responsabilidad**: Formulario para crear y editar servidores

**Características**:
- Modo dual: Crear nuevo / Editar existente
- Carga automática de datos en modo edición
- Validación en tiempo real con Yup
- Mensajes de error específicos por campo
- Soporte para tags/etiquetas
- Diferentes métodos de autenticación (password/SSH key)

**Validaciones Implementadas**:
- Nombre: 3-100 caracteres, alfanumérico
- IP: IPv4 válido o hostname
- Puerto: 1-65535
- Usuario: Formato Unix válido
- Método de autenticación: password o ssh_key

## ✅ Validaciones

### Esquema de Validación (Yup)

```typescript
// Ejemplo de validación de IP o hostname
ipAddress: yup
  .string()
  .required('La dirección IP o hostname es obligatoria')
  .test('ip-or-hostname', 'Debe ser una IP válida o hostname válido',
    (value) => ipv4Regex.test(value) || hostnameRegex.test(value)
  )
```

### Validaciones Implementadas

1. **Nombre de Servidor**
   - Obligatorio
   - Mínimo 3 caracteres
   - Solo letras, números, espacios, guiones

2. **Dirección IP/Hostname**
   - Obligatorio
   - IPv4 válido O hostname válido
   - Regex de validación estricta

3. **Puerto SSH**
   - Número entre 1 y 65535
   - Por defecto: 22

4. **Usuario SSH**
   - Obligatorio
   - Formato Unix (lowercase, guiones bajos)
   - Máximo 32 caracteres

5. **Método de Autenticación**
   - Opciones: password, ssh_key
   - Si es ssh_key, SSH Key ID es obligatorio

## 🔌 API Integration

### Cliente Axios Configurado

```typescript
// src/services/api.ts
const apiClient = axios.create({
  baseURL: '/api/v1',
  timeout: 30000,
  withCredentials: true  // Para Laravel Sanctum
})

// Interceptor de request
apiClient.interceptors.request.use(config => {
  // Agregar CSRF token si existe
  const csrfToken = document.querySelector('meta[name="csrf-token"]')
  if (csrfToken) {
    config.headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content')
  }
  return config
})

// Interceptor de response
apiClient.interceptors.response.use(
  response => response,
  error => {
    // Manejo de errores 401, 403, 404, 422, 500
    return Promise.reject(error)
  }
)
```

### Endpoints de API Esperados

```
GET    /api/v1/servers           # Listar servidores (con paginación)
GET    /api/v1/servers/:id       # Obtener un servidor
POST   /api/v1/servers           # Crear servidor
PUT    /api/v1/servers/:id       # Actualizar servidor
DELETE /api/v1/servers/:id       # Eliminar servidor
POST   /api/v1/servers/:id/test-connection  # Probar conexión
GET    /api/v1/servers/:id/status           # Obtener estado
```

### Formato de Respuestas

```typescript
// Respuesta exitosa
{
  "success": true,
  "data": { /* ... */ }
}

// Respuesta paginada
{
  "success": true,
  "data": [/* ... */],
  "pagination": {
    "total": 100,
    "perPage": 10,
    "currentPage": 1,
    "lastPage": 10
  }
}

// Respuesta de error
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error detail"]
  }
}
```

## 🚢 Despliegue

### Build para Producción

```bash
npm run build
```

Esto genera la carpeta `dist/` con los archivos optimizados.

### Configuración de Nginx

```nginx
server {
    listen 80;
    server_name portal-vps.ejemplo.com;
    root /var/www/portal-vps/dist;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location /api {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

### Variables de Entorno para Producción

```bash
VITE_API_URL=https://api.portal-vps.ejemplo.com/api/v1
```

## 📝 Documentación Técnica

### Errores Comunes y Soluciones

#### Error: "Cannot find module '@/...'"

**Causa**: Path alias no configurado correctamente

**Solución**: Verificar `vite.config.ts` y `tsconfig.json`

```typescript
// vite.config.ts
resolve: {
  alias: {
    '@': fileURLToPath(new URL('./src', import.meta.url))
  }
}

// tsconfig.json
"paths": {
  "@/*": ["./src/*"]
}
```

#### Error: "Property does not exist on type"

**Causa**: Tipo TypeScript incorrecto o faltante

**Solución**: Definir interfaces en `src/types/entities.ts`

### Mejores Prácticas

1. **Componentes**
   - Un componente = Una responsabilidad
   - Props tipadas con TypeScript
   - Emits documentados

2. **Servicios**
   - Singleton pattern para servicios
   - Manejo de errores centralizado
   - Tipado de requests y responses

3. **Validaciones**
   - Esquemas Yup reutilizables
   - Mensajes de error en español
   - Validación client-side + server-side

4. **Estado**
   - TanStack Query para datos de API
   - Pinia para estado global
   - ref/reactive para estado local

## 🐛 Debugging

### Vue DevTools

Instalar extensión de navegador: [Vue DevTools](https://devtools.vuejs.org/)

### TanStack Query DevTools

Agregar al proyecto (solo desarrollo):

```typescript
import { VueQueryDevtools } from '@tanstack/vue-query-devtools'

// En main.ts (desarrollo)
if (import.meta.env.DEV) {
  app.component('VueQueryDevtools', VueQueryDevtools)
}
```

## 📄 Licencia

Proyecto propietario - Todos los derechos reservados

## 👥 Autores

- **Equipo de Desarrollo** - Portal VPS

---

**Nota**: Este es un proyecto profesional diseñado para producción. Todas las funcionalidades están completamente implementadas, documentadas y listas para usar con el backend de Laravel.
