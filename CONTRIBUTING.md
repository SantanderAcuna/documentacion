# Guía de Contribución

¡Gracias por tu interés en contribuir al CMS Gubernamental! Esta guía te ayudará a entender cómo puedes colaborar en el proyecto.

## 📋 Tabla de Contenidos

- [Código de Conducta](#código-de-conducta)
- [¿Cómo Puedo Contribuir?](#cómo-puedo-contribuir)
- [Proceso de Desarrollo](#proceso-de-desarrollo)
- [Estándares de Código](#estándares-de-código)
- [Commits y Pull Requests](#commits-y-pull-requests)
- [Testing](#testing)

## 🤝 Código de Conducta

Este proyecto se adhiere a un código de conducta profesional. Se espera que todos los contribuyentes:

- Sean respetuosos y profesionales
- Acepten críticas constructivas
- Se enfoquen en lo mejor para el proyecto
- Muestren empatía hacia otros miembros

## 🛠️ ¿Cómo Puedo Contribuir?

### Reportar Bugs

1. Verifica que el bug no haya sido reportado previamente
2. Abre un nuevo issue usando la plantilla de bug
3. Incluye:
   - Descripción clara del problema
   - Pasos para reproducir
   - Comportamiento esperado vs actual
   - Screenshots si aplica
   - Información del entorno

### Sugerir Mejoras

1. Abre un issue usando la plantilla de feature request
2. Describe la funcionalidad propuesta
3. Explica por qué sería útil
4. Considera posibles implementaciones

### Contribuir Código

1. Fork el repositorio
2. Crea una rama feature/bugfix
3. Haz tus cambios
4. Envía un Pull Request

## 🔄 Proceso de Desarrollo

### 1. Setup Inicial

```bash
# Fork y clonar
git clone https://github.com/tu-usuario/documentacion.git
cd documentacion

# Configurar upstream
git remote add upstream https://github.com/SantanderAcuna/documentacion.git

# Iniciar con Docker
docker-compose up -d
```

### 2. Crear Rama

```bash
# Actualizar main
git checkout main
git pull upstream main

# Crear rama feature
git checkout -b feature/nombre-descriptivo

# O rama bugfix
git checkout -b bugfix/nombre-descriptivo
```

### 3. Hacer Cambios

- Sigue los estándares de código
- Escribe tests
- Actualiza documentación si es necesario
- Haz commits frecuentes y descriptivos

### 4. Probar Cambios

```bash
# Backend
docker-compose exec backend php artisan test
docker-compose exec backend vendor/bin/phpstan analyse

# Frontend Admin
docker-compose exec frontend-admin npm run lint
docker-compose exec frontend-admin npm run test

# Frontend Public
docker-compose exec frontend-public npm run lint
docker-compose exec frontend-public npm run test
docker-compose exec frontend-public npm run test:a11y
```

### 5. Enviar Pull Request

```bash
# Push a tu fork
git push origin feature/nombre-descriptivo

# Crear PR en GitHub
# Usa la plantilla de PR
# Solicita revisión
```

## 📝 Estándares de Código

### PHP (Backend)

- **Estándar:** PSR-12
- **Análisis Estático:** PHPStan Level 8
- **Documentación:** PHPDoc

```php
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Servicio para gestión de usuarios
 */
class UserService
{
    /**
     * Crear un nuevo usuario
     *
     * @param array<string, mixed> $data
     * @return User
     */
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        
        return User::create($data);
    }
}
```

### TypeScript/Vue (Frontend)

- **Guía de Estilos:** Vue 3 Official
- **Mode:** TypeScript strict
- **Composition API:** `<script setup>`

```vue
<script setup lang="ts">
import { ref, computed } from 'vue'

interface User {
  id: number
  name: string
  email: string
}

const props = defineProps<{
  user: User
}>()

const emit = defineEmits<{
  update: [user: User]
}>()

const isActive = ref(true)

const displayName = computed(() => {
  return `${props.user.name} (${props.user.email})`
})

function handleUpdate() {
  emit('update', props.user)
}
</script>

<template>
  <div class="user-card">
    <h3>{{ displayName }}</h3>
    <button @click="handleUpdate">Update</button>
  </div>
</template>

<style scoped lang="scss">
.user-card {
  padding: 1rem;
  border: 1px solid #ddd;
}
</style>
```

### Nombres

**PHP:**
- Clases: PascalCase (`UserController`)
- Métodos: camelCase (`getUserById`)
- Variables: snake_case en BD, camelCase en código
- Constantes: UPPER_SNAKE_CASE (`MAX_ATTEMPTS`)

**TypeScript/Vue:**
- Componentes: PascalCase (`UserCard.vue`)
- Composables: camelCase con prefijo use (`useAuth`)
- Variables: camelCase (`userName`)
- Constantes: UPPER_SNAKE_CASE (`API_URL`)

### Comentarios

Solo cuando la lógica no es obvia:

```php
// ❌ Mal - obvio
// Incrementar contador
$counter++;

// ✅ Bien - explica lógica compleja
// Aplicar descuento solo si el usuario es nuevo Y 
// el monto es mayor a $100 (regla de negocio RN-042)
if ($user->isNew() && $amount > 100) {
    $amount *= 0.9;
}
```

## 💾 Commits y Pull Requests

### Conventional Commits

Usamos [Conventional Commits](https://www.conventionalcommits.org/):

```
tipo(alcance): descripción corta

[Descripción larga opcional]

[Footer opcional]
```

**Tipos:**
- `feat`: Nueva funcionalidad
- `fix`: Corrección de bug
- `docs`: Cambios en documentación
- `style`: Formato, sin cambios funcionales
- `refactor`: Refactorización sin cambios funcionales
- `test`: Agregar/modificar tests
- `chore`: Mantenimiento, deps, config

**Ejemplos:**

```bash
feat(auth): agregar login con Sanctum

fix(pqrs): corregir validación de formulario

docs(readme): actualizar instrucciones de instalación

refactor(user-service): extraer lógica de validación

test(auth): agregar tests de autenticación
```

### Pull Requests

**Título:** Igual que commit principal

**Descripción:**
```markdown
## Descripción
Breve descripción de los cambios

## Tipo de Cambio
- [ ] Bug fix
- [ ] Nueva funcionalidad
- [ ] Breaking change
- [ ] Documentación

## Checklist
- [ ] Tests pasan localmente
- [ ] Código sigue estándares
- [ ] Documentación actualizada
- [ ] No hay warnings de lint
- [ ] Tests agregados/actualizados
- [ ] WCAG 2.1 AA (si es frontend público)

## Capturas (si aplica)
```

### Code Review

Tu PR será revisado por al menos un mantenedor. Se verificará:

- ✅ Tests pasan
- ✅ Sin conflictos de merge
- ✅ Código limpio y legible
- ✅ Sigue estándares
- ✅ Documentación actualizada
- ✅ Sin regressions

## 🧪 Testing

### Backend

```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter=UserTest

# Con cobertura
php artisan test --coverage --min=80

# PHPStan
vendor/bin/phpstan analyse
```

### Frontend

```bash
# Unit tests
npm run test:unit

# E2E tests
npm run test:e2e

# Accesibilidad (solo público)
npm run test:a11y

# Lint
npm run lint
```

### Cobertura Mínima

- Backend: 80%
- Frontend: 80%

## 🔒 Seguridad

**NO COMMITEAR:**
- Secretos o credenciales
- API keys
- Contraseñas
- Tokens

**SÍ USAR:**
- Variables de entorno (`.env`)
- Secrets de GitHub Actions
- Vault en producción

Si encuentras una vulnerabilidad de seguridad, **NO** abras un issue público. Contacta directamente al equipo.

## 📚 Recursos

- [Documentación Laravel](https://laravel.com/docs/12.x)
- [Documentación Vue 3](https://vuejs.org/guide/)
- [Vuestic UI](https://vuestic.dev/)
- [GOV.CO Design](https://www.gov.co/)
- [WCAG 2.1](https://www.w3.org/WAI/WCAG21/quickref/)

## ❓ Preguntas

Si tienes dudas:

1. Revisa la documentación existente
2. Busca en issues cerrados
3. Abre un issue de pregunta
4. Participa en Discussions

---

¡Gracias por contribuir al CMS Gubernamental! 🇨🇴
