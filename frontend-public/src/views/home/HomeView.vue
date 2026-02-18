<script setup lang="ts">
import { ref } from 'vue'

const welcomeMessage = ref('Bienvenido al Portal Oficial')
</script>

<template>
  <div class="home-view">
    <section class="hero">
      <div class="container">
        <h1>{{ welcomeMessage }}</h1>
        <p class="lead">Gobierno Digital al servicio de la ciudadanía</p>
        <div class="cta-buttons">
          <router-link to="/pqrs" class="btn btn-primary btn-lg">
            Presentar PQRS
          </router-link>
          <router-link to="/transparencia" class="btn btn-outline-primary btn-lg">
            Transparencia
          </router-link>
        </div>
      </div>
    </section>
    
    <section class="features">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <div class="feature-card">
              <i class="fas fa-newspaper fa-3x" aria-hidden="true"></i>
              <h3>Noticias</h3>
              <p>Mantente informado sobre las últimas novedades de nuestra administración</p>
              <router-link to="/noticias" class="btn btn-link">Ver más</router-link>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="feature-card">
              <i class="fas fa-balance-scale fa-3x" aria-hidden="true"></i>
              <h3>Transparencia</h3>
              <p>Accede a información pública y datos abiertos</p>
              <router-link to="/transparencia" class="btn btn-link">Ver más</router-link>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="feature-card">
              <i class="fas fa-comments fa-3x" aria-hidden="true"></i>
              <h3>PQRS</h3>
              <p>Presenta tus peticiones, quejas, reclamos o sugerencias</p>
              <router-link to="/pqrs" class="btn btn-link">Ver más</router-link>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped lang="scss">
.hero {
  background: linear-gradient(135deg, $azul-institucional 0%, $azul-bandera 100%);
  color: $blanco;
  padding: $spacing-xl * 3 0;
  text-align: center;
  
  h1 {
    font-size: 2.5rem;
    margin-bottom: $spacing-md;
  }
  
  .lead {
    font-size: 1.25rem;
    margin-bottom: $spacing-xl;
  }
  
  .cta-buttons {
    display: flex;
    gap: $spacing-md;
    justify-content: center;
    flex-wrap: wrap;
  }
}

.features {
  padding: $spacing-xl * 2 0;
}

.feature-card {
  text-align: center;
  padding: $spacing-xl;
  border-radius: 8px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  
  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  
  i {
    color: $azul-institucional;
    margin-bottom: $spacing-md;
  }
  
  h3 {
    color: $texto-principal;
    margin-bottom: $spacing-md;
  }
  
  p {
    color: $texto-secundario;
    margin-bottom: $spacing-md;
  }
}

.btn {
  display: inline-block;
  padding: $spacing-sm $spacing-lg;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  
  &-primary {
    background-color: $azul-institucional;
    color: $blanco;
    
    &:hover {
      background-color: $azul-bandera;
    }
  }
  
  &-outline-primary {
    background-color: transparent;
    color: $blanco;
    border-color: $blanco;
    
    &:hover {
      background-color: $blanco;
      color: $azul-institucional;
    }
  }
  
  &-link {
    color: $azul-institucional;
    
    &:hover {
      text-decoration: underline;
    }
  }
  
  &-lg {
    padding: $spacing-md $spacing-xl;
    font-size: 1.1rem;
  }
}
</style>
