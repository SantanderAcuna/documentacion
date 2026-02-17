<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Seeder para Roles y Permisos
 * 
 * Crea la estructura RBAC completa del sistema
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Contenidos
            'ver-contenidos',
            'crear-contenidos',
            'editar-contenidos',
            'eliminar-contenidos',
            'publicar-contenidos',
            
            // Decretos
            'ver-decretos',
            'crear-decretos',
            'editar-decretos',
            'eliminar-decretos',
            'publicar-decretos',
            
            // Gacetas
            'ver-gacetas',
            'crear-gacetas',
            'editar-gacetas',
            'eliminar-gacetas',
            
            // Contratos
            'ver-contratos',
            'crear-contratos',
            'editar-contratos',
            'eliminar-contratos',
            
            // PQRS
            'ver-pqrs',
            'asignar-pqrs',
            'responder-pqrs',
            'cerrar-pqrs',
            
            // Usuarios
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'eliminar-usuarios',
            'asignar-roles',
            
            // Dependencias
            'ver-dependencias',
            'crear-dependencias',
            'editar-dependencias',
            'eliminar-dependencias',
            
            // Reportes
            'ver-reportes',
            'exportar-reportes',
            
            // Configuración
            'ver-configuracion',
            'editar-configuracion',
            
            // Auditoría
            'ver-auditoria',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crear roles
        
        // Super Admin - Todos los permisos
        $superAdmin = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Administrador - Casi todos los permisos
        $admin = Role::create(['name' => 'administrador', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'ver-contenidos', 'crear-contenidos', 'editar-contenidos', 'eliminar-contenidos', 'publicar-contenidos',
            'ver-decretos', 'crear-decretos', 'editar-decretos', 'eliminar-decretos', 'publicar-decretos',
            'ver-gacetas', 'crear-gacetas', 'editar-gacetas', 'eliminar-gacetas',
            'ver-contratos', 'crear-contratos', 'editar-contratos',
            'ver-pqrs', 'asignar-pqrs', 'responder-pqrs', 'cerrar-pqrs',
            'ver-usuarios', 'crear-usuarios', 'editar-usuarios',
            'ver-dependencias', 'crear-dependencias', 'editar-dependencias',
            'ver-reportes', 'exportar-reportes',
            'ver-auditoria',
        ]);

        // Editor - Gestión de contenidos
        $editor = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $editor->givePermissionTo([
            'ver-contenidos', 'crear-contenidos', 'editar-contenidos', 'publicar-contenidos',
            'ver-decretos', 'crear-decretos', 'editar-decretos',
            'ver-gacetas', 'crear-gacetas', 'editar-gacetas',
        ]);

        // Autor - Crear y editar su propio contenido
        $autor = Role::create(['name' => 'autor', 'guard_name' => 'web']);
        $autor->givePermissionTo([
            'ver-contenidos', 'crear-contenidos', 'editar-contenidos',
        ]);

        // Funcionario - PQRS y consultas
        $funcionario = Role::create(['name' => 'funcionario', 'guard_name' => 'web']);
        $funcionario->givePermissionTo([
            'ver-contenidos',
            'ver-pqrs', 'asignar-pqrs', 'responder-pqrs',
            'ver-reportes',
        ]);

        // Ciudadano - Solo lectura pública
        $ciudadano = Role::create(['name' => 'ciudadano', 'guard_name' => 'web']);
        $ciudadano->givePermissionTo([
            'ver-contenidos',
        ]);

        $this->command->info('✅ Roles y permisos creados exitosamente');
    }
}
