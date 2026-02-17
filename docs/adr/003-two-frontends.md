# ADR-003: Dos Frontends Separados (Admin + Público)

**Estado:** Aceptado  
**Fecha:** 2026-02-17  
**Decisores:** Equipo de Arquitectura, UX Lead  
**Contexto:** Fase 1 - Constitución del Proyecto

---

## Contexto y Problema

El CMS Gubernamental requiere dos interfaces distintas:
1. **Panel Administrativo:** Para editores y administradores
2. **Sitio Público:** Para ciudadanos y visitantes

Debemos decidir si:
- Usar un solo frontend con diferentes rutas
- Usar dos frontends completamente separados

---

## Factores de Decisión

- **Diseño:** Admin requiere UI compleja vs. Público requiere diseño GOV.CO
- **Performance:** Sitio público debe ser ultra-rápido
- **Seguridad:** Separación de código admin/público
- **Mantenibilidad:** Equipos pueden trabajar independientemente
- **Escalabilidad:** Deploy independiente
- **Cumplimiento:** WCAG 2.1 AA en sitio público

---

## Opciones Consideradas

### Opción 1: Dos Frontends Separados ✅
**Descripción:** `frontend-admin/` y `frontend-public/` separados

**Frontend Admin:**
- Vue 3 + Vuestic UI
- Componentes complejos (tables, charts, forms)
- No requiere optimización extrema
- Autenticación obligatoria

**Frontend Público:**
- Vue 3 + Bootstrap 5
- Diseño GOV.CO oficial
- Optimizado para performance
- Accesibilidad WCAG 2.1 AA
- Mayormente estático

**Pros:**
- ✅ UI frameworks diferentes (Vuestic vs Bootstrap)
- ✅ Bundle sizes optimizados
- ✅ Deploy independiente
- ✅ Equipos trabajan sin conflictos
- ✅ Seguridad: código admin no expuesto en público
- ✅ Performance: sitio público ultra-ligero

**Contras:**
- ⚠️ Duplicación de código compartido
- ⚠️ Dos configuraciones de build

### Opción 2: Un Solo Frontend con Rutas
**Descripción:** `frontend/` con `/admin` y `/` rutas

