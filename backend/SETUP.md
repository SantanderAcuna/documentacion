# Backend Setup Guide

## Prerequisites

- PHP 8.3+
- Composer
- MySQL 8.0+
- Redis (optional, for caching)

## Quick Setup (Development)

### 1. Install Dependencies

```bash
cd backend
composer install
```

### 2. Configure Environment

```bash
cp .env.example .env
```

Edit `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cms_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Migrations

```bash
# Create all tables
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=RolePermissionSeeder
```

### 5. Create Storage Link

```bash
php artisan storage:link
```

### 6. Create Initial Admin User

```bash
php artisan tinker
```

In Tinker console:

```php
$admin = App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@alcaldia.gov.co',
    'password' => bcrypt('Admin2026!')
]);

$admin->assignRole('super-admin');

// Create an editor
$editor = App\Models\User::create([
    'name' => 'Editor',
    'email' => 'editor@alcaldia.gov.co',
    'password' => bcrypt('Editor2026!')
]);

$editor->assignRole('editor');

// Create PQRS attendant
$pqrs = App\Models\User::create([
    'name' => 'Atención PQRS',
    'email' => 'pqrs@alcaldia.gov.co',
    'password' => bcrypt('Pqrs2026!')
]);

$pqrs->assignRole('atencion-pqrs');

exit
```

### 7. Start Development Server

```bash
php artisan serve
```

API will be available at: `http://localhost:8000/api/v1`

---

## Testing the API

### Option 1: Using cURL

**Test Authentication:**

```bash
# Register a new user
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Login
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@alcaldia.gov.co",
    "password": "Admin2026!"
  }'
```

Copy the `token` from the response.

**Test Content Creation:**

```bash
curl -X POST http://localhost:8000/api/v1/contents \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "title": "Primera Noticia",
    "content": "Este es el contenido de la primera noticia del CMS.",
    "excerpt": "Resumen de la noticia",
    "status": "published",
    "is_featured": true
  }'
```

**Test PQRS Creation (Public):**

```bash
curl -X POST http://localhost:8000/api/v1/pqrs \
  -H "Content-Type: application/json" \
  -d '{
    "tipo": "peticion",
    "nombre": "Juan Ciudadano",
    "email": "juan@example.com",
    "telefono": "3001234567",
    "asunto": "Solicitud de información",
    "mensaje": "Requiero información sobre los trámites disponibles."
  }'
```

Note the `folio` in the response, then track it:

```bash
curl http://localhost:8000/api/v1/pqrs/PQRS-2026-000001
```

### Option 2: Using Postman

1. Import the API endpoints from `API_DOCUMENTATION.md`
2. Set up environment variables:
   - `base_url`: `http://localhost:8000/api/v1`
   - `token`: (get from login response)
3. Create requests for each endpoint

---

## Seeding Sample Data

### Create Sample Categories

```bash
php artisan tinker
```

```php
$categories = [
    ['name' => 'Noticias', 'slug' => 'noticias', 'description' => 'Noticias de la alcaldía'],
    ['name' => 'Transparencia', 'slug' => 'transparencia', 'description' => 'Información de transparencia'],
    ['name' => 'Trámites', 'slug' => 'tramites', 'description' => 'Trámites y servicios'],
];

foreach ($categories as $cat) {
    App\Models\Category::create($cat);
}
```

### Create Sample Tags

```php
$tags = ['Importante', 'Urgente', 'Transparencia', 'Normativa'];

foreach ($tags as $tag) {
    App\Models\Tag::create([
        'name' => $tag,
        'slug' => Str::slug($tag)
    ]);
}
```

### Create Sample Content

```php
$admin = App\Models\User::where('email', 'admin@alcaldia.gov.co')->first();

App\Models\Content::create([
    'title' => 'Bienvenidos al Portal de Transparencia',
    'slug' => 'bienvenidos-portal-transparencia',
    'excerpt' => 'La alcaldía se compromete con la transparencia y el acceso a la información.',
    'content' => '<p>En cumplimiento de la Ley 1712 de 2014...</p>',
    'status' => 'published',
    'published_at' => now(),
    'author_id' => $admin->id,
    'category_id' => 2, // Transparencia
    'is_featured' => true,
]);

exit
```

---

## Running Tests

### PHPUnit

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter AuthenticationTest

# Run with coverage
php artisan test --coverage
```

---

## Common Issues

### Issue: "Class 'Redis' not found"

**Solution:** Install PHP Redis extension or use array/database driver:

```env
CACHE_STORE=database
SESSION_DRIVER=database
```

### Issue: Storage link not working

**Solution:**

```bash
# Remove existing link
rm public/storage

# Recreate
php artisan storage:link
```

### Issue: Permission denied on storage

**Solution:**

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## Production Deployment

### 1. Optimize Application

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Set Environment Variables

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.gov.co

# Use strong random key
APP_KEY=base64:...

# Production database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-secure-password

# Redis for production (recommended)
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 3. Security Checklist

- [ ] `.env` file is not in git
- [ ] `APP_DEBUG=false` in production
- [ ] HTTPS configured (SSL certificate)
- [ ] Database credentials are secure
- [ ] File permissions are correct (755 for directories, 644 for files)
- [ ] `storage/` and `bootstrap/cache/` are writable
- [ ] CORS is properly configured
- [ ] Rate limiting is enabled
- [ ] Backups are configured

---

## Maintenance

### Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Backup

```bash
mysqldump -u username -p cms_db > backup-$(date +%Y%m%d).sql
```

### View Logs

```bash
tail -f storage/logs/laravel.log
```

---

## API Rate Limiting

Default rate limits (configurable in `app/Http/Kernel.php`):

- **Global:** 60 requests per minute
- **Login:** 5 attempts per 15 minutes
- **PQRS Creation:** 10 per hour (recommended)

---

## Support

For issues or questions:
- Check `API_DOCUMENTATION.md` for endpoint details
- Review Laravel logs: `storage/logs/laravel.log`
- Check database migrations: `php artisan migrate:status`

---

**Backend API is ready for integration!** 🚀
