<template>
  <div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'server-list' }">
            <i class="fas fa-server me-1"></i>
            Servidores
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          {{ isEditMode ? 'Editar Servidor' : 'Nuevo Servidor' }}
        </li>
      </ol>
    </nav>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
              <i :class="isEditMode ? 'fas fa-edit' : 'fas fa-plus'" class="me-2"></i>
              {{ isEditMode ? 'Editar Servidor VPS' : 'Nuevo Servidor VPS' }}
            </h4>
          </div>

          <div class="card-body">
            <!-- Mensaje de carga al obtener datos -->
            <div v-if="isLoadingServer && isEditMode" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
              <p class="mt-3 text-muted">Cargando datos del servidor...</p>
            </div>

            <!-- Mensaje de error al cargar -->
            <div v-else-if="loadError && isEditMode" class="alert alert-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>
              Error al cargar el servidor: {{ loadError.message }}
            </div>

            <!-- Formulario -->
            <form v-else @submit.prevent="handleSubmit" novalidate>
              <!-- Nombre del servidor -->
              <div class="mb-3">
                <label for="name" class="form-label">
                  Nombre del Servidor
                  <span class="text-danger">*</span>
                </label>
                <input
                  id="name"
                  v-model="formData.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  placeholder="Ej: Servidor Producción 01"
                  @blur="validateField('name')"
                />
                <div v-if="errors.name" class="invalid-feedback">
                  {{ errors.name }}
                </div>
                <small class="form-text text-muted">
                  Nombre descriptivo para identificar el servidor
                </small>
              </div>

              <!-- IP Address / Hostname -->
              <div class="row">
                <div class="col-md-8">
                  <div class="mb-3">
                    <label for="ipAddress" class="form-label">
                      Dirección IP / Hostname
                      <span class="text-danger">*</span>
                    </label>
                    <input
                      id="ipAddress"
                      v-model="formData.ipAddress"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': errors.ipAddress }"
                      placeholder="Ej: 192.168.1.100 o servidor.ejemplo.com"
                      @blur="validateField('ipAddress')"
                    />
                    <div v-if="errors.ipAddress" class="invalid-feedback">
                      {{ errors.ipAddress }}
                    </div>
                  </div>
                </div>

                <!-- Puerto SSH -->
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="port" class="form-label">
                      Puerto SSH
                      <span class="text-danger">*</span>
                    </label>
                    <input
                      id="port"
                      v-model.number="formData.port"
                      type="number"
                      class="form-control"
                      :class="{ 'is-invalid': errors.port }"
                      placeholder="22"
                      min="1"
                      max="65535"
                      @blur="validateField('port')"
                    />
                    <div v-if="errors.port" class="invalid-feedback">
                      {{ errors.port }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Usuario -->
              <div class="mb-3">
                <label for="username" class="form-label">
                  Usuario SSH
                  <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="fas fa-user"></i>
                  </span>
                  <input
                    id="username"
                    v-model="formData.username"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.username }"
                    placeholder="root"
                    @blur="validateField('username')"
                  />
                  <div v-if="errors.username" class="invalid-feedback">
                    {{ errors.username }}
                  </div>
                </div>
                <small class="form-text text-muted">
                  Usuario para conectarse al servidor vía SSH
                </small>
              </div>

              <!-- Método de autenticación -->
              <div class="mb-3">
                <label class="form-label">
                  Método de Autenticación
                  <span class="text-danger">*</span>
                </label>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-check form-check-inline">
                      <input
                        id="authPassword"
                        v-model="formData.authMethod"
                        class="form-check-input"
                        type="radio"
                        value="password"
                        @change="validateField('authMethod')"
                      />
                      <label class="form-check-label" for="authPassword">
                        <i class="fas fa-key me-1"></i>
                        Contraseña
                      </label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check form-check-inline">
                      <input
                        id="authSshKey"
                        v-model="formData.authMethod"
                        class="form-check-input"
                        type="radio"
                        value="ssh_key"
                        @change="validateField('authMethod')"
                      />
                      <label class="form-check-label" for="authSshKey">
                        <i class="fas fa-lock me-1"></i>
                        Llave SSH
                      </label>
                    </div>
                  </div>
                </div>
                <div v-if="errors.authMethod" class="text-danger small mt-1">
                  {{ errors.authMethod }}
                </div>
              </div>

              <!-- Llave SSH (solo si authMethod es ssh_key) -->
              <div v-if="formData.authMethod === 'ssh_key'" class="mb-3">
                <label for="sshKeyId" class="form-label">
                  Llave SSH
                  <span class="text-danger">*</span>
                </label>
                <select
                  id="sshKeyId"
                  v-model="formData.sshKeyId"
                  class="form-select"
                  :class="{ 'is-invalid': errors.sshKeyId }"
                  @change="validateField('sshKeyId')"
                >
                  <option :value="null">Seleccione una llave SSH</option>
                  <option value="ssh-key-1">SSH Key 01 (fingerprint: abc123)</option>
                  <option value="ssh-key-2">SSH Key 02 (fingerprint: def456)</option>
                  <!-- TODO: Cargar llaves SSH desde la API -->
                </select>
                <div v-if="errors.sshKeyId" class="invalid-feedback">
                  {{ errors.sshKeyId }}
                </div>
                <small class="form-text text-muted">
                  <router-link :to="{ name: 'ssh-keys' }">
                    <i class="fas fa-plus me-1"></i>
                    Crear nueva llave SSH
                  </router-link>
                </small>
              </div>

              <!-- Tags -->
              <div class="mb-3">
                <label for="tags" class="form-label">
                  Etiquetas (Tags)
                </label>
                <input
                  id="tags"
                  v-model="tagsInput"
                  type="text"
                  class="form-control"
                  placeholder="Ej: producción, web, backend (separados por coma)"
                  @blur="parseTags"
                />
                <small class="form-text text-muted">
                  Separe las etiquetas con comas. Ej: producción, web, backend
                </small>
                <div v-if="formData.tags && formData.tags.length > 0" class="mt-2">
                  <span
                    v-for="(tag, index) in formData.tags"
                    :key="index"
                    class="badge bg-secondary me-1"
                  >
                    {{ tag }}
                    <i
                      class="fas fa-times ms-1"
                      style="cursor: pointer"
                      @click="removeTag(index)"
                    ></i>
                  </span>
                </div>
              </div>

              <!-- Mensaje de error general -->
              <div v-if="submitError" class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ submitError }}
              </div>

              <!-- Botones de acción -->
              <div class="d-flex justify-content-between align-items-center">
                <button
                  type="button"
                  class="btn btn-outline-secondary"
                  @click="handleCancel"
                  :disabled="isSubmitting"
                >
                  <i class="fas fa-times me-2"></i>
                  Cancelar
                </button>

                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="isSubmitting || !isFormValid"
                >
                  <i
                    :class="isSubmitting ? 'fas fa-spinner fa-spin me-2' : 'fas fa-save me-2'"
                  ></i>
                  {{ isSubmitting ? 'Guardando...' : (isEditMode ? 'Actualizar' : 'Crear') }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Card de ayuda -->
        <div class="card mt-4 border-info">
          <div class="card-header bg-info text-white">
            <h6 class="mb-0">
              <i class="fas fa-info-circle me-2"></i>
              Información Importante
            </h6>
          </div>
          <div class="card-body">
            <ul class="mb-0">
              <li>
                <strong>Nombre:</strong> Use un nombre descriptivo que identifique fácilmente el servidor.
              </li>
              <li>
                <strong>IP/Hostname:</strong> Puede usar una dirección IP (192.168.1.100) o un hostname (servidor.ejemplo.com).
              </li>
              <li>
                <strong>Puerto SSH:</strong> El puerto por defecto es 22, pero puede cambiarlo si su servidor usa otro.
              </li>
              <li>
                <strong>Autenticación:</strong> Se recomienda usar llaves SSH para mayor seguridad.
              </li>
              <li>
                <strong>Etiquetas:</strong> Use etiquetas para organizar y filtrar sus servidores.
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { useToast } from 'vue-toastification'
import { vpsServerService } from '@/services/vpsServerService'
import { createVPSServerSchema, updateVPSServerSchema } from '@/types/validations'
import type { CreateVPSServerDto, UpdateVPSServerDto } from '@/types/entities'
import type { ValidationError } from 'yup'

/**
 * Composables
 */
const router = useRouter()
const route = useRoute()
const queryClient = useQueryClient()
const toast = useToast()

/**
 * Determinar si estamos en modo edición
 */
const serverId = computed(() => route.params.id as string | undefined)
const isEditMode = computed(() => !!serverId.value)

/**
 * Estado del formulario
 */
const formData = ref<CreateVPSServerDto | UpdateVPSServerDto>({
  name: '',
  ipAddress: '',
  port: 22,
  username: '',
  authMethod: 'password',
  sshKeyId: null,
  tags: [],
})

const tagsInput = ref('')
const errors = ref<Record<string, string>>({})
const submitError = ref('')
const isSubmitting = ref(false)

/**
 * Query para cargar datos del servidor en modo edición
 */
const {
  data: serverData,
  isLoading: isLoadingServer,
  error: loadError,
} = useQuery({
  queryKey: ['server', serverId],
  queryFn: () => vpsServerService.getById(serverId.value!),
  enabled: isEditMode,
})

/**
 * Cargar datos del servidor en el formulario cuando se obtienen
 */
watch(serverData, (data) => {
  if (data?.data) {
    const server = data.data
    formData.value = {
      name: server.name,
      ipAddress: server.ipAddress,
      port: server.port,
      username: server.username,
      authMethod: server.authMethod,
      sshKeyId: server.sshKeyId,
      tags: server.tags || [],
    }
    tagsInput.value = server.tags?.join(', ') || ''
  }
})

/**
 * Mutation para crear servidor
 */
const createMutation = useMutation({
  mutationFn: (data: CreateVPSServerDto) => vpsServerService.create(data),
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['servers'] })
    toast.success('Servidor creado exitosamente')
    router.push({ name: 'server-list' })
  },
  onError: (error: any) => {
    const message = error.response?.data?.message || 'Error al crear el servidor'
    submitError.value = message
    toast.error(message)
    
    // Manejar errores de validación del backend
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  },
  onSettled: () => {
    isSubmitting.value = false
  },
})

