<template>
  <div class="container-fluid py-4">
    <!-- Header con título y botón de crear -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h3 mb-0">
              <i class="fas fa-server me-2"></i>
              Gestión de Servidores VPS
            </h1>
            <p class="text-muted mb-0">
              Administre y monitoree sus servidores VPS
            </p>
          </div>
          <button
            class="btn btn-primary"
            @click="handleCreate"
            :disabled="isLoading"
          >
            <i class="fas fa-plus me-2"></i>
            Nuevo Servidor
          </button>
        </div>
      </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="row mb-3">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text">
            <i class="fas fa-search"></i>
          </span>
          <input
            type="text"
            class="form-control"
            placeholder="Buscar por nombre, IP o usuario..."
            v-model="searchQuery"
          />
        </div>
      </div>
      <div class="col-md-3">
        <select class="form-select" v-model="statusFilter">
          <option value="">Todos los estados</option>
          <option value="online">En línea</option>
          <option value="offline">Fuera de línea</option>
          <option value="unknown">Desconocido</option>
        </select>
      </div>
      <div class="col-md-3 text-end">
        <button class="btn btn-outline-secondary" @click="refetch">
          <i class="fas fa-sync-alt me-2"></i>
          Actualizar
        </button>
      </div>
    </div>

    <!-- Tabla de servidores -->
    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <!-- Estado de carga -->
            <div v-if="isLoading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
              <p class="mt-3 text-muted">Cargando servidores...</p>
            </div>

            <!-- Mensaje de error -->
            <div v-else-if="error" class="alert alert-danger" role="alert">
              <i class="fas fa-exclamation-triangle me-2"></i>
              Error al cargar los servidores: {{ error.message }}
            </div>

            <!-- Lista vacía -->
            <div v-else-if="!filteredServers.length" class="text-center py-5">
              <i class="fas fa-server fa-3x text-muted mb-3"></i>
              <p class="text-muted">
                No hay servidores disponibles.
                <a href="#" @click.prevent="handleCreate">Cree uno nuevo</a>
              </p>
            </div>

            <!-- Tabla con datos -->
            <div v-else class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Nombre</th>
                    <th>IP / Hostname</th>
                    <th>Puerto</th>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Última Verificación</th>
                    <th class="text-end">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="server in filteredServers" :key="server.id">
                    <td>
                      <strong>{{ server.name }}</strong>
                      <br />
                      <small class="text-muted">
                        <span
                          v-for="tag in server.tags"
                          :key="tag"
                          class="badge bg-secondary me-1"
                        >
                          {{ tag }}
                        </span>
                      </small>
                    </td>
                    <td>
                      <code>{{ server.ipAddress }}</code>
                    </td>
                    <td>{{ server.port }}</td>
                    <td>{{ server.username }}</td>
                    <td>
                      <span
                        class="badge"
                        :class="getStatusBadgeClass(server.status)"
                      >
                        <i :class="getStatusIcon(server.status)" class="me-1"></i>
                        {{ getStatusText(server.status) }}
                      </span>
                    </td>
                    <td>
                      <small class="text-muted">
                        {{ formatDate(server.lastChecked) }}
                      </small>
                    </td>
                    <td class="text-end">
                      <div class="btn-group" role="group">
                        <button
                          class="btn btn-sm btn-outline-info"
                          @click="handleTestConnection(server)"
                          :disabled="isTestingConnection === server.id"
                          title="Probar Conexión"
                        >
                          <i
                            :class="
                              isTestingConnection === server.id
                                ? 'fas fa-spinner fa-spin'
                                : 'fas fa-plug'
                            "
                          ></i>
                        </button>
                        <button
                          class="btn btn-sm btn-outline-primary"
                          @click="handleEdit(server)"
                          title="Editar"
                        >
                          <i class="fas fa-edit"></i>
                        </button>
                        <button
                          class="btn btn-sm btn-outline-danger"
                          @click="handleDelete(server)"
                          :disabled="deletingId === server.id"
                          title="Eliminar"
                        >
                          <i
                            :class="
                              deletingId === server.id
                                ? 'fas fa-spinner fa-spin'
                                : 'fas fa-trash'
                            "
                          ></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <div v-if="data?.pagination" class="d-flex justify-content-between align-items-center mt-3">
              <div class="text-muted">
                Mostrando {{ filteredServers.length }} de {{ data.pagination.total }} servidores
              </div>
              <nav>
                <ul class="pagination mb-0">
                  <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <button class="page-link" @click="goToPage(currentPage - 1)">
                      Anterior
                    </button>
                  </li>
                  <li
                    v-for="page in visiblePages"
                    :key="page"
                    class="page-item"
                    :class="{ active: page === currentPage }"
                  >
                    <button class="page-link" @click="goToPage(page)">
                      {{ page }}
                    </button>
                  </li>
                  <li
                    class="page-item"
                    :class="{ disabled: currentPage === data.pagination.lastPage }"
                  >
                    <button class="page-link" @click="goToPage(currentPage + 1)">
                      Siguiente
                    </button>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div
      v-if="showDeleteConfirm"
      class="modal fade show d-block"
      tabindex="-1"
      style="background-color: rgba(0, 0, 0, 0.5)"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-exclamation-triangle text-warning me-2"></i>
              Confirmar Eliminación
            </h5>
            <button
              type="button"
              class="btn-close"
              @click="cancelDelete"
            ></button>
          </div>
          <div class="modal-body">
            <p>
              ¿Está seguro que desea eliminar el servidor
              <strong>{{ serverToDelete?.name }}</strong>?
            </p>
            <p class="text-muted mb-0">
              Esta acción no se puede deshacer.
            </p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="cancelDelete"
              :disabled="deletingId !== null"
            >
              Cancelar
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click="confirmDelete"
              :disabled="deletingId !== null"
            >
              <i
                :class="
                  deletingId !== null
                    ? 'fas fa-spinner fa-spin me-2'
                    : 'fas fa-trash me-2'
                "
              ></i>
              {{ deletingId !== null ? 'Eliminando...' : 'Eliminar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { useToast } from 'vue-toastification'
import { vpsServerService } from '@/services/vpsServerService'
import type { VPSServer } from '@/types/entities'

/**
 * Composables y servicios
 */
const router = useRouter()
const queryClient = useQueryClient()
const toast = useToast()

/**
 * Estado reactivo del componente
 */
const currentPage = ref(1)
const perPage = ref(10)
const searchQuery = ref('')
const statusFilter = ref<string>('')
const showDeleteConfirm = ref(false)
const serverToDelete = ref<VPSServer | null>(null)
const deletingId = ref<string | null>(null)
const isTestingConnection = ref<string | null>(null)

/**
 * Query para obtener la lista de servidores
 */
const {
  data,
  isLoading,
  error,
  refetch,
} = useQuery({
  queryKey: ['servers', currentPage, perPage],
  queryFn: () => vpsServerService.getAll(currentPage.value, perPage.value),
  staleTime: 30000, // Los datos se consideran frescos por 30 segundos
})

/**
 * Mutation para eliminar un servidor
 */
const deleteMutation = useMutation({
  mutationFn: (id: string) => vpsServerService.delete(id),
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['servers'] })
    toast.success('Servidor eliminado exitosamente')
    showDeleteConfirm.value = false
    serverToDelete.value = null
  },
  onError: (error: any) => {
    toast.error(error.response?.data?.message || 'Error al eliminar el servidor')
  },
  onSettled: () => {
    deletingId.value = null
  },
})

