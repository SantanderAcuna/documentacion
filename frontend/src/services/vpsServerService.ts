/**
 * Servicio CRUD para Servidores VPS
 * 
 * Este servicio maneja todas las operaciones CRUD (Create, Read, Update, Delete)
 * para los servidores VPS
 */

import apiClient from './api'
import type {
  VPSServer,
  CreateVPSServerDto,
  UpdateVPSServerDto,
  ApiResponse,
  PaginatedResponse,
} from '@/types/entities'

/**
 * Servicio para gestión de servidores VPS
 */
class VPSServerService {
  private readonly basePath = '/servers'

  /**
   * Obtiene la lista de todos los servidores
   * @param page - Número de página para paginación
   * @param perPage - Cantidad de elementos por página
   * @returns Promise con la lista paginada de servidores
   */
  async getAll(page = 1, perPage = 10): Promise<PaginatedResponse<VPSServer>> {
    const response = await apiClient.get<PaginatedResponse<VPSServer>>(
      this.basePath,
      {
        params: { page, perPage },
      }
    )
    return response.data
  }

  /**
   * Obtiene un servidor por su ID
   * @param id - ID del servidor
   * @returns Promise con los datos del servidor
   */
  async getById(id: string): Promise<ApiResponse<VPSServer>> {
    const response = await apiClient.get<ApiResponse<VPSServer>>(
      `${this.basePath}/${id}`
    )
    return response.data
  }

  /**
   * Crea un nuevo servidor
   * @param data - Datos del servidor a crear
   * @returns Promise con el servidor creado
   */
  async create(data: CreateVPSServerDto): Promise<ApiResponse<VPSServer>> {
    const response = await apiClient.post<ApiResponse<VPSServer>>(
      this.basePath,
      data
    )
    return response.data
  }

  /**
   * Actualiza un servidor existente
   * @param id - ID del servidor a actualizar
   * @param data - Datos a actualizar
   * @returns Promise con el servidor actualizado
   */
  async update(
    id: string,
    data: UpdateVPSServerDto
  ): Promise<ApiResponse<VPSServer>> {
    const response = await apiClient.put<ApiResponse<VPSServer>>(
      `${this.basePath}/${id}`,
      data
    )
    return response.data
  }

  /**
   * Elimina un servidor
   * @param id - ID del servidor a eliminar
   * @returns Promise con la confirmación de eliminación
   */
  async delete(id: string): Promise<ApiResponse<{ deleted: boolean }>> {
    const response = await apiClient.delete<ApiResponse<{ deleted: boolean }>>(
      `${this.basePath}/${id}`
    )
    return response.data
  }

  /**
   * Prueba la conexión a un servidor
   * @param id - ID del servidor
   * @returns Promise con el resultado de la prueba
   */
  async testConnection(id: string): Promise<ApiResponse<{ connected: boolean; message: string }>> {
    const response = await apiClient.post<ApiResponse<{ connected: boolean; message: string }>>(
      `${this.basePath}/${id}/test-connection`
    )
    return response.data
  }

  /**
   * Obtiene el estado actual de un servidor
   * @param id - ID del servidor
   * @returns Promise con el estado del servidor
   */
  async getStatus(id: string): Promise<ApiResponse<{ status: string }>> {
    const response = await apiClient.get<ApiResponse<{ status: string }>>(
      `${this.basePath}/${id}/status`
    )
    return response.data
  }
}

// Exportamos una instancia única del servicio (Singleton)
export const vpsServerService = new VPSServerService()
