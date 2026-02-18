# Architecture Decision Records (ADR)

Este directorio contiene los registros de decisiones arquitectónicas del proyecto CMS Gubernamental.

## ¿Qué es un ADR?

Un Architecture Decision Record (ADR) es un documento que captura una decisión arquitectónica importante junto con su contexto y consecuencias.

## Lista de ADRs

| ID | Título | Estado | Fecha |
|----|--------|--------|-------|
| [001](001-monorepo-docker.md) | Monorepo con Docker para Desarrollo | Aceptado | 2026-02-17 |
| [002](002-sanctum-authentication.md) | Laravel Sanctum para Autenticación | Aceptado | 2026-02-17 |
| [003](003-two-frontends.md) | Dos Frontends Separados (Admin + Público) | Aceptado | 2026-02-17 |

## Plantilla para Nuevos ADRs

```markdown
# ADR-XXX: Título Descriptivo

**Estado:** Propuesto | Aceptado | Rechazado | Obsoleto  
**Fecha:** YYYY-MM-DD  
**Decisores:** Nombres/Roles  
**Contexto:** Fase del proyecto

---

## Contexto y Problema

Describir el contexto técnico y el problema a resolver.

---

## Factores de Decisión

- Factor 1
- Factor 2
- Factor 3

---

## Opciones Consideradas

### Opción 1: Nombre
**Pros:**
- Pro 1
- Pro 2

**Contras:**
- Contra 1
- Contra 2

### Opción 2: Nombre
...

---

## Decisión

Describir la opción elegida y por qué.

---

## Consecuencias

### Positivas
- ✅ Consecuencia 1
- ✅ Consecuencia 2

### Negativas
- ⚠️ Consecuencia 1
- ⚠️ Consecuencia 2

### Neutrales
- ℹ️ Consecuencia 1

---

## Validación

### Criterios de Éxito
- [ ] Criterio 1
- [ ] Criterio 2

---

## Referencias
- Enlace 1
- Enlace 2

---

**Firmado:** Nombres  
**Próxima revisión:** YYYY-MM-DD
```

## Proceso de Creación

1. Copiar la plantilla
2. Asignar siguiente número (verificar lista)
3. Llenar todas las secciones
4. Solicitar revisión al equipo
5. Obtener aprobación de decisores
6. Actualizar este README

## Política de ADRs

- **Obligatorio para:**
  - Cambios en stack tecnológico
  - Cambios en arquitectura base
  - Decisiones de seguridad
  - Patrones arquitectónicos nuevos

- **Opcional para:**
  - Decisiones tácticas pequeñas
  - Decisiones reversibles fácilmente

- **Revisión:**
  - Cada ADR se revisa anualmente
  - Se marca como Obsoleto si ya no aplica
  - Se crea nuevo ADR para reemplazar

---

**Última actualización:** 2026-02-17
