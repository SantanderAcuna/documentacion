# Guía de Seguridad - Portal de Configuración VPS

## Índice

1. [Principios de Seguridad](#principios-de-seguridad)
2. [Autenticación y Autorización](#autenticación-y-autorización)
3. [Protección de Datos](#protección-de-datos)
4. [Seguridad de API](#seguridad-de-api)
5. [Seguridad Frontend](#seguridad-frontend)
6. [Seguridad de Infraestructura](#seguridad-de-infraestructura)
7. [Auditoría y Compliance](#auditoría-y-compliance)
8. [Incident Response](#incident-response)

---

## Principios de Seguridad

### Defense in Depth (Defensa en Profundidad)
Múltiples capas de seguridad para que si una falla, otras sigan protegiendo:
- Frontend: Validación, sanitización
- Backend: Autenticación, autorización, validación
- Base de Datos: Permisos, encriptación
- Infraestructura: Firewall, SSL, monitoreo

### Principle of Least Privilege
- Usuarios solo tienen permisos necesarios
- Servicios con permisos mínimos
- Acceso basado en roles (RBAC)

### Security by Default
- Configuración segura por defecto
- HTTPS obligatorio
- Cookies seguras (HTTP-Only, Secure, SameSite)

---

## Autenticación y Autorización

### 1. Autenticación con Laravel Sanctum

#### Configuración Segura

```php
// config/sanctum.php
return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    'guard' => ['web'],

    'expiration' => 120, // 2 hours

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

#### Cookies Seguras

```php
// config/session.php
return [
    'driver' => env('SESSION_DRIVER', 'redis'),
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => true,
    'http_only' => true,  // Previene acceso vía JavaScript
    'same_site' => 'lax', // Protección CSRF
    'secure' => env('SESSION_SECURE_COOKIE', true), // Solo HTTPS
    'domain' => env('SESSION_DOMAIN'),
];
```

### 2. Política de Contraseñas

**Requisitos Mínimos:**
```php
// app/Rules/StrongPassword.php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    public function passes($attribute, $value)
    {
        return strlen($value) >= 8 &&
               preg_match('/[a-z]/', $value) &&      // Minúscula
               preg_match('/[A-Z]/', $value) &&      // Mayúscula
               preg_match('/[0-9]/', $value) &&      // Número
               preg_match('/[@$!%*#?&]/', $value);  // Especial
    }

    public function message()
    {
        return 'La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y caracteres especiales.';
    }
}
```

**Bcrypt Seguro:**
```php
use Illuminate\Support\Facades\Hash;

// Siempre usar bcrypt
$hashedPassword = Hash::make($request->password);

// Verificar
if (Hash::check($plainPassword, $hashedPassword)) {
    // Contraseña correcta
}

// Nunca comparar hashes directamente
// ❌ if ($hash1 === $hash2) 
// ✅ if (Hash::check($plain, $hash))
```

### 3. Multi-Factor Authentication (Futuro)

```php
// Preparación para 2FA
Schema::create('two_factor_auth', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('type'); // totp, sms, email
    $table->string('secret')->nullable();
    $table->string('recovery_codes')->nullable();
    $table->boolean('enabled')->default(false);
    $table->timestamp('verified_at')->nullable();
    $table->timestamps();
});
```

### 4. Rate Limiting

**Login Protection:**
```php
// app/Http/Controllers/AuthController.php
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

public function login(Request $request)
{
    $key = 'login:' . $request->ip();
    
    if (RateLimiter::tooManyAttempts($key, 5)) {
        $seconds = RateLimiter::availableIn($key);
        throw ValidationException::withMessages([
            'email' => ["Demasiados intentos. Intenta en {$seconds} segundos."],
        ])->status(429);
    }
    
    // Attempt login
    if (Auth::attempt($credentials)) {
        RateLimiter::clear($key);
        // ...
    } else {
        RateLimiter::hit($key, 60); // Block for 60 seconds
        // ...
    }
}
```

**API Rate Limiting:**
```php
// app/Providers/RouteServiceProvider.php
protected function configureRateLimiting()
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });
    
    RateLimiter::for('search', function (Request $request) {
        return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
    });
    
    RateLimiter::for('upload', function (Request $request) {
        return Limit::perHour(10)->by($request->user()->id);
    });
}
```

---

## Protección de Datos

### 1. SQL Injection Prevention

**✅ Usar Eloquent o Query Builder:**
```php
// ✅ CORRECTO - Eloquent
$user = User::where('email', $email)->first();

// ✅ CORRECTO - Query Builder con bindings
$users = DB::table('users')
    ->where('status', $status)
    ->get();

// ❌ INCORRECTO - SQL crudo sin bindings
$users = DB::select("SELECT * FROM users WHERE status = '$status'");

// ✅ CORRECTO - SQL crudo con bindings
$users = DB::select('SELECT * FROM users WHERE status = ?', [$status]);
```

**Validación de Entrada:**
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category_id' => 'required|exists:categories,id',
    ]);
    
    // Usar solo datos validados
    Document::create($validated);
}
```

### 2. XSS Prevention

**Backend - Escape de Output:**
```php
// Laravel escapa automáticamente en Blade
{{ $user->name }} // Escapado
{!! $user->bio !!} // Sin escapar - ¡Cuidado!

// En JSON API
return response()->json([
    'title' => htmlspecialchars($document->title, ENT_QUOTES, 'UTF-8')
]);
```

**Frontend - Sanitización:**
```typescript
// Usar DOMPurify para HTML user-generated
import DOMPurify from 'dompurify'

const sanitizeHtml = (dirty: string): string => {
  return DOMPurify.sanitize(dirty, {
    ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'ul', 'ol', 'li', 'a', 'code', 'pre'],
    ALLOWED_ATTR: ['href', 'class'],
    ALLOW_DATA_ATTR: false
  })
}

// Vue component
<div v-html="sanitizeHtml(content)"></div>
```

**Markdown Seguro:**
```typescript
// Usar marked con opciones seguras
import { marked } from 'marked'

marked.setOptions({
  headerIds: false,
  mangle: false,
  sanitize: true, // Remove HTML
})

const renderMarkdown = (markdown: string): string => {
  return marked.parse(markdown)
}
```

### 3. CSRF Protection

**Backend:**
```php
// Habilitado por defecto en Laravel
// app/Http/Middleware/VerifyCsrfToken.php

protected $except = [
    // Solo excluir endpoints públicos específicos
    'api/webhook/*',
];
```

**Frontend:**
```typescript
// axios-instance.ts
import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true, // Enviar cookies
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  }
})

// Interceptor para CSRF token
api.interceptors.request.use(config => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (token) {
    config.headers['X-CSRF-TOKEN'] = token
  }
  return config
})
```

### 4. Encriptación de Datos Sensibles

**Database Encryption:**
```php
// app/Models/User.php
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function ssn(): Attribute
{
    return Attribute::make(
        get: fn ($value) => decrypt($value),
        set: fn ($value) => encrypt($value),
    );
}
```

**File Encryption:**
```php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

// Encrypt before storing
$encrypted = Crypt::encryptString($sensitiveData);
Storage::put('sensitive.txt', $encrypted);

// Decrypt when retrieving
$decrypted = Crypt::decryptString(Storage::get('sensitive.txt'));
```

---

## Seguridad de API

### 1. Headers de Seguridad

**Nginx Configuration:**
```nginx
# /etc/nginx/sites-available/vps-portal
server {
    # ...
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;
    
    # Hide server version
    server_tokens off;
}
```

**Laravel Middleware:**
```php
// app/Http/Middleware/SecurityHeaders.php
namespace App\Http\Middleware;

use Closure;

class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        
        return $response;
    }
}
```

### 2. CORS Configuration

```php
// config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    
    'allowed_origins' => env('APP_ENV') === 'production' 
        ? ['https://yourdomain.com']
        : ['http://localhost:5173', 'http://127.0.0.1:5173'],
    
    'allowed_origins_patterns' => [],
    
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'Accept', 'X-CSRF-TOKEN'],
    
    'exposed_headers' => [],
    
    'max_age' => 3600,
    
    'supports_credentials' => true,
];
```

### 3. Input Validation

**Reglas Completas:**
```php
// app/Http/Requests/StoreDocumentRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create_documents');
    }

    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'min:10',
                'max:255',
                'unique:documents,title',
                'regex:/^[\pL\s\d\-_]+$/u', // Solo letras, números, guiones
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:documents,slug',
                'regex:/^[a-z0-9-]+$/', // Solo lowercase, números, guiones
            ],
            'summary' => [
                'required',
                'string',
                'min:20',
                'max:500',
            ],
            'content' => [
                'required',
                'string',
                'min:50',
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'tags' => [
                'nullable',
                'array',
                'max:10',
            ],
            'tags.*' => [
                'string',
                'max:50',
                'regex:/^[a-z0-9-]+$/',
            ],
            'status' => [
                'required',
                'in:draft,published',
            ],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'El título es obligatorio',
            'title.min' => 'El título debe tener al menos 10 caracteres',
            'content.min' => 'El contenido debe tener al menos 50 caracteres',
            // ...
        ];
    }
}
```

### 4. File Upload Security

```php
// app/Http/Controllers/FileController.php
public function upload(Request $request)
{
    $request->validate([
        'file' => [
            'required',
            'file',
            'max:5120', // 5MB
            'mimes:jpg,jpeg,png,gif,webp',
            'dimensions:max_width=2048,max_height=2048',
        ]
    ]);
    
    $file = $request->file('file');
    
    // Verificar MIME type real (no solo extensión)
    $mime = $file->getMimeType();
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($mime, $allowedMimes)) {
        return response()->json(['error' => 'Tipo de archivo no permitido'], 422);
    }
    
    // Generar nombre único
    $uuid = Str::uuid();
    $extension = $file->getClientOriginalExtension();
    $filename = $uuid . '.' . $extension;
    
    // Sanitizar nombre original
    $originalName = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $file->getClientOriginalName());
    
    // Subir a S3/Spaces
    $path = $file->storePubliclyAs(
        'uploads/' . date('Y/m'),
        $filename,
        'spaces'
    );
    
    // Guardar metadata en DB
    $uploadedFile = File::create([
        'user_id' => auth()->id(),
        'uuid' => $uuid,
        'filename' => $originalName,
        'path' => $path,
        'url' => Storage::disk('spaces')->url($path),
        'mime_type' => $mime,
        'size' => $file->getSize(),
        'disk' => 'spaces',
    ]);
    
    return response()->json([
        'success' => true,
        'data' => $uploadedFile
    ], 201);
}
```

---

## Seguridad Frontend

### 1. Content Security Policy

```html
<!-- index.html -->
<meta http-equiv="Content-Security-Policy" 
      content="default-src 'self'; 
               script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; 
               style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; 
               img-src 'self' data: https:; 
               font-src 'self' data:; 
               connect-src 'self' https://yourdomain.com;">
```

### 2. Secure Storage

```typescript
// utils/secureStorage.ts
export const secureStorage = {
  set(key: string, value: any): void {
    try {
      const encrypted = btoa(JSON.stringify(value))
      sessionStorage.setItem(key, encrypted)
    } catch (error) {
      console.error('Storage error:', error)
    }
  },
  
  get(key: string): any {
    try {
      const encrypted = sessionStorage.getItem(key)
      return encrypted ? JSON.parse(atob(encrypted)) : null
    } catch (error) {
      console.error('Storage error:', error)
      return null
    }
  },
  
  remove(key: string): void {
    sessionStorage.removeItem(key)
  }
}

// ❌ NO almacenar tokens en localStorage
// ✅ Usar httpOnly cookies para tokens
// ✅ sessionStorage solo para datos no sensibles
```

### 3. Dependency Security

```bash
# Auditar dependencias regularmente
npm audit

# Actualizar dependencias con vulnerabilidades
npm audit fix

# Revisar dependencias antes de instalar
npm view <package-name> 

# Usar package-lock.json
# Commitear package-lock.json al repositorio
```

---

## Seguridad de Infraestructura

### 1. Firewall (UFW)

```bash
# Configuración básica UFW
ufw default deny incoming
ufw default allow outgoing

# Permitir SSH, HTTP, HTTPS
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp

# Habilitar
ufw enable

# Ver status
ufw status verbose
```

### 2. Fail2Ban

```bash
# Instalar
apt install fail2ban

# Configurar
cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5
destemail = admin@yourdomain.com
sendername = Fail2Ban

[sshd]
enabled = true
port = ssh
logpath = /var/log/auth.log

[nginx-http-auth]
enabled = true
filter = nginx-http-auth
port = http,https
logpath = /var/log/nginx/error.log

[nginx-limit-req]
enabled = true
filter = nginx-limit-req
port = http,https
logpath = /var/log/nginx/error.log
EOF

# Reiniciar
systemctl restart fail2ban

# Ver bans
fail2ban-client status
```

### 3. SSL/TLS Configuration

```nginx
# Modern SSL configuration
ssl_protocols TLSv1.2 TLSv1.3;
ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384';
ssl_prefer_server_ciphers off;

ssl_session_timeout 1d;
ssl_session_cache shared:SSL:50m;
ssl_session_tickets off;

# OCSP stapling
ssl_stapling on;
ssl_stapling_verify on;
resolver 8.8.8.8 8.8.4.4 valid=300s;
resolver_timeout 5s;

# Certificate
ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
ssl_trusted_certificate /etc/letsencrypt/live/yourdomain.com/chain.pem;
```

### 4. Server Hardening

```bash
# Deshabilitar root login via SSH
sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config

# Solo permitir key-based auth
sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config

# Cambiar puerto SSH (opcional)
sed -i 's/#Port 22/Port 2222/' /etc/ssh/sshd_config

# Restart SSH
systemctl restart sshd

# Actualizar sistema regularmente
apt update && apt upgrade -y

# Setup unattended upgrades
apt install unattended-upgrades
dpkg-reconfigure --priority=low unattended-upgrades
```

---

## Auditoría y Compliance

### 1. Activity Logging

```php
// app/Services/ActivityLogger.php
namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(string $action, string $description, $subject = null)
    {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}

// Uso en controllers
ActivityLogger::log('document.created', "Created document: {$document->title}", $document);
```

### 2. Security Audits

**Checklist Mensual:**
- [ ] Revisar logs de acceso no autorizado
- [ ] Verificar usuarios con permisos elevados
- [ ] Auditar dependencias con vulnerabilidades
- [ ] Revisar configuración de firewall
- [ ] Verificar certificados SSL vigentes
- [ ] Revisar backups funcionando correctamente

**Tools:**
```bash
# Laravel Security Checker
composer require --dev enlightn/security-checker
./vendor/bin/security-checker security:check

# NPM Audit
npm audit

# OWASP ZAP
docker run -t owasp/zap2docker-stable zap-baseline.py -t https://yourdomain.com
```

### 3. Compliance (GDPR/Privacy)

**Data Protection:**
```php
// app/Http/Controllers/UserController.php
public function deleteAccount(Request $request)
{
    $user = auth()->user();
    
    // Anonymize user data (GDPR Right to be Forgotten)
    $user->update([
        'name' => 'Deleted User',
        'email' => 'deleted_' . $user->id . '@deleted.local',
        'avatar' => null,
        'bio' => null,
    ]);
    
    // Soft delete
    $user->delete();
    
    // Log action
    ActivityLogger::log('user.deleted', 'User deleted their account');
    
    return response()->json(['success' => true]);
}

// Export user data (GDPR Right to Data Portability)
public function exportData()
{
    $user = auth()->user();
    
    $data = [
        'user' => $user->toArray(),
        'documents' => $user->documents()->get()->toArray(),
        'favorites' => $user->favorites()->with('document')->get()->toArray(),
        'activity' => $user->activityLogs()->get()->toArray(),
    ];
    
    return response()->json($data);
}
```

---

## Incident Response

### 1. Preparación

**Plan de Respuesta:**
1. Detectar incidente
2. Contener amenaza
3. Erradicar causa
4. Recuperar sistema
5. Lecciones aprendidas

### 2. Detección

**Monitoring:**
```bash
# Setup monitoring
# Laravel Log Monitor
tail -f storage/logs/laravel.log | grep "error\|exception\|unauthorized"

# Nginx error log
tail -f /var/log/nginx/error.log

# MySQL slow query log
mysql -e "SET GLOBAL slow_query_log = 'ON';"
tail -f /var/log/mysql/slow.log
```

### 3. Respuesta a Breach

```bash
# 1. Aislar sistema
ufw deny from <ATTACKER_IP>
fail2ban-client set <jail> banip <ATTACKER_IP>

# 2. Revisar logs
grep "<ATTACKER_IP>" /var/log/nginx/access.log
grep "unauthorized\|failed" storage/logs/laravel.log

# 3. Cambiar credenciales
php artisan tinker
> User::find($compromisedUserId)->update(['password' => Hash::make('new_temp_password')]);

# 4. Revisar cambios no autorizados
git log --all --oneline --graph --decorate
mysql -e "SELECT * FROM activity_logs WHERE created_at > '2026-02-17 12:00:00' ORDER BY id DESC LIMIT 100;"

# 5. Notificar afectados
# Enviar emails a usuarios comprometidos

# 6. Documentar incidente
# Crear post-mortem document
```

### 4. Post-Incident

**Checklist:**
- [ ] Documentar timeline del incidente
- [ ] Identificar causa raíz
- [ ] Implementar medidas preventivas
- [ ] Actualizar plan de respuesta
- [ ] Training del equipo
- [ ] Notificar stakeholders si es necesario

---

## Checklist de Seguridad

### Pre-Deployment
- [ ] Todas las credenciales en .env (no en código)
- [ ] APP_DEBUG=false en producción
- [ ] APP_KEY generado
- [ ] HTTPS habilitado
- [ ] Firewall configurado
- [ ] Fail2Ban activo
- [ ] Backups automatizados
- [ ] Monitoreo configurado

### Código
- [ ] No hay SQL injection vulnerabilities
- [ ] XSS protection implementada
- [ ] CSRF tokens en formularios
- [ ] Input validation en todos endpoints
- [ ] Output escaping apropiado
- [ ] No hay secrets en código
- [ ] Dependencias sin vulnerabilidades críticas

### Infraestructura
- [ ] SSL/TLS configurado correctamente
- [ ] Headers de seguridad presentes
- [ ] CORS configurado restrictivamente
- [ ] Rate limiting en APIs
- [ ] Logs rotando correctamente
- [ ] Permisos de archivos correctos

---

## Recursos

### Tools
- [OWASP ZAP](https://www.zaproxy.org/) - Security scanner
- [Laravel Security](https://github.com/Roave/SecurityAdvisories) - Composer security
- [npm audit](https://docs.npmjs.com/cli/v8/commands/npm-audit) - NPM security
- [SSL Labs](https://www.ssllabs.com/ssltest/) - SSL testing

### Referencias
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [Vue.js Security](https://vuejs.org/guide/best-practices/security.html)

---

**Última actualización:** 2026-02-17  
**Versión:** 1.0  
**Security Team Contact:** security@yourdomain.com
