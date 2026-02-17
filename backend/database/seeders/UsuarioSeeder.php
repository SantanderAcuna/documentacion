<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder para Usuarios
 * 
 * Crea usuarios iniciales del sistema con roles asignados
 */
class UsuarioSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Administrador',
            'email' => 'admin@santamarta.gov.co',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');

        // Administrador
        $admin = User::create([
            'name' => 'Administrador Sistema',
            'email' => 'administrador@santamarta.gov.co',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('administrador');

        // Editor
        $editor = User::create([
            'name' => 'Editor de Contenidos',
            'email' => 'editor@santamarta.gov.co',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $editor->assignRole('editor');

        // Autor
        $autor = User::create([
            'name' => 'Autor de Artículos',
            'email' => 'autor@santamarta.gov.co',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $autor->assignRole('autor');

        // Funcionario PQRS
        $funcionario = User::create([
            'name' => 'Funcionario PQRS',
            'email' => 'pqrs@santamarta.gov.co',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $funcionario->assignRole('funcionario');

        // Usuarios de prueba adicionales
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('autor');
        });

        $this->command->info('✅ Usuarios creados: 15 (5 predefinidos + 10 de prueba)');
        $this->command->warn('⚠️  Contraseña por defecto: password');
    }
}
