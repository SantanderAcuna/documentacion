# ADR-002: Laravel Sanctum para Autenticación

**Estado:** Aceptado  
**Fecha:** 2026-02-17  
**Decisores:** Equipo de Arquitectura, Security Officer  
**Contexto:** Fase 1 - Constitución del Proyecto

---

## Contexto y Problema

El CMS Gubernamental requiere autenticación segura para:
- Panel administrativo (editores, administradores)
- API REST
- Protección de datos personales (Ley 1581/2012)

Debemos elegir una solución de autenticación que sea:
- Segura por diseño
- Compatible con Vue 3 SPA
- Cumpla normativas colombianas
- Soporte CSRF protection

---

## Factores de Decisión

- **Seguridad:** Cumplimiento SEC-01 a SEC-10
- **Compatibilidad SPA:** Vue 3 con Axios
- **CSRF Protection:** Requerido por normativa
- **Simplicidad:** Fácil de implementar y mantener
- **Escalabilidad:** Soportar múltiples dispositivos
- **Auditoría:** Rastrear sesiones y actividad

---

## Opciones Consideradas

### Opción 1: Laravel Sanctum con Cookies ✅
**Descripción:** Autenticación stateful con cookies HTTP-Only

**Pros:**
- ✅ Cookies HTTP-Only (JavaScript no puede acceder)
- ✅ SameSite=Strict (previene CSRF)
- ✅ Protección CSRF integrada
- ✅ Stateful (sesiones en Redis)
- ✅ Soporte nativo en Laravel
- ✅ Fácil implementación con Axios `withCredentials`

**Contras:**
- ⚠️ Requiere mismo dominio o subdominios
- ⚠️ No funciona bien con apps móviles nativas

### Opción 2: Laravel Passport (OAuth2)
**Descripción:** OAuth2 server completo

**Pros:**
- ✅ Estándar OAuth2
- ✅ Soporte tokens de terceros

**Contras:**
- ❌ Overkill para nuestra necesidad
- ❌ Más complejo de implementar
- ❌ No necesitamos OAuth2

### Opción 3: JWT (tymon/jwt-auth)
**Descripción:** JSON Web Tokens en localStorage

**Pros:**
- ✅ Stateless
- ✅ Funciona con cualquier dominio

**Contras:**
- ❌ Tokens en localStorage (vulnerable a XSS)
- ❌ No protege contra CSRF
- ❌ Difícil invalidar tokens
- ❌ No cumple SEC-04 (protección CSRF)

---

## Decisión

**Elegimos Opción 1: Laravel Sanctum con Cookies HTTP-Only**

### Implementación

#### Backend (Laravel)
```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 
    'localhost,localhost:3000,localhost:3001,127.0.0.1'
)),

'middleware' => [
    'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
],
```

#### Frontend (Vue 3 + Axios)
```typescript
// axios.config.ts
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true, // Enviar cookies
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Interceptor para CSRF token
api.interceptors.request.use(async (config) => {
  await fetch('/sanctum/csrf-cookie', { credentials: 'include' });
  return config;
});
```

#### Flujo de Autenticación
1. Cliente solicita `/sanctum/csrf-cookie`
2. Laravel envía cookie XSRF-TOKEN
3. Cliente hace POST `/api/login` con credenciales
4. Laravel valida y crea sesión
5. Laravel envía cookie de sesión (HTTP-Only)
6. Cliente usa cookie en requests subsecuentes

---

## Consecuencias

### Positivas
- ✅ **Seguridad máxima:** Cookies HTTP-Only + SameSite
- ✅ **CSRF protegido:** Token en header X-XSRF-TOKEN
- ✅ **Cumple normativas:** SEC-04, SEC-09
- ✅ **Auditable:** Sesiones en Redis con TTL
- ✅ **Fácil logout:** Invalidar sesión server-side
- ✅ **Rate limiting:** Nativo en Laravel

### Negativas
- ⚠️ **Requiere HTTPS:** Cookies Secure
- ⚠️ **Mismo dominio:** Backend y frontend en mismo dominio raíz
- ⚠️ **No mobile nativo:** Para apps móviles usar Sanctum API tokens

### Neutrales
- ℹ️ **Sesiones en Redis:** Consumo de memoria predecible
- ℹ️ **TTL configurable:** `SESSION_LIFETIME=120` (minutos)

---

## Configuración de Seguridad

### Cookies
```php
// config/session.php
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => true,
'same_site' => 'strict',
'domain' => env('SESSION_DOMAIN', '.alcaldia.gov.co'),
```

### CORS
```php
// config/cors.php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'supports_credentials' => true,
'allowed_origins' => [
    'https://admin.alcaldia.gov.co',
    'https://www.alcaldia.gov.co',
],
```

### Rate Limiting
```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});
```

---

## Validación

### Criterios de Éxito
- [x] Cookies tienen flags: HttpOnly, Secure, SameSite=Strict
- [ ] CSRF token en todas las requests mutantes
- [ ] Rate limiting 5 intentos / 15 min
- [ ] Logout invalida sesión server-side
- [ ] Tests de autenticación pasan

### Tests
```php
// tests/Feature/AuthenticationTest.php
public function test_user_can_login()
{
    $user = User::factory()->create();
    
    $response = $this->post('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    
    $response->assertOk();
    $this->assertAuthenticatedAs($user);
}

public function test_csrf_protection_works()
{
    $response = $this->post('/api/login', [], [
        'X-XSRF-TOKEN' => 'invalid',
    ]);
    
    $response->assertStatus(419); // CSRF token mismatch
}
```

---

## Alternativas para Apps Móviles

Si en el futuro se requiere app móvil nativa:
```php
// Usar Sanctum API Tokens
$user = User::find(1);
$token = $user->createToken('mobile-app')->plainTextToken;

// Cliente móvil usa Bearer token
// Authorization: Bearer {token}
```

---

## Referencias
- Laravel Sanctum: https://laravel.com/docs/12.x/sanctum
- OWASP Session Management: https://owasp.org/www-community/Session_Management_Cheat_Sheet
- Ley 1581/2012: Protección de datos personales

---

**Firmado:** Equipo de Arquitectura, Security Officer  
**Próxima revisión:** 2026-08-17 (6 meses)
