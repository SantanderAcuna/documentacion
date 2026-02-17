<template>
  <div id="app">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <router-link class="navbar-brand" :to="{ name: 'server-list' }">
          <i class="fas fa-server me-2"></i>
          Portal VPS
        </router-link>
        
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link
                class="nav-link"
                :to="{ name: 'server-list' }"
                active-class="active"
              >
                <i class="fas fa-server me-1"></i>
                Servidores
              </router-link>
            </li>
            <li class="nav-item">
              <router-link
                class="nav-link"
                :to="{ name: 'ssh-keys' }"
                active-class="active"
              >
                <i class="fas fa-key me-1"></i>
                Llaves SSH
              </router-link>
            </li>
          </ul>
          
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                id="userDropdown"
                role="button"
                data-bs-toggle="dropdown"
              >
                <i class="fas fa-user-circle me-1"></i>
                Admin
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="#">
                    <i class="fas fa-user me-2"></i>
                    Mi Perfil
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="#">
                    <i class="fas fa-cog me-2"></i>
                    Configuración
                  </a>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <a class="dropdown-item" href="#">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Cerrar Sesión
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Contenido principal -->
    <main>
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-6">
            <span class="text-muted">
              © 2026 Portal VPS - Gestión Profesional de Servidores
            </span>
          </div>
          <div class="col-md-6 text-end">
            <span class="text-muted">
              Versión 1.0.0
            </span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
/**
 * Componente principal de la aplicación
 * 
 * Este componente contiene el layout principal con:
 * - Navbar de navegación
 * - Área de contenido con router-view
 * - Footer
 */
</script>

<style scoped>
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
  background-color: #f8f9fa;
}

.navbar-brand {
  font-weight: 600;
  font-size: 1.25rem;
}

.footer {
  border-top: 1px solid #dee2e6;
}

/* Transición de páginas */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
