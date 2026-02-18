<script setup lang="ts">
import { ref } from 'vue'

const noticias = ref([
  {
    id: 1,
    title: 'Inauguración de nueva infraestructura',
    excerpt: 'Se inaugura nueva infraestructura para mejorar la calidad de vida de los ciudadanos',
    date: '2026-02-15'
  },
  {
    id: 2,
    title: 'Programa de apoyo a emprendedores',
    excerpt: 'Lanzamiento del programa municipal de apoyo a emprendedores locales',
    date: '2026-02-10'
  }
])
</script>

<template>
  <div class="noticias-view">
    <div class="container">
      <h1>Noticias</h1>
      <p class="lead">Mantente informado sobre las últimas novedades</p>
      
      <div class="noticias-grid">
        <article 
          v-for="noticia in noticias" 
          :key="noticia.id" 
          class="noticia-card"
        >
          <h2>{{ noticia.title }}</h2>
          <p class="date">{{ noticia.date }}</p>
          <p>{{ noticia.excerpt }}</p>
          <a :href="`/noticias/${noticia.id}`" class="read-more">
            Leer más
          </a>
        </article>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.noticias-view {
  padding: $spacing-xl 0;
  
  h1 {
    color: $azul-institucional;
    margin-bottom: $spacing-sm;
  }
  
  .lead {
    color: $texto-secundario;
    margin-bottom: $spacing-xl;
  }
}

.noticias-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: $spacing-lg;
}

.noticia-card {
  padding: $spacing-lg;
  border: 1px solid $gris-claro;
  border-radius: 8px;
  
  h2 {
    font-size: 1.5rem;
    margin-bottom: $spacing-sm;
  }
  
  .date {
    color: $texto-secundario;
    font-size: 0.9rem;
    margin-bottom: $spacing-md;
  }
  
  .read-more {
    color: $azul-institucional;
    font-weight: 500;
  }
}
</style>
