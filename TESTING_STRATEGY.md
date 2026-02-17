# Estrategia de Testing - Portal de Configuración VPS

## Objetivos de Testing

### Cobertura Mínima
- **Backend:** 70% code coverage
- **Frontend:** 70% code coverage
- **E2E:** Flujos críticos cubiertos

### Tipos de Tests
1. Tests Unitarios (Unit Tests)
2. Tests de Integración (Integration Tests)
3. Tests Funcionales (Feature Tests)
4. Tests End-to-End (E2E Tests)
5. Tests de Performance
6. Tests de Seguridad

---

## 1. Testing Backend (Laravel + PHPUnit)

### 1.1 Setup de Testing

```bash
# Instalar PHPUnit
composer require --dev phpunit/phpunit

# Crear database de testing
mysql -u root -p << EOF
CREATE DATABASE vps_portal_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL ON vps_portal_testing.* TO 'vps_portal'@'localhost';
EOF

# Configurar phpunit.xml
cp phpunit.xml.dist phpunit.xml
```

**phpunit.xml**:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="mysql"/>
        <env name="DB_DATABASE" value="vps_portal_testing"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
    </php>
    <coverage>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
        <exclude>
            <directory>./app/Console</directory>
            <file>./app/Providers/RouteServiceProvider.php</file>
        </exclude>
        <report>
            <html outputDirectory="./coverage-html"/>
            <text outputFile="php://stdout" showUncoveredFiles="true"/>
        </report>
    </coverage>
</phpunit>
```

### 1.2 Tests Unitarios

**Ejemplo: UserTest.php**
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function user_can_have_a_role()
    {
        $user = User::factory()->create();
        $user->assignRole('editor');
        
        $this->assertTrue($user->hasRole('editor'));
        $this->assertFalse($user->hasRole('admin'));
    }
    
    /** @test */
    public function user_can_have_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('editor');
        
        $this->assertTrue($user->can('create_documents'));
        $this->assertTrue($user->can('edit_own_documents'));
        $this->assertFalse($user->can('manage_users'));
    }
    
    /** @test */
    public function user_full_name_accessor_works()
    {
        $user = User::factory()->create([
            'name' => 'John Doe'
        ]);
        
        $this->assertEquals('John Doe', $user->name);
    }
}
```

**Ejemplo: DocumentTest.php**
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Document;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function document_belongs_to_user()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['user_id' => $user->id]);
        
        $this->assertInstanceOf(User::class, $document->user);
        $this->assertEquals($user->id, $document->user->id);
    }
    
    /** @test */
    public function document_belongs_to_category()
    {
        $category = Category::factory()->create();
        $document = Document::factory()->create(['category_id' => $category->id]);
        
        $this->assertInstanceOf(Category::class, $document->category);
    }
    
    /** @test */
    public function document_can_have_tags()
    {
        $document = Document::factory()->create();
        $document->tags()->createMany([
            ['name' => 'ssh', 'slug' => 'ssh'],
            ['name' => 'security', 'slug' => 'security'],
        ]);
        
        $this->assertCount(2, $document->tags);
        $this->assertTrue($document->tags->contains('name', 'ssh'));
    }
    
    /** @test */
    public function document_slug_is_generated_from_title()
    {
        $document = Document::factory()->create([
            'title' => 'Configuración de SSH Avanzada'
        ]);
        
        $this->assertEquals('configuracion-de-ssh-avanzada', $document->slug);
    }
}
```

### 1.3 Tests de Integración (Feature Tests)

**Ejemplo: AuthenticationTest.php**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecureP@ss123',
            'password_confirmation' => 'SecureP@ss123',
        ]);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['user' => ['id', 'name', 'email']]
            ]);
        
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }
    
    /** @test */
    public function user_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);
        
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'existing@example.com',
            'password' => 'SecureP@ss123',
            'password_confirmation' => 'SecureP@ss123',
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
    
    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123')
        ]);
        
        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['user' => ['id', 'name', 'email', 'role']]
            ]);
        
        $this->assertAuthenticated();
    }
    
    /** @test */
    public function user_cannot_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('correct-password')
        ]);
        
        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ]);
        
        $response->assertStatus(401);
        $this->assertGuest();
    }
    
    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->postJson('/api/auth/logout');
        
        $response->assertStatus(200);
    }
}
```

