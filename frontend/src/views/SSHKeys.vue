<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-6 text-center">
        <i class="fas fa-key fa-5x text-primary mb-4"></i>
        <h1 class="h2 mb-3">Llaves SSH</h1>
        <p class="lead text-muted mb-4">
          Gestione sus llaves SSH para autenticación segura en los servidores.
        </p>
        <div class="alert alert-info" role="alert">
          <i class="fas fa-info-circle me-2"></i>
          <strong>Próximamente:</strong> Esta funcionalidad estará disponible en una próxima versión.
        </div>
        <router-link :to="{ name: 'server-list' }" class="btn btn-primary">
          <i class="fas fa-arrow-left me-2"></i>
          Volver a Servidores
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
/**
 * Vista placeholder para Llaves SSH
 * TODO: Implementar gestión de llaves SSH
 */
</script>
