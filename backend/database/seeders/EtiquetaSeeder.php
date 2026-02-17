<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Etiqueta;

/**
 * Seeder para Etiquetas
 * 
 * Crea etiquetas predefinidas comunes
 */
class EtiquetaSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        $etiquetas = [
            ['nombre' => 'Urgente', 'slug' => 'urgente', 'color' => '#DC2626'],
            ['nombre' => 'Importante', 'slug' => 'importante', 'color' => '#F59E0B'],
            ['nombre' => 'Destacado', 'slug' => 'destacado', 'color' => '#8B5CF6'],
            ['nombre' => 'Nuevo', 'slug' => 'nuevo', 'color' => '#10B981'],
            ['nombre' => 'Actualizado', 'slug' => 'actualizado', 'color' => '#3B82F6'],
            ['nombre' => 'SECOP', 'slug' => 'secop', 'color' => '#6366F1'],
            ['nombre' => 'Transparencia', 'slug' => 'transparencia', 'color' => '#0EA5E9'],
            ['nombre' => 'Vigente', 'slug' => 'vigente', 'color' => '#14B8A6'],
            ['nombre' => 'Archivado', 'slug' => 'archivado', 'color' => '#6B7280'],
            ['nombre' => 'Público', 'slug' => 'publico', 'color' => '#22C55E'],
        ];

        foreach ($etiquetas as $etiqueta) {
            Etiqueta::create($etiqueta);
        }

        $this->command->info('✅ Etiquetas creadas: ' . count($etiquetas));
    }
}