/**
 * Mutation para actualizar servidor
 */
const updateMutation = useMutation({
  mutationFn: (data: { id: string; data: UpdateVPSServerDto }) =>
    vpsServerService.update(data.id, data.data),
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['servers'] })
    queryClient.invalidateQueries({ queryKey: ['server', serverId.value] })
    toast.success('Servidor actualizado exitosamente')
    router.push({ name: 'server-list' })
  },
  onError: (error: any) => {
    const message = error.response?.data?.message || 'Error al actualizar el servidor'
    submitError.value = message
    toast.error(message)
    
    // Manejar errores de validación del backend
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  },
  onSettled: () => {
    isSubmitting.value = false
  },
})

/**
 * Validar un campo individual
 */
const validateField = async (fieldName: string) => {
  try {
    const schema = isEditMode.value ? updateVPSServerSchema : createVPSServerSchema
    await schema.validateAt(fieldName, formData.value)
    // Si la validación pasa, limpiar el error
    delete errors.value[fieldName]
  } catch (error) {
    if (error instanceof Error && 'message' in error) {
      errors.value[fieldName] = error.message
    }
  }
}

/**
 * Validar todo el formulario
 */
const validateForm = async (): Promise<boolean> => {
  try {
    const schema = isEditMode.value ? updateVPSServerSchema : createVPSServerSchema
    await schema.validate(formData.value, { abortEarly: false })
    errors.value = {}
    return true
  } catch (error) {
    if (error instanceof Error && 'inner' in error) {
      const validationError = error as ValidationError
      errors.value = {}
      validationError.inner.forEach((err) => {
        if (err.path) {
          errors.value[err.path] = err.message
        }
      })
    }
    return false
  }
}

