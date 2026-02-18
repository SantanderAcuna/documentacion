<script setup lang="ts">
import { ref } from 'vue'

const menuOpen = ref(false)

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value
}
</script>

<template>
  <header class="govco-header" role="banner">
    <div class="header-top">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <img src="/logo-govco.svg" alt="GOV.CO - Portal del Estado Colombiano" class="logo" />
          </div>
          <div class="col-md-6 text-end">
            <a href="#" class="btn-accesibilidad" aria-label="Opciones de accesibilidad">
              <i class="fas fa-universal-access" aria-hidden="true"></i>
              Accesibilidad
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <nav class="header-nav" role="navigation" aria-label="Navegación principal">
      <div class="container">
        <button 
          class="menu-toggle" 
          @click="toggleMenu"
          :aria-expanded="menuOpen"
          aria-label="Menú de navegación"
        >
          <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        
        <ul :class="['nav-menu', { 'active': menuOpen }]">
          <li><router-link to="/">Inicio</router-link></li>
          <li><router-link to="/noticias">Noticias</router-link></li>
          <li><router-link to="/transparencia">Transparencia</router-link></li>
          <li><router-link to="/pqrs">PQRS</router-link></li>
        </ul>
      </div>
    </nav>
  </header>
</template>

<style scoped lang="scss">
.govco-header {
  background-color: $blanco;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-top {
  background-color: $azul-institucional;
  color: $blanco;
  padding: $spacing-sm 0;
}

.logo {
  height: 40px;
}

.btn-accesibilidad {
  color: $blanco;
  text-decoration: none;
  font-size: 0.9rem;
  
  i {
    margin-right: $spacing-xs;
  }
}

.header-nav {
  padding: $spacing-md 0;
}

.menu-toggle {
  display: none;
  background: none;
  border: none;
  font-size: 1.5rem;
  color: $azul-institucional;
  
  @media (max-width: $breakpoint-md) {
    display: block;
  }
}

.nav-menu {
  list-style: none;
  display: flex;
  gap: $spacing-lg;
  margin: 0;
  padding: 0;
  
  @media (max-width: $breakpoint-md) {
    display: none;
    flex-direction: column;
    
    &.active {
      display: flex;
    }
  }
  
  a {
    color: $texto-principal;
    text-decoration: none;
    font-weight: 500;
    
    &:hover,
    &.router-link-active {
      color: $azul-institucional;
      text-decoration: underline;
    }
  }
}
</style>