**Ejemplo: DocumentCrudTest.php**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Document;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentCrudTest extends TestCase
{
    use RefreshDatabase;
    
    protected $editor;
    protected $category;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->editor = User::factory()->create();
        $this->editor->assignRole('editor');
        
        $this->category = Category::factory()->create();
    }
    
    /** @test */
    public function editor_can_create_document()
    {
        $response = $this->actingAs($this->editor)
            ->postJson('/api/documents', [
                'title' => 'Test Document',
                'summary' => 'This is a test document summary for testing purposes',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod.',
                'category_id' => $this->category->id,
                'tags' => ['test', 'example'],
                'status' => 'draft',
            ]);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'slug', 'status']
            ]);
        
        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'user_id' => $this->editor->id
        ]);
    }
    
    /** @test */
    public function editor_can_update_own_document()
    {
        $document = Document::factory()->create([
            'user_id' => $this->editor->id,
            'title' => 'Original Title'
        ]);
        
        $response = $this->actingAs($this->editor)
            ->putJson("/api/documents/{$document->id}", [
                'title' => 'Updated Title'
            ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Updated Title'
        ]);
    }
    
    /** @test */
    public function editor_cannot_update_others_document()
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('editor');
        
        $document = Document::factory()->create([
            'user_id' => $otherUser->id
        ]);
        
        $response = $this->actingAs($this->editor)
            ->putJson("/api/documents/{$document->id}", [
                'title' => 'Hacked Title'
            ]);
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function viewer_cannot_create_document()
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');
        
        $response = $this->actingAs($viewer)
            ->postJson('/api/documents', [
                'title' => 'Test Document',
                'summary' => 'Test summary that meets minimum length requirement',
                'content' => 'Test content that is long enough to pass validation rules',
                'category_id' => $this->category->id,
                'status' => 'draft',
            ]);
        
        $response->assertStatus(403);
    }
    
    /** @test */
    public function document_views_count_increments()
    {
        $document = Document::factory()->create();
        
        $response = $this->getJson("/api/documents/{$document->id}");
        
        $response->assertStatus(200);
        
        $document->refresh();
        $this->assertEquals(1, $document->views_count);
    }
}
```

### 1.4 Comandos de Testing

```bash
# Ejecutar todos los tests
php artisan test

# Tests con coverage
php artisan test --coverage

# Solo tests unitarios
php artisan test --testsuite=Unit

# Solo tests de feature
php artisan test --testsuite=Feature

# Tests específicos
php artisan test tests/Feature/AuthenticationTest.php

# Tests con output detallado
php artisan test --verbose

# Tests en paralelo (más rápido)
php artisan test --parallel
```

---

## 2. Testing Frontend (Vue + Vitest)

### 2.1 Setup de Testing

```bash
# Instalar dependencias
npm install --save-dev vitest @vue/test-utils happy-dom @vitest/coverage-v8
```

**vite.config.ts**:
```typescript
/// <reference types="vitest" />
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  test: {
    globals: true,
    environment: 'happy-dom',
    coverage: {
      provider: 'v8',
      reporter: ['text', 'html', 'lcov'],
      exclude: [
        'node_modules/',
        'tests/',
        '**/*.spec.ts',
        '**/*.test.ts',
      ],
      all: true,
      lines: 70,
      functions: 70,
      branches: 70,
      statements: 70,
    },
  },
})
```

**package.json**:
```json
{
  "scripts": {
    "test:unit": "vitest",
    "test:coverage": "vitest run --coverage",
    "test:ui": "vitest --ui"
  }
}
```

### 2.2 Tests de Componentes

**BaseButton.spec.ts**:
```typescript
import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import BaseButton from '@/components/common/BaseButton.vue'

