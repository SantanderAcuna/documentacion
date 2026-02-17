/**
 * Configuración de Axios para realizar peticiones HTTP a la API
 * 
 * Este archivo configura:
 * - URL base de la API
 * - Interceptores de request (agregar token de autenticación)
 * - Interceptores de response (manejo de errores)
 * - Timeout y headers por defecto
 */

import axios, { AxiosError, InternalAxiosRequestConfig } from 'axios'
import type { ApiError } from '@/types/entities'

/**
 * Instancia de Axios configurada para la API
 */
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api/v1',
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // Para enviar cookies de sesión de Laravel Sanctum
})

/**
 * Interceptor de request - Agrega el token de autenticación
 */
apiClient.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    // Laravel Sanctum usa cookies HTTP-Only, pero podemos agregar un token CSRF si es necesario
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    
    if (csrfToken && config.headers) {
      config.headers['X-CSRF-TOKEN'] = csrfToken
    }

    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

/**
 * Interceptor de response - Maneja errores globales
 */
apiClient.interceptors.response.use(
  (response) => {
    return response
  },
  (error: AxiosError<ApiError>) => {
    // Manejo de errores comunes
    if (error.response) {
      const status = error.response.status

      switch (status) {
        case 401:
          // No autorizado - redirigir al login
          console.error('No autorizado. Por favor, inicie sesión.')
          // Aquí podrías redirigir al login si tienes el router disponible
          break

        case 403:
          // Prohibido - sin permisos
          console.error('No tiene permisos para realizar esta acción.')
          break

        case 404:
          // No encontrado
          console.error('Recurso no encontrado.')
          break

        case 422:
          // Error de validación
          console.error('Error de validación:', error.response.data.errors)
          break

        case 500:
          // Error del servidor
          console.error('Error interno del servidor. Por favor, intente más tarde.')
          break

        default:
          console.error('Error en la petición:', error.response.data.message)
      }
    } else if (error.request) {
      // La petición se hizo pero no hubo respuesta
      console.error('No se pudo conectar con el servidor.')
    } else {
      // Algo pasó al configurar la petición
      console.error('Error al realizar la petición:', error.message)
    }

    return Promise.reject(error)
  }
)

export default apiClient
