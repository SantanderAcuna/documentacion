<script setup lang="ts">
import { ref } from 'vue'

const sections = ref([
  { id: 1, title: 'Información Mínima Obligatoria', link: '#imo' },
  { id: 2, title: 'Presupuesto y Gastos', link: '#presupuesto' },
  { id: 3, title: 'Contratación', link: '#contratacion' },
  { id: 4, title: 'Datos Abiertos', link: '/datos-abiertos' }
])
</script>

<template>
  <div class="transparencia-view">
    <div class="container">
      <h1>Transparencia y Acceso a la Información</h1>
      <p class="lead">
        Cumplimiento de la Ley 1712 de 2014 - Transparencia y Acceso a la Información Pública
      </p>
      
      <div class="sections-grid">
        <div 
          v-for="section in sections" 
          :key="section.id" 
          class="section-card"
        >
          <h2>{{ section.title }}</h2>
          <a :href="section.link" class="btn btn-primary">
            Ver información
          </a>
        </div>
      </div>
      
      <section class="compliance-info">
        <h2>Cumplimiento Normativo</h2>
        <ul>
          <li>Ley 1712 de 2014 - Transparencia y Acceso a la Información</li>
          <li>Índice de Transparencia y Acceso a la Información (ITA)</li>
          <li>Formulario Único de Reporte de Avances de Gestión (FURAG)</li>
        </ul>
      </section>
    </div>
  </div>
</template>

<style scoped lang="scss">
.transparencia-view {
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

.sections-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: $spacing-lg;
  margin-bottom: $spacing-xl * 2;
}

.section-card {
  padding: $spacing-xl;
  background-color: $gris-claro;
  border-radius: 8px;
  text-align: center;
  
  h2 {
    font-size: 1.3rem;
    margin-bottom: $spacing-md;
    color: $texto-principal;
  }
}

.compliance-info {
  background-color: $gris-claro;
  padding: $spacing-xl;
  border-radius: 8px;
  
  h2 {
    color: $azul-institucional;
    margin-bottom: $spacing-md;
  }
  
  ul {
    list-style-position: inside;
    
    li {
      margin-bottom: $spacing-sm;
      color: $texto-principal;
    }
  }
}

.btn-primary {
  background-color: $azul-institucional;
  color: $blanco;
  padding: $spacing-sm $spacing-lg;
  text-decoration: none;
  border-radius: 4px;
  display: inline-block;
  
  &:hover {
    background-color: $azul-bandera;
  }
}
</style>