describe('BaseButton', () => {
  it('renders slot content', () => {
    const wrapper = mount(BaseButton, {
      slots: {
        default: 'Click me'
      }
    })
    
    expect(wrapper.text()).toContain('Click me')
  })
  
  it('applies variant class', () => {
    const wrapper = mount(BaseButton, {
      props: {
        variant: 'primary'
      }
    })
    
    expect(wrapper.classes()).toContain('btn-primary')
  })
  
  it('emits click event', async () => {
    const wrapper = mount(BaseButton)
    
    await wrapper.trigger('click')
    
    expect(wrapper.emitted()).toHaveProperty('click')
  })
  
  it('is disabled when loading', () => {
    const wrapper = mount(BaseButton, {
      props: {
        loading: true
      }
    })
    
    expect(wrapper.attributes('disabled')).toBeDefined()
  })
})
```

**DocumentCard.spec.ts**:
```typescript
import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import DocumentCard from '@/components/documents/DocumentCard.vue'

describe('DocumentCard', () => {
  const mockDocument = {
    id: 1,
    title: 'Test Document',
    summary: 'Test summary',
    category: { name: 'SSH', slug: 'ssh' },
    author: { name: 'John Doe' },
    views: 100,
    favorites_count: 10,
    is_favorited: false,
    created_at: '2026-01-01T00:00:00.000000Z'
  }
  
  it('renders document information', () => {
    const wrapper = mount(DocumentCard, {
      props: { document: mockDocument }
    })
    
    expect(wrapper.text()).toContain('Test Document')
    expect(wrapper.text()).toContain('Test summary')
    expect(wrapper.text()).toContain('John Doe')
  })
  
  it('emits favorite event when favorite button clicked', async () => {
    const wrapper = mount(DocumentCard, {
      props: { document: mockDocument }
    })
    
    await wrapper.find('[data-test="favorite-btn"]').trigger('click')
    
    expect(wrapper.emitted()).toHaveProperty('favorite')
    expect(wrapper.emitted('favorite')?.[0]).toEqual([mockDocument.id])
  })
  
  it('shows favorited state', () => {
    const wrapper = mount(DocumentCard, {
      props: {
        document: { ...mockDocument, is_favorited: true }
      }
    })
    
    const favoriteBtn = wrapper.find('[data-test="favorite-btn"]')
    expect(favoriteBtn.classes()).toContain('favorited')
  })
})
```

### 2.3 Tests de Stores (Pinia)

**authStore.spec.ts**:
```typescript
import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/stores/authStore'
import axios from 'axios'

vi.mock('axios')

describe('Auth Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })
  
  it('initializes with null user', () => {
    const store = useAuthStore()
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })
  
  it('sets user on login', async () => {
    const store = useAuthStore()
    const mockUser = { id: 1, name: 'John', email: 'john@example.com' }
    
    vi.mocked(axios.post).mockResolvedValue({
      data: { success: true, data: { user: mockUser } }
    })
    
    await store.login({ email: 'john@example.com', password: 'password' })
    
    expect(store.user).toEqual(mockUser)
    expect(store.isAuthenticated).toBe(true)
  })
  
  it('clears user on logout', async () => {
    const store = useAuthStore()
    store.user = { id: 1, name: 'John', email: 'john@example.com' }
    
    vi.mocked(axios.post).mockResolvedValue({
      data: { success: true }
    })
    
    await store.logout()
    
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })
})
```

### 2.4 Tests de Composables

**useApi.spec.ts**:
```typescript
import { describe, it, expect, vi } from 'vitest'
import { useApi } from '@/composables/useApi'
import axios from 'axios'

vi.mock('axios')

