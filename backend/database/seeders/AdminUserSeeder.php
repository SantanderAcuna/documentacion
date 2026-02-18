<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@alcaldia.gov.co',
            'password' => Hash::make('Admin2026!'),
        ]);
        $admin->assignRole('super-admin');

        // Create Editor
        $editor = User::create([
            'name' => 'Editor',
            'email' => 'editor@alcaldia.gov.co',
            'password' => Hash::make('Editor2026!'),
        ]);
        $editor->assignRole('editor');

        // Create PQRS Attendant
        $pqrs = User::create([
            'name' => 'Atención PQRS',
            'email' => 'pqrs@alcaldia.gov.co',
            'password' => Hash::make('Pqrs2026!'),
        ]);
        $pqrs->assignRole('atencion-pqrs');

        // Create Transparency Admin
        $transparency = User::create([
            'name' => 'Admin Transparencia',
            'email' => 'transparencia@alcaldia.gov.co',
            'password' => Hash::make('Trans2026!'),
        ]);
        $transparency->assignRole('admin-transparencia');

        $this->command->info('Admin users created successfully!');
        $this->command->table(
            ['Email', 'Password', 'Role'],
            [
                ['admin@alcaldia.gov.co', 'Admin2026!', 'super-admin'],
                ['editor@alcaldia.gov.co', 'Editor2026!', 'editor'],
                ['pqrs@alcaldia.gov.co', 'Pqrs2026!', 'atencion-pqrs'],
                ['transparencia@alcaldia.gov.co', 'Trans2026!', 'admin-transparencia'],
            ]
        );
    }
}
