<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { VaButton, VaCard, VaInput } from 'vuestic-ui'

const router = useRouter()
const email = ref('')
const password = ref('')

const handleLogin = () => {
  // TODO: Implementar lógica de login
  console.log('Login attempt:', email.value)
  router.push({ name: 'home' })
}
</script>

<template>
  <div class="login-container">
    <VaCard class="login-card">
      <h1>CMS Admin</h1>
      <p>Panel Administrativo</p>
      
      <form @submit.prevent="handleLogin">
        <VaInput
          v-model="email"
          type="email"
          label="Correo electrónico"
          placeholder="usuario@alcaldia.gov.co"
          class="mb-3"
        />
        
        <VaInput
          v-model="password"
          type="password"
          label="Contraseña"
          placeholder="••••••••"
          class="mb-4"
        />
        
        <VaButton type="submit" color="primary" block>
          Iniciar Sesión
        </VaButton>
      </form>
    </VaCard>
  </div>
</template>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #004884 0%, #003366 100%);
}

.login-card {
  width: 100%;
  max-width: 400px;
  padding: 2rem;
}

h1 {
  color: #004884;
  margin-bottom: 0.5rem;
  text-align: center;
}

p {
  text-align: center;
  color: #6B6B6B;
  margin-bottom: 2rem;
}

.mb-3 {
  margin-bottom: 1rem;
}

.mb-4 {
  margin-bottom: 1.5rem;
}
</style>
