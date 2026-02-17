<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Contenidos
            'ver-contenidos',
            'crear-contenidos',
            'editar-contenidos',
            'eliminar-contenidos',
            'publicar-contenidos',
            
            // Categorías
            'ver-categorias',
            'crear-categorias',
            'editar-categorias',
            'eliminar-categorias',
            
            // Usuarios
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'eliminar-usuarios',
            
            // Transparencia
            'ver-transparencia',
            'editar-transparencia',
            'publicar-transparencia',
            
            // PQRS
            'ver-pqrs',
            'responder-pqrs',
            'cerrar-pqrs',
            
            // Configuración
            'ver-configuracion',
            'editar-configuracion',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        
        // Super Admin - Todos los permisos
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Editor - Gestión de contenidos
        $editor = Role::create(['name' => 'editor']);
        $editor->givePermissionTo([
            'ver-contenidos',
            'crear-contenidos',
            'editar-contenidos',
            'publicar-contenidos',
            'ver-categorias',
            'crear-categorias',
            'editar-categorias',
        ]);

        // Administrador de Transparencia
        $adminTransparencia = Role::create(['name' => 'admin-transparencia']);
        $adminTransparencia->givePermissionTo([
            'ver-transparencia',
            'editar-transparencia',
            'publicar-transparencia',
            'ver-contenidos',
        ]);

        // Atención PQRS
        $atencionPqrs = Role::create(['name' => 'atencion-pqrs']);
        $atencionPqrs->givePermissionTo([
            'ver-pqrs',
            'responder-pqrs',
            'cerrar-pqrs',
        ]);

        // Ciudadano - Solo lectura
        $ciudadano = Role::create(['name' => 'ciudadano']);
        $ciudadano->givePermissionTo([
            'ver-contenidos',
        ]);

        // Auditor - Solo lectura a todo
        $auditor = Role::create(['name' => 'auditor']);
        $auditor->givePermissionTo([
            'ver-contenidos',
            'ver-categorias',
            'ver-usuarios',
            'ver-transparencia',
            'ver-pqrs',
            'ver-configuracion',
        ]);
    }
}
