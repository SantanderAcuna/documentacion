/**
 * Interfaces de Entidades del Backend
 * 
 * Estas interfaces definen la estructura de los datos que se reciben y envían
 * desde/hacia la API de Laravel
 */

/**
 * Roles disponibles en el sistema
 */
export type UserRole = 'super_admin' | 'admin' | 'editor' | 'viewer'

/**
 * Estados de un servidor VPS
 */
export type ServerStatus = 'online' | 'offline' | 'unknown'

/**
 * Métodos de autenticación SSH
 */
export type AuthMethod = 'password' | 'ssh_key'

/**
 * Interface principal de Usuario
 */
export interface User {
  id: string
  email: string
  firstName: string
  lastName: string
  role: UserRole
  isActive: boolean
  twoFactorEnabled: boolean
  lastLogin: string | null
  createdAt: string
  updatedAt: string
}

/**
 * Interface para crear un nuevo usuario
 */
export interface CreateUserDto {
  email: string
  password: string
  firstName: string
  lastName: string
  role: UserRole
}

/**
 * Interface para actualizar un usuario
 */
export interface UpdateUserDto {
  email?: string
  firstName?: string
  lastName?: string
  role?: UserRole
  isActive?: boolean
}

/**
 * Interface de Servidor VPS
 */
export interface VPSServer {
  id: string
  name: string
  ipAddress: string
  port: number
  username: string
  authMethod: AuthMethod
  sshKeyId: string | null
  tags: string[]
  status: ServerStatus
  lastChecked: string | null
  createdBy: string
  createdAt: string
  updatedAt: string
}

/**
 * Interface para crear un servidor VPS
 */
export interface CreateVPSServerDto {
  name: string
  ipAddress: string
  port?: number
  username: string
  authMethod: AuthMethod
  sshKeyId?: string | null
  tags?: string[]
}

/**
 * Interface para actualizar un servidor VPS
 */
export interface UpdateVPSServerDto {
  name?: string
  ipAddress?: string
  port?: number
  username?: string
  authMethod?: AuthMethod
  sshKeyId?: string | null
  tags?: string[]
  status?: ServerStatus
}

/**
 * Interface de Llave SSH
 */
export interface SSHKey {
  id: string
  name: string
  publicKey: string
  fingerprint: string
  createdBy: string
  createdAt: string
  expiresAt: string | null
}

/**
 * Interface para crear una llave SSH
 */
export interface CreateSSHKeyDto {
  name: string
  publicKey: string
  privateKey: string
  passphrase?: string
}

/**
 * Interface de respuesta de la API
 */
export interface ApiResponse<T> {
  success: boolean
  data: T
  message?: string
}

/**
 * Interface de respuesta paginada
 */
export interface PaginatedResponse<T> {
  success: boolean
  data: T[]
  pagination: {
    total: number
    perPage: number
    currentPage: number
    lastPage: number
  }
}

/**
 * Interface de error de la API
 */
export interface ApiError {
  success: false
  message: string
  errors?: Record<string, string[]>
}
