/**
 * Esquemas de Validación con Yup
 * 
 * Define las reglas de validación para los formularios de servidores VPS
 */

import * as yup from 'yup'

/**
 * Regex para validar direcciones IP (IPv4)
 */
const ipv4Regex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/

/**
 * Regex para validar hostnames
 */
const hostnameRegex = /^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)*[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/

/**
 * Esquema de validación para crear un servidor VPS
 */
export const createVPSServerSchema = yup.object({
  name: yup
    .string()
    .required('El nombre del servidor es obligatorio')
    .min(3, 'El nombre debe tener al menos 3 caracteres')
    .max(100, 'El nombre no puede exceder 100 caracteres')
    .matches(
      /^[a-zA-Z0-9\s\-_.]+$/,
      'El nombre solo puede contener letras, números, espacios, guiones y guiones bajos'
    ),

  ipAddress: yup
    .string()
    .required('La dirección IP o hostname es obligatoria')
    .test(
      'ip-or-hostname',
      'Debe ser una dirección IP válida o un hostname válido',
      (value) => {
        if (!value) return false
        return ipv4Regex.test(value) || hostnameRegex.test(value)
      }
    ),

  port: yup
    .number()
    .typeError('El puerto debe ser un número')
    .min(1, 'El puerto debe ser mayor a 0')
    .max(65535, 'El puerto debe ser menor a 65536')
    .default(22),

  username: yup
    .string()
    .required('El nombre de usuario es obligatorio')
    .min(1, 'El nombre de usuario no puede estar vacío')
    .max(32, 'El nombre de usuario no puede exceder 32 caracteres')
    .matches(
      /^[a-z_][a-z0-9_-]*[$]?$/,
      'El nombre de usuario debe seguir el formato Unix'
    ),

  authMethod: yup
    .string()
    .oneOf(['password', 'ssh_key'], 'Método de autenticación inválido')
    .required('El método de autenticación es obligatorio'),

  sshKeyId: yup
    .string()
    .nullable()
    .when('authMethod', {
      is: 'ssh_key',
      then: (schema) => schema.required('Debe seleccionar una llave SSH'),
      otherwise: (schema) => schema.nullable(),
    }),

  tags: yup
    .array()
    .of(yup.string())
    .default([])
    .nullable(),
})

/**
 * Esquema de validación para actualizar un servidor VPS
 * Similar al de creación pero todos los campos son opcionales
 */
export const updateVPSServerSchema = yup.object({
  name: yup
    .string()
    .min(3, 'El nombre debe tener al menos 3 caracteres')
    .max(100, 'El nombre no puede exceder 100 caracteres')
    .matches(
      /^[a-zA-Z0-9\s\-_.]+$/,
      'El nombre solo puede contener letras, números, espacios, guiones y guiones bajos'
    ),

  ipAddress: yup
    .string()
    .test(
      'ip-or-hostname',
      'Debe ser una dirección IP válida o un hostname válido',
      (value) => {
        if (!value) return true // Opcional
        return ipv4Regex.test(value) || hostnameRegex.test(value)
      }
    ),

  port: yup
    .number()
    .typeError('El puerto debe ser un número')
    .min(1, 'El puerto debe ser mayor a 0')
    .max(65535, 'El puerto debe ser menor a 65536'),

  username: yup
    .string()
    .min(1, 'El nombre de usuario no puede estar vacío')
    .max(32, 'El nombre de usuario no puede exceder 32 caracteres')
    .matches(
      /^[a-z_][a-z0-9_-]*[$]?$/,
      'El nombre de usuario debe seguir el formato Unix'
    ),

  authMethod: yup
    .string()
    .oneOf(['password', 'ssh_key'], 'Método de autenticación inválido'),

  sshKeyId: yup
    .string()
    .nullable()
    .when('authMethod', {
      is: 'ssh_key',
      then: (schema) => schema.required('Debe seleccionar una llave SSH'),
      otherwise: (schema) => schema.nullable(),
    }),

  tags: yup
    .array()
    .of(yup.string())
    .nullable(),
})

/**
 * Tipo inferido del esquema de creación
 */
export type CreateVPSServerFormData = yup.InferType<typeof createVPSServerSchema>

/**
 * Tipo inferido del esquema de actualización
 */
export type UpdateVPSServerFormData = yup.InferType<typeof updateVPSServerSchema>