/**
 * Verificar si el formulario es válido
 */
const isFormValid = computed(() => {
  return Object.keys(errors.value).length === 0 &&
         formData.value.name !== '' &&
         formData.value.ipAddress !== '' &&
         formData.value.username !== ''
})

/**
 * Parsear tags desde el input
 */
const parseTags = () => {
  if (tagsInput.value.trim()) {
    formData.value.tags = tagsInput.value
      .split(',')
      .map((tag) => tag.trim())
      .filter((tag) => tag !== '')
  } else {
    formData.value.tags = []
  }
}

/**
 * Eliminar un tag
 */
const removeTag = (index: number) => {
  formData.value.tags?.splice(index, 1)
  tagsInput.value = formData.value.tags?.join(', ') || ''
}

/**
 * Manejar envío del formulario
 */
const handleSubmit = async () => {
  submitError.value = ''
  
  // Validar formulario
  const isValid = await validateForm()
  if (!isValid) {
    toast.warning('Por favor, corrija los errores en el formulario')
    return
  }

  isSubmitting.value = true

  if (isEditMode.value && serverId.value) {
    // Actualizar servidor existente
    updateMutation.mutate({
      id: serverId.value,
      data: formData.value as UpdateVPSServerDto,
    })
  } else {
    // Crear nuevo servidor
    createMutation.mutate(formData.value as CreateVPSServerDto)
  }
}

/**
 * Cancelar y volver a la lista
 */
const handleCancel = () => {
  router.push({ name: 'server-list' })
}
</script>

<style scoped>
.form-label {
  font-weight: 500;
}

.form-text {
  font-size: 0.875rem;
}

.badge {
  font-size: 0.875rem;
}

.badge i {
  font-size: 0.75rem;
}

.invalid-feedback {
  display: block;
}

.card-header h4,
.card-header h6 {
  margin: 0;
}
</style>