describe('useApi', () => {
  it('makes GET request', async () => {
    const mockData = { id: 1, title: 'Test' }
    vi.mocked(axios.get).mockResolvedValue({ data: { data: mockData } })
    
    const { data, error, loading, execute } = useApi('/api/documents/1')
    
    await execute()
    
    expect(data.value).toEqual(mockData)
    expect(error.value).toBeNull()
    expect(loading.value).toBe(false)
  })
  
  it('handles errors', async () => {
    const mockError = { message: 'Not found' }
    vi.mocked(axios.get).mockRejectedValue(mockError)
    
    const { data, error, execute } = useApi('/api/documents/999')
    
    await execute()
    
    expect(data.value).toBeNull()
    expect(error.value).toEqual(mockError)
  })
})
```

---

## 3. Tests End-to-End (Cypress)

### 3.1 Setup E2E

```bash
# Instalar Cypress
npm install --save-dev cypress

# Abrir Cypress
npx cypress open
```

**cypress.config.ts**:
```typescript
import { defineConfig } from 'cypress'

export default defineConfig({
  e2e: {
    baseUrl: 'http://localhost:5173',
    specPattern: 'cypress/e2e/**/*.cy.{js,jsx,ts,tsx}',
    supportFile: 'cypress/support/e2e.ts',
  },
  env: {
    apiUrl: 'http://localhost:8000/api',
  },
})
```

### 3.2 Tests E2E Críticos

**auth.cy.ts**:
```typescript
describe('Authentication Flow', () => {
  beforeEach(() => {
    cy.visit('/login')
  })
  
  it('allows user to login with valid credentials', () => {
    cy.get('[data-test="email-input"]').type('editor@example.com')
    cy.get('[data-test="password-input"]').type('password')
    cy.get('[data-test="login-btn"]').click()
    
    cy.url().should('include', '/dashboard')
    cy.get('[data-test="user-menu"]').should('contain', 'Editor')
  })
  
  it('shows error with invalid credentials', () => {
    cy.get('[data-test="email-input"]').type('wrong@example.com')
    cy.get('[data-test="password-input"]').type('wrongpassword')
    cy.get('[data-test="login-btn"]').click()
    
    cy.get('[data-test="error-message"]').should('be.visible')
    cy.url().should('include', '/login')
  })
  
  it('validates required fields', () => {
    cy.get('[data-test="login-btn"]').click()
    
    cy.get('[data-test="email-error"]').should('contain', 'requerido')
    cy.get('[data-test="password-error"]').should('contain', 'requerido')
  })
})
```

**document-crud.cy.ts**:
```typescript
describe('Document CRUD', () => {
  beforeEach(() => {
    // Login as editor
    cy.login('editor@example.com', 'password')
  })
  
  it('creates a new document', () => {
    cy.visit('/documents/create')
    
    cy.get('[data-test="title-input"]').type('My Test Document')
    cy.get('[data-test="summary-input"]').type('This is a test summary')
    cy.get('[data-test="content-editor"]').type('# Test Content\n\nThis is test content')
    cy.get('[data-test="category-select"]').select('SSH')
    cy.get('[data-test="status-select"]').select('published')
    cy.get('[data-test="submit-btn"]').click()
    
    cy.url().should('match', /\/documents\/\d+/)
    cy.get('[data-test="document-title"]').should('contain', 'My Test Document')
  })
  
  it('edits existing document', () => {
    cy.visit('/documents/1/edit')
    
    cy.get('[data-test="title-input"]').clear().type('Updated Title')
    cy.get('[data-test="submit-btn"]').click()
    
    cy.get('[data-test="success-toast"]').should('be.visible')
    cy.get('[data-test="document-title"]').should('contain', 'Updated Title')
  })
  
  it('searches documents', () => {
    cy.visit('/documents')
    
    cy.get('[data-test="search-input"]').type('SSH')
    cy.get('[data-test="search-results"]').should('be.visible')
    cy.get('[data-test="document-card"]').should('have.length.greaterThan', 0)
  })
})
```

---

## 4. Tests de Performance

### 4.1 Laravel Telescope (Desarrollo)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 4.2 Load Testing con k6

**load-test.js**:
```javascript
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '1m', target: 10 },  // Ramp up to 10 users
    { duration: '3m', target: 50 },  // Stay at 50 users
    { duration: '1m', target: 0 },   // Ramp down
  ],
  thresholds: {
    http_req_duration: ['p(95)<500'], // 95% of requests < 500ms
    http_req_failed: ['rate<0.01'],   // Less than 1% errors
  },
};