**Pros:**
- ✅ Código compartido fácilmente
- ✅ Una sola configuración
- ✅ DRY (Don't Repeat Yourself)

**Contras:**
- ❌ Bundle único muy grande
- ❌ Código admin expuesto en público
- ❌ Difícil usar dos UI frameworks
- ❌ Compromisos en diseño
- ❌ Deploy acoplado

### Opción 3: Microfrontends
**Descripción:** Module Federation o similar

**Pros:**
- ✅ Máxima separación
- ✅ Deploy ultra-independiente

**Contras:**
- ❌ Complejidad técnica muy alta
- ❌ Overkill para nuestro caso
- ❌ Más difícil de mantener

---

## Decisión

**Elegimos Opción 1: Dos Frontends Separados**

### Estructura
```
cms-gubernamental/
├── frontend-admin/           # Panel Administrativo
│   ├── src/
│   │   ├── components/
│   │   ├── views/
│   │   ├── stores/          # Pinia
│   │   ├── composables/
│   │   └── router/
│   ├── package.json
│   └── vite.config.ts
│
└── frontend-public/          # Sitio Público
    ├── src/
    │   ├── components/
    │   ├── views/
    │   ├── stores/          # Pinia
    │   ├── composables/
    │   └── router/
    ├── package.json
    └── vite.config.ts
```

### Características por Frontend

#### Frontend Admin (`frontend-admin/`)
- **UI Framework:** Vuestic UI
- **Purpose:** Panel administrativo completo
- **Users:** Editores, Administradores, Auditores
- **Features:**
  - Dashboard con métricas
  - CRUD de contenidos
  - Gestión de usuarios y roles
  - Editor WYSIWYG
  - Tablas con filtros y paginación
  - Gráficas y reportes
- **Performance:** Moderado (usuarios autenticados)
- **Accesibilidad:** WCAG 2.1 A (mínimo)

#### Frontend Público (`frontend-public/`)
- **UI Framework:** Bootstrap 5 + SASS
- **Design System:** GOV.CO (MinTIC)
- **Purpose:** Sitio web público
- **Users:** Ciudadanos, Visitantes
- **Features:**
  - Noticias y contenidos institucionales
  - Transparencia activa
  - PQRS
  - Buscador
  - Datos abiertos
- **Performance:** Crítico (Google PageSpeed >90)
- **Accesibilidad:** WCAG 2.1 AA (obligatorio)

---

## Consecuencias

### Positivas
- ✅ **Vuestic UI en admin:** Componentes profesionales out-of-the-box
- ✅ **GOV.CO en público:** Cumplimiento diseño gubernamental
- ✅ **Bundle optimizado:** Público ~200KB, Admin ~800KB
- ✅ **Seguridad:** Código admin no en público
- ✅ **Deploy:** CDN para público, servidor para admin
- ✅ **Performance:** Público optimizado para SEO
- ✅ **Equipos:** Desarrollo paralelo sin conflictos

### Negativas
- ⚠️ **Código compartido:** Crear package `@cms/shared` si necesario
- ⚠️ **Duplicación:** Axios config, composables básicos
- ⚠️ **Mantenimiento:** Dos package.json, dos builds

### Neutrales
- ℹ️ **CI/CD:** Build ambos frontends en paralelo
- ℹ️ **Hosting:** Admin en subdomain admin.alcaldia.gov.co

---

## Código Compartido

Para evitar duplicación crítica, crear package compartido:

```
shared/
├── composables/
│   ├── useApi.ts
│   ├── useAuth.ts
│   └── useToast.ts
├── types/
│   ├── User.ts
│   ├── Content.ts
│   └── Response.ts
└── utils/
    ├── formatters.ts
    └── validators.ts
```

Usar npm workspaces:
```json
// package.json (root)
{
  "workspaces": [
    "frontend-admin",
    "frontend-public",
    "shared"
  ]
}
```

---

## Dominios y URLs

### Desarrollo
- Admin: http://localhost:3000
- Público: http://localhost:3001
- API: http://localhost:8000

### Producción
- Admin: https://admin.alcaldia.gov.co
- Público: https://www.alcaldia.gov.co
- API: https://api.alcaldia.gov.co

---

## Build y Deploy

### Build
```bash
# Admin
cd frontend-admin
npm run build  # Output: dist/

# Público
cd frontend-public
npm run build  # Output: dist/
```

### Deploy
```yaml
# .github/workflows/deploy.yml
jobs:
  deploy-admin:
    runs-on: ubuntu-latest
    steps:
      - name: Build Admin
        run: cd frontend-admin && npm run build
      - name: Deploy to Server
        run: rsync -avz dist/ server:/var/www/admin/

  deploy-public:
    runs-on: ubuntu-latest
    steps:
      - name: Build Public
        run: cd frontend-public && npm run build
      - name: Deploy to CDN
        run: aws s3 sync dist/ s3://bucket/
```

---

## Validación

### Criterios de Éxito
- [ ] Admin usa Vuestic UI exitosamente
- [ ] Público cumple diseño GOV.CO
- [ ] Bundle público <300KB gzipped
- [ ] Sitio público WCAG 2.1 AA (Axe)
- [ ] PageSpeed público >90
- [ ] No código admin en bundle público

### Tests
```bash
# Verificar bundle sizes
cd frontend-public
npm run build
ls -lh dist/assets/*.js  # Debe ser <300KB

# Verificar accesibilidad
npm run test:a11y  # Axe tests

# Verificar no hay código admin
grep -r "Vuestic" dist/  # Debe estar vacío
```

---

## Referencias
- Vuestic UI: https://vuestic.dev/
- GOV.CO Design: https://www.gov.co/
- WCAG 2.1: https://www.w3.org/WAI/WCAG21/quickref/
- npm workspaces: https://docs.npmjs.com/cli/v7/using-npm/workspaces

---

**Firmado:** Equipo de Arquitectura, UX Lead  
**Próxima revisión:** 2026-08-17 (6 meses)
