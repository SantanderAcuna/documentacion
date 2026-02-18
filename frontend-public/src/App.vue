<script setup lang="ts">
import { RouterView } from 'vue-router'
import HeaderComponent from '@/components/layout/HeaderComponent.vue'
import FooterComponent from '@/components/layout/FooterComponent.vue'
</script>

<template>
  <div id="app">
    <a href="#main-content" class="sr-only">Saltar al contenido principal</a>
    <HeaderComponent />
    <main id="main-content" role="main">
      <RouterView />
    </main>
    <FooterComponent />
  </div>
</template>

<style scoped>
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}
</style>