export default function () {
  // Test homepage
  let res = http.get('https://yourdomain.com/api/documents');
  check(res, {
    'status is 200': (r) => r.status === 200,
    'response time < 500ms': (r) => r.timings.duration < 500,
  });
  
  sleep(1);
}
```

Ejecutar:
```bash
k6 run load-test.js
```

---

## 5. Tests de Seguridad

### 5.1 OWASP ZAP Scan

```bash
# Pull ZAP Docker image
docker pull owasp/zap2docker-stable

# Run baseline scan
docker run -t owasp/zap2docker-stable zap-baseline.py \
  -t https://yourdomain.com \
  -r zap-report.html
```

### 5.2 Dependency Scanning

```bash
# Backend (PHP)
composer audit

# Frontend (npm)
npm audit
npm audit fix
```

---

## 6. CI/CD Integration

### GitHub Actions Workflow

**.github/workflows/tests.yml**:
```yaml
name: Tests

on: [push, pull_request]

jobs:
  backend-tests:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: vps_portal_testing
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
          
      redis:
        image: redis:7
        options: >-
          --health-cmd="redis-cli ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: mbstring, xml, bcmath, mysql, redis
          coverage: xdebug
          
      - name: Install Dependencies
        working-directory: ./backend
        run: composer install --prefer-dist --no-progress
        
      - name: Run Tests
        working-directory: ./backend
        run: php artisan test --coverage --min=70
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: vps_portal_testing
          DB_USERNAME: root
          DB_PASSWORD: password
          REDIS_HOST: 127.0.0.1
          
      - name: Upload Coverage
        uses: codecov/codecov-action@v3
        with:
          files: ./backend/coverage.xml
  
  frontend-tests:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '20'
          cache: 'npm'
          cache-dependency-path: frontend/package-lock.json
          
      - name: Install Dependencies
        working-directory: ./frontend
        run: npm ci
        
      - name: Run Tests
        working-directory: ./frontend
        run: npm run test:coverage
        
      - name: Upload Coverage
        uses: codecov/codecov-action@v3
        with:
          files: ./frontend/coverage/lcov.info
  
  e2e-tests:
    runs-on: ubuntu-latest
    needs: [backend-tests, frontend-tests]
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '20'
          
      - name: Install Dependencies
        working-directory: ./frontend
        run: npm ci
        
      - name: Run Cypress
        uses: cypress-io/github-action@v5
        with:
          working-directory: ./frontend
          start: npm run dev
          wait-on: 'http://localhost:5173'
          browser: chrome
          
      - name: Upload Screenshots
        uses: actions/upload-artifact@v3
        if: failure()
        with:
          name: cypress-screenshots
          path: frontend/cypress/screenshots
```

---

## Checklist de Testing

### Pre-commit
- [ ] Tests unitarios pasando
- [ ] Linting sin errores
- [ ] No hay console.logs

### Pre-PR
- [ ] Todos los tests pasando
- [ ] Coverage mínimo 70%
- [ ] Tests E2E críticos pasando
- [ ] No hay warnings de seguridad

### Pre-deploy
- [ ] Tests en CI pasando
- [ ] Load tests aceptables
- [ ] Security scan sin críticos
- [ ] Smoke tests en staging

---

**Última actualización:** 2026-02-17  
**Versión:** 1.0
