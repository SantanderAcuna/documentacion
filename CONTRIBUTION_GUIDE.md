# Guía de Contribución - Portal de Configuración VPS

Gracias por tu interés en contribuir al Portal de Configuración VPS. Esta guía te ayudará a empezar.

## Tabla de Contenidos

1. [Código de Conducta](#código-de-conducta)
2. [Cómo Contribuir](#cómo-contribuir)
3. [Setup del Entorno](#setup-del-entorno)
4. [Workflow de Desarrollo](#workflow-de-desarrollo)
5. [Estándares de Código](#estándares-de-código)
6. [Testing](#testing)
7. [Documentación](#documentación)
8. [Pull Requests](#pull-requests)

---

## Código de Conducta

### Nuestro Compromiso

Nos comprometemos a hacer de la participación en este proyecto una experiencia libre de acoso para todos, independientemente de edad, tamaño corporal, discapacidad, etnia, identidad de género, nivel de experiencia, nacionalidad, apariencia personal, raza, religión o identidad sexual.

### Comportamiento Esperado

- Usar lenguaje acogedor e inclusivo
- Respetar diferentes puntos de vista y experiencias
- Aceptar críticas constructivas con gracia
- Enfocarse en lo que es mejor para la comunidad
- Mostrar empatía hacia otros miembros

### Comportamiento Inaceptable

- Uso de lenguaje o imágenes sexualizadas
- Trolling, insultos o comentarios despectivos
- Acoso público o privado
- Publicar información privada de otros
- Conducta poco profesional

### Reporte

Incidentes pueden reportarse a: conduct@yourdomain.com

---

## Cómo Contribuir

### Tipos de Contribuciones

#### 🐛 Reportar Bugs

Antes de crear un issue:
- Verifica que no exista ya
- Usa la plantilla de bug report
- Incluye pasos para reproducir
- Adjunta screenshots si aplica
- Especifica versión/entorno

**Template:**
```markdown
**Descripción del Bug**
Descripción clara y concisa del bug.

**Pasos para Reproducir**
1. Ir a '...'
2. Click en '....'
3. Scroll down to '....'
4. Ver error

**Comportamiento Esperado**
Lo que esperabas que sucediera.

**Screenshots**
Si aplica, agrega screenshots.

**Entorno:**
 - OS: [e.g. Ubuntu 24.04]
 - Browser [e.g. chrome, safari]
 - Version [e.g. 22]
```

#### ✨ Sugerir Funcionalidades

Para sugerir nuevas features:
- Usa la plantilla de feature request
- Explica el problema que soluciona
- Describe la solución propuesta
- Considera alternativas

**Template:**
```markdown
**¿El feature está relacionado a un problema?**
Descripción clara del problema.

**Solución Propuesta**
Descripción de lo que quieres que suceda.

**Alternativas Consideradas**
Otras soluciones que consideraste.

**Contexto Adicional**
Cualquier otro contexto o screenshots.
```

#### 📝 Mejorar Documentación

La documentación siempre puede mejorar:
- Corregir typos
- Clarificar secciones confusas
- Agregar ejemplos
- Traducir documentación

#### 💻 Contribuir Código

Ver secciones siguientes para detalles.

---

## Setup del Entorno

### Prerrequisitos

- **Git** 2.x+
- **PHP** 8.3+
- **Composer** 2.x+
- **Node.js** 20.x+
- **MySQL** 8.0+
- **Redis** 7.x+
- **Docker** (opcional pero recomendado)

### 1. Fork y Clone

```bash
# Fork el repositorio en GitHub
# Luego clona tu fork

git clone https://github.com/TU_USUARIO/documentacion.git
cd documentacion

# Agregar upstream
git remote add upstream https://github.com/SantanderAcuna/documentacion.git
```

### 2. Setup con Docker (Recomendado)

```bash
# Copiar environment files
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

# Iniciar containers
docker-compose up -d

# Instalar dependencias backend
docker-compose exec php composer install

# Generar key
docker-compose exec php php artisan key:generate

# Migraciones y seeders
docker-compose exec php php artisan migrate --seed

# Instalar dependencias frontend
docker-compose exec node npm install

# Iniciar dev server
docker-compose exec node npm run dev
```

### 3. Setup Manual (Sin Docker)

**Backend:**
```bash
cd backend

# Instalar dependencias
composer install

# Configurar .env
cp .env.example .env
# Editar .env con tus credenciales de DB

# Generar key
php artisan key:generate

# Crear database
mysql -u root -p -e "CREATE DATABASE vps_portal_dev"

# Migraciones y seeders
php artisan migrate --seed

# Iniciar servidor
php artisan serve
```

**Frontend:**
```bash
cd frontend

# Instalar dependencias
npm install

# Configurar .env
cp .env.example .env

# Iniciar dev server
npm run dev
```

### 4. Verificar Setup

```bash
# Backend
curl http://localhost:8000/api/health

# Frontend
open http://localhost:5173

# Login con credenciales de seed
# Email: admin@example.com
# Password: password
```

---

## Workflow de Desarrollo

### 1. Crear Branch

```bash
# Actualizar main
git checkout main
git pull upstream main

# Crear feature branch
git checkout -b feature/nombre-descriptivo

# O para bugfix
git checkout -b fix/descripcion-del-bug
```

**Naming Conventions:**
- `feature/` - Nuevas funcionalidades
- `fix/` - Bug fixes
- `docs/` - Documentación
- `refactor/` - Refactorización
- `test/` - Tests
- `chore/` - Tareas de mantenimiento

### 2. Desarrollar

```bash
# Hacer cambios
# Escribir tests
# Ejecutar tests localmente
# Commit frecuentemente

git add .
git commit -m "feat: descripción del cambio"
```

**Conventional Commits:**
```
feat: nueva funcionalidad
fix: bug fix
docs: cambios en documentación
style: formato, puntos y comas, etc
refactor: refactorización de código
test: agregar tests
chore: tareas de mantenimiento
```

### 3. Mantener Actualizado

```bash
# Actualizar desde upstream frecuentemente
git fetch upstream
git rebase upstream/main
```

### 4. Push y PR

```bash
# Push a tu fork
git push origin feature/nombre-descriptivo

# Crear Pull Request en GitHub
# Llenar template de PR
# Esperar review
```

---

## Estándares de Código

### Backend (PHP/Laravel)

#### PSR-12 Coding Standard

```php
<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Http\Requests\StoreDocumentRequest;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    /**
     * Store a newly created document.
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $document = Document::create($request->validated());
        
        return response()->json([
            'success' => true,
            'data' => $document,
        ], 201);
    }
}
```

#### Reglas

- Indentación: 4 espacios
- Line length: máximo 120 caracteres
- Usar type hints
- Documentar con PHPDoc
- Nombrado:
  - Classes: `PascalCase`
  - Methods: `camelCase`
  - Variables: `camelCase`
  - Constants: `UPPER_SNAKE_CASE`

#### Laravel Best Practices

```php
// ✅ Usar Eloquent
$users = User::where('active', true)->get();

// ❌ No usar Query Builder cuando Eloquent es suficiente
$users = DB::table('users')->where('active', true)->get();

// ✅ Usar Form Requests para validación
public function store(StoreDocumentRequest $request) { }

// ❌ No validar en controllers
public function store(Request $request) {
    $request->validate([...]);
}

// ✅ Usar Resources para transformación
return DocumentResource::collection($documents);

// ❌ No retornar modelos directamente
return $documents;
```

#### Linting

```bash
# PHP CS Fixer
composer require --dev friendsofphp/php-cs-fixer

# Configurar .php-cs-fixer.php
./vendor/bin/php-cs-fixer fix

# PHPStan
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse
```

### Frontend (TypeScript/Vue)

#### ESLint + Prettier

```typescript
// ✅ Use interfaces for types
interface User {
  id: number
  name: string
  email: string
}

// ✅ Use Composition API
import { ref, computed } from 'vue'

export default {
  setup() {
    const count = ref(0)
    const doubled = computed(() => count.value * 2)
    
    return { count, doubled }
  }
}

// ✅ Type props
interface Props {
  title: string
  count?: number
}

const props = defineProps<Props>()

// ✅ Use TypeScript for everything
const fetchUser = async (id: number): Promise<User> => {
  const response = await api.get<User>(`/users/${id}`)
  return response.data
}
```

#### Reglas

- Indentación: 2 espacios
- Usar single quotes
- Semicolons: no
- Trailing commas: es5
- Nombrado:
  - Components: `PascalCase.vue`
  - Composables: `useXxx.ts`
  - Types: `PascalCase`
  - Variables: `camelCase`
  - Constants: `UPPER_SNAKE_CASE`

#### Linting

```bash
# ESLint
npm run lint

# Prettier
npm run format

# Type check
npm run type-check
```

### Git Commits

```bash
# ✅ Buenos mensajes
git commit -m "feat(auth): add password recovery"
git commit -m "fix(documents): resolve slug generation bug"
git commit -m "docs(api): update endpoint documentation"

# ❌ Malos mensajes
git commit -m "fixed stuff"
git commit -m "WIP"
git commit -m "asdf"
```

---

## Testing

### Backend Tests

```bash
# Ejecutar todos los tests
php artisan test

# Con coverage
php artisan test --coverage

# Tests específicos
php artisan test tests/Feature/AuthTest.php

# Tests por suite
php artisan test --testsuite=Unit
```

**Coverage Mínimo: 70%**

### Frontend Tests

```bash
# Tests unitarios
npm run test:unit

# Con coverage
npm run test:coverage

# Tests E2E
npm run test:e2e

# Watch mode
npm run test:watch
```

**Coverage Mínimo: 70%**

### Tests Requeridos

Para cada PR:
- [ ] Tests unitarios para nuevas funciones
- [ ] Tests de integración para endpoints API
- [ ] Tests de componentes para UI
- [ ] Tests E2E para flujos críticos (opcional)
- [ ] Todos los tests existentes pasan

---

## Documentación

### Documentar Código

**PHP:**
```php
/**
 * Retrieve a document by its ID.
 *
 * @param  int  $id  The document ID
 * @return \App\Models\Document
 * 
 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
 */
public function findDocument(int $id): Document
{
    return Document::findOrFail($id);
}
```

**TypeScript:**
```typescript
/**
 * Fetches a user by ID from the API
 * 
 * @param id - The user ID to fetch
 * @returns Promise resolving to User object
 * @throws {ApiError} When the user is not found
 */
async function fetchUser(id: number): Promise<User> {
  // ...
}
```

### Actualizar Docs

Si tu cambio afecta:
- **API**: Actualiza `API_DOCUMENTATION.md`
- **Base de Datos**: Actualiza `DATABASE_SCHEMA.md`
- **Deployment**: Actualiza `DEPLOYMENT_GUIDE.md`
- **Testing**: Actualiza `TESTING_STRATEGY.md`
- **Seguridad**: Actualiza `SECURITY_GUIDE.md`
- **README**: Actualiza si cambia setup o uso

---

## Pull Requests

### Template de PR

```markdown
## Descripción
Breve descripción de los cambios.

## Tipo de Cambio
- [ ] Bug fix (non-breaking change)
- [ ] New feature (non-breaking change)
- [ ] Breaking change
- [ ] Documentation update

## ¿Cómo se ha probado?
Describe los tests que ejecutaste.

## Checklist
- [ ] Mi código sigue los estándares del proyecto
- [ ] He realizado una auto-revisión
- [ ] He comentado código complejo
- [ ] He actualizado la documentación
- [ ] Mis cambios no generan warnings
- [ ] He agregado tests que prueban mis cambios
- [ ] Tests unitarios y de integración pasan
- [ ] He verificado mi código y corregido typos

## Screenshots (si aplica)
```

### Proceso de Review

1. **Autor crea PR**
   - Llena template completo
   - Asigna reviewers
   - Agrega labels apropiados

2. **CI ejecuta checks**
   - Tests automáticos
   - Linting
   - Coverage check
   - Security scan

3. **Code Review**
   - Al menos 1 aprobación requerida
   - Todos los comentarios deben resolverse
   - CI debe estar en verde

4. **Merge**
   - Squash and merge (preferido)
   - Rebase and merge (si hay buenos commits)
   - Crear merge commit (solo para features grandes)

### Durante Review

**Como Autor:**
- Responde a comentarios rápidamente
- Haz cambios solicitados
- Agradece feedback constructivo
- Pregunta si algo no está claro

**Como Reviewer:**
- Se constructivo y específico
- Sugiere soluciones, no solo problemas
- Aprueba cuando esté listo
- Bloquea si hay issues críticos

---

## Áreas de Contribución

### 🔰 Good First Issues

Issues etiquetados como `good-first-issue` son ideales para nuevos contribuidores:
- Bien documentados
- Alcance limitado
- No requieren conocimiento profundo del código

### 🌟 Priority Issues

Issues críticos:
- Bugs de seguridad
- Bugs bloqueantes
- Features de alta prioridad

### 📚 Documentación

Siempre bienvenida:
- Corregir typos
- Agregar ejemplos
- Clarificar secciones
- Traducir docs

### 🎨 UI/UX

Mejoras visuales:
- Diseño responsive
- Accesibilidad
- Performance frontend

### ⚡ Performance

Optimizaciones:
- Queries de DB
- Carga de assets
- Tiempos de respuesta API

---

## Recursos

### Documentación

- [Laravel Docs](https://laravel.com/docs)
- [Vue.js Guide](https://vuejs.org/guide/)
- [TypeScript Handbook](https://www.typescriptlang.org/docs/)

### Tools

- [GitHub Desktop](https://desktop.github.com/)
- [VS Code](https://code.visualstudio.com/)
- [Postman](https://www.postman.com/)
- [TablePlus](https://tableplus.com/)

### Contacto

- **Discusiones**: GitHub Discussions
- **Chat**: Discord (link en README)
- **Email**: contribute@yourdomain.com

---

## Agradecimientos

Gracias a todos nuestros contribuidores! 🎉

Cada contribución, sin importar el tamaño, es valiosa para el proyecto.

---

**Última actualización:** 2026-02-17  
**Versión:** 1.0
