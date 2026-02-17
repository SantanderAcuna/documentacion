<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Database Seeder Principal
 * 
 * Orquesta la ejecución de todos los seeders del sistema
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Ejecutar los seeders de la base de datos
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando proceso de seeding del CMS Gubernamental');
        $this->command->newLine();

        // 1. Roles y Permisos (debe ser primero)
        $this->command->info('📝 Creando roles y permisos...');
        $this->call(RolePermissionSeeder::class);
        $this->command->newLine();

        // 2. Tipos de Contenido
        $this->command->info('📁 Creando tipos de contenido...');
        $this->call(TipoContenidoSeeder::class);
        $this->command->newLine();

        // 3. Dependencias (estructura organizacional)
        $this->command->info('🏛️  Creando estructura de dependencias...');
        $this->call(DependenciaSeeder::class);
        $this->command->newLine();

        // 4. Categorías
        $this->command->info('📂 Creando categorías...');
        $this->call(CategoriaSeeder::class);
        $this->command->newLine();

        // 5. Etiquetas
        $this->command->info('🏷️  Creando etiquetas...');
        $this->call(EtiquetaSeeder::class);
        $this->command->newLine();

        // 6. Usuarios (con roles asignados)
        $this->command->info('👥 Creando usuarios...');
        $this->call(UsuarioSeeder::class);
        $this->command->newLine();

        $this->command->newLine();
        $this->command->info('✅ Proceso de seeding completado exitosamente');
        $this->command->newLine();
        $this->command->info('📊 Resumen:');
        $this->command->line('  - Roles y permisos configurados');
        $this->command->line('  - Tipos de contenido creados');
        $this->command->line('  - Estructura organizacional establecida');
        $this->command->line('  - Taxonomía (categorías y etiquetas) creada');
        $this->command->line('  - Usuarios iniciales configurados');

        $this->command->newLine();
        $this->command->warn('⚠️  Credenciales por defecto:');
        $this->command->line('   Email: admin@santamarta.gov.co');
        $this->command->line('   Contraseña: password');
    }
}