/**
 * Mutation para probar conexión
 */
const testConnectionMutation = useMutation({
  mutationFn: (id: string) => vpsServerService.testConnection(id),
  onSuccess: (response) => {
    if (response.data.connected) {
      toast.success('Conexión exitosa al servidor')
    } else {
      toast.error(response.data.message || 'No se pudo conectar al servidor')
    }
  },
  onError: (error: any) => {
    toast.error(error.response?.data?.message || 'Error al probar la conexión')
  },
  onSettled: () => {
    isTestingConnection.value = null
  },
})

/**
 * Servidores filtrados por búsqueda y estado
 */
const filteredServers = computed(() => {
  if (!data.value?.data) return []

  let servers = data.value.data

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    servers = servers.filter(
      (server) =>
        server.name.toLowerCase().includes(query) ||
        server.ipAddress.toLowerCase().includes(query) ||
        server.username.toLowerCase().includes(query)
    )
  }

  // Filtrar por estado
  if (statusFilter.value) {
    servers = servers.filter((server) => server.status === statusFilter.value)
  }

  return servers
})

/**
 * Páginas visibles en la paginación
 */
const visiblePages = computed(() => {
  if (!data.value?.pagination) return []
  
  const total = data.value.pagination.lastPage
  const current = currentPage.value
  const pages: number[] = []
  
  // Mostrar máximo 5 páginas
  let start = Math.max(1, current - 2)
  let end = Math.min(total, start + 4)
  
  // Ajustar si estamos cerca del final
  if (end - start < 4) {
    start = Math.max(1, end - 4)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

/**
 * Handlers de eventos
 */
const handleCreate = () => {
  router.push({ name: 'server-create' })
}

const handleEdit = (server: VPSServer) => {
  router.push({ name: 'server-edit', params: { id: server.id } })
}

const handleDelete = (server: VPSServer) => {
  serverToDelete.value = server
  showDeleteConfirm.value = true
}

const cancelDelete = () => {
  showDeleteConfirm.value = false
  serverToDelete.value = null
}

const confirmDelete = () => {
  if (serverToDelete.value) {
    deletingId.value = serverToDelete.value.id
    deleteMutation.mutate(serverToDelete.value.id)
  }
}

const handleTestConnection = (server: VPSServer) => {
  isTestingConnection.value = server.id
  testConnectionMutation.mutate(server.id)
}

const goToPage = (page: number) => {
  if (page >= 1 && data.value?.pagination && page <= data.value.pagination.lastPage) {
    currentPage.value = page
  }
}

/**
 * Funciones auxiliares
 */
const getStatusBadgeClass = (status: string) => {
  const classes = {
    online: 'bg-success',
    offline: 'bg-danger',
    unknown: 'bg-warning',
  }
  return classes[status as keyof typeof classes] || 'bg-secondary'
}

const getStatusIcon = (status: string) => {
  const icons = {
    online: 'fas fa-check-circle',
    offline: 'fas fa-times-circle',
    unknown: 'fas fa-question-circle',
  }
  return icons[status as keyof typeof icons] || 'fas fa-circle'
}

const getStatusText = (status: string) => {
  const texts = {
    online: 'En línea',
    offline: 'Fuera de línea',
    unknown: 'Desconocido',
  }
  return texts[status as keyof typeof texts] || status
}

const formatDate = (date: string | null) => {
  if (!date) return 'Nunca'
  
  const d = new Date(date)
  const now = new Date()
  const diff = now.getTime() - d.getTime()
  
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)
  
  if (minutes < 1) return 'Hace un momento'
  if (minutes < 60) return `Hace ${minutes} minuto${minutes > 1 ? 's' : ''}`
  if (hours < 24) return `Hace ${hours} hora${hours > 1 ? 's' : ''}`
  if (days < 7) return `Hace ${days} día${days > 1 ? 's' : ''}`
  
  return d.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}
</script>

<style scoped>
.modal.show {
  display: block;
}

.btn-group .btn {
  padding: 0.25rem 0.5rem;
}

.table code {
  background-color: #f8f9fa;
  padding: 0.125rem 0.25rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
}

.badge {
  font-size: 0.75rem;
  font-weight: 500;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}
</style>
