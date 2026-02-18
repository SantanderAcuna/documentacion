<script setup lang="ts">
import { ref } from 'vue'

const formData = ref({
  tipoSolicitud: '',
  nombre: '',
  email: '',
  telefono: '',
  asunto: '',
  mensaje: ''
})

const handleSubmit = () => {
  console.log('Formulario PQRS enviado:', formData.value)
  alert('Su solicitud ha sido enviada correctamente. En breve recibirá un correo de confirmación.')
}
</script>

<template>
  <div class="pqrs-view">
    <div class="container">
      <h1>PQRS - Peticiones, Quejas, Reclamos y Sugerencias</h1>
      <p class="lead">
        Ejerce tu derecho de petición. Responderemos en un máximo de 15 días hábiles.
      </p>
      
      <form @submit.prevent="handleSubmit" class="pqrs-form">
        <div class="form-group">
          <label for="tipoSolicitud">
            Tipo de solicitud
            <span class="required" aria-label="campo obligatorio">*</span>
          </label>
          <select 
            id="tipoSolicitud"
            v-model="formData.tipoSolicitud"
            required
            aria-required="true"
          >
            <option value="">Seleccione una opción</option>
            <option value="peticion">Petición</option>
            <option value="queja">Queja</option>
            <option value="reclamo">Reclamo</option>
            <option value="sugerencia">Sugerencia</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="nombre">
            Nombre completo
            <span class="required" aria-label="campo obligatorio">*</span>
          </label>
          <input 
            id="nombre"
            v-model="formData.nombre"
            type="text"
            required
            aria-required="true"
          />
        </div>
        
        <div class="form-group">
          <label for="email">
            Correo electrónico
            <span class="required" aria-label="campo obligatorio">*</span>
          </label>
          <input 
            id="email"
            v-model="formData.email"
            type="email"
            required
            aria-required="true"
          />
        </div>
        
        <div class="form-group">
          <label for="telefono">Teléfono</label>
          <input 
            id="telefono"
            v-model="formData.telefono"
            type="tel"
          />
        </div>
        
        <div class="form-group">
          <label for="asunto">
            Asunto
            <span class="required" aria-label="campo obligatorio">*</span>
          </label>
          <input 
            id="asunto"
            v-model="formData.asunto"
            type="text"
            required
            aria-required="true"
          />
        </div>
        
        <div class="form-group">
          <label for="mensaje">
            Mensaje
            <span class="required" aria-label="campo obligatorio">*</span>
          </label>
          <textarea 
            id="mensaje"
            v-model="formData.mensaje"
            rows="6"
            required
            aria-required="true"
          ></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">
          Enviar solicitud
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped lang="scss">
.pqrs-view {
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

.pqrs-form {
  max-width: 600px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: $spacing-lg;
  
  label {
    display: block;
    margin-bottom: $spacing-sm;
    font-weight: 500;
    color: $texto-principal;
  }
  
  .required {
    color: $error;
  }
  
  input,
  select,
  textarea {
    width: 100%;
    padding: $spacing-sm $spacing-md;
    border: 1px solid $gris-medio;
    border-radius: 4px;
    font-size: $font-size-base;
    
    &:focus {
      outline: 2px solid $azul-institucional;
      outline-offset: 2px;
      border-color: $azul-institucional;
    }
  }
  
  textarea {
    resize: vertical;
  }
}

.btn-primary {
  background-color: $azul-institucional;
  color: $blanco;
  padding: $spacing-md $spacing-xl;
  border: none;
  border-radius: 4px;
  font-size: 1.1rem;
  font-weight: 500;
  cursor: pointer;
  width: 100%;
  
  &:hover {
    background-color: $azul-bandera;
  }
  
  &:focus {
    outline: 2px solid $azul-institucional;
    outline-offset: 2px;
  }
}
</style>
