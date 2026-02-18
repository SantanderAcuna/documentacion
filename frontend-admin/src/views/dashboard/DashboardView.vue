<script setup lang="ts">
import { ref } from 'vue'
import { VaButton, VaCard } from 'vuestic-ui'

const title = ref('Dashboard Admin')
</script>

<template>
  <div class="dashboard">
    <VaCard>
      <h1>{{ title }}</h1>
      <p>Bienvenido al Panel Administrativo del CMS Gubernamental</p>
      <VaButton color="primary">
        Comenzar
      </VaButton>
    </VaCard>
  </div>
</template>

<style scoped>
.dashboard {
  padding: 2rem;
}

h1 {
  color: #004884;
  margin-bottom: 1rem;
}
</style>
