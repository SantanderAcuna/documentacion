<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-6 text-center">
        <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
        <h1 class="display-4 mb-3">404</h1>
        <h2 class="h4 mb-3">Página No Encontrada</h2>
        <p class="lead text-muted mb-4">
          Lo sentimos, la página que busca no existe o ha sido movida.
        </p>
        <router-link :to="{ name: 'server-list' }" class="btn btn-primary">
          <i class="fas fa-home me-2"></i>
          Ir al Inicio
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
/**
 * Vista 404 - Página no encontrada
 */
</script>
