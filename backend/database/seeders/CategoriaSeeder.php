<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

/**
 * Seeder para Categorías
 * 
 * Crea categorías predefinidas con estructura jerárquica
 */
class CategoriaSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        // Categorías raíz
        $categoriasRaiz = [
            [
                'nombre' => 'Transparencia',
                'slug' => 'transparencia',
                'descripcion' => 'Información de transparencia y acceso a la información',
                'color' => '#2563EB',
                'icono' => 'fa-eye',
            ],
            [
                'nombre' => 'Normatividad',
                'slug' => 'normatividad',
                'descripcion' => 'Decretos, gacetas y normativa municipal',
                'color' => '#DC2626',
                'icono' => 'fa-gavel',
            ],
            [
                'nombre' => 'Servicios',
                'slug' => 'servicios',
                'descripcion' => 'Servicios y trámites ciudadanos',
                'color' => '#16A34A',
                'icono' => 'fa-hands-helping',
            ],
            [
                'nombre' => 'Noticias',
                'slug' => 'noticias',
                'descripcion' => 'Noticias y comunicados oficiales',
                'color' => '#F59E0B',
                'icono' => 'fa-newspaper',
            ],
            [
                'nombre' => 'Eventos',
                'slug' => 'eventos',
                'descripcion' => 'Eventos y actividades municipales',
                'color' => '#8B5CF6',
                'icono' => 'fa-calendar-alt',
            ],
        ];

        foreach ($categoriasRaiz as $index => $catData) {
            Categoria::create(array_merge($catData, [
                'padre_id' => null,
                'orden' => $index + 1,
                'esta_activo' => true,
            ]));
        }

        // Subcategorías de Transparencia
        $transparencia = Categoria::where('slug', 'transparencia')->first();
        if ($transparencia) {
            $subTransparencia = [
                ['nombre' => 'Contratos', 'slug' => 'contratos'],
                ['nombre' => 'Presupuesto', 'slug' => 'presupuesto'],
                ['nombre' => 'Contratación', 'slug' => 'contratacion'],
                ['nombre' => 'Datos Abiertos', 'slug' => 'datos-abiertos'],
            ];

            foreach ($subTransparencia as $index => $sub) {
                Categoria::create(array_merge($sub, [
                    'padre_id' => $transparencia->id,
                    'color' => '#3B82F6',
                    'icono' => 'fa-folder',
                    'orden' => $index + 1,
                    'esta_activo' => true,
                ]));
            }
        }

        // Subcategorías de Normatividad
        $normatividad = Categoria::where('slug', 'normatividad')->first();
        if ($normatividad) {
            $subNormatividad = [
                ['nombre' => 'Decretos', 'slug' => 'decretos'],
                ['nombre' => 'Gacetas', 'slug' => 'gacetas'],
                ['nombre' => 'Circulares', 'slug' => 'circulares'],
                ['nombre' => 'Acuerdos', 'slug' => 'acuerdos'],
            ];

            foreach ($subNormatividad as $index => $sub) {
                Categoria::create(array_merge($sub, [
                    'padre_id' => $normatividad->id,
                    'color' => '#EF4444',
                    'icono' => 'fa-file-alt',
                    'orden' => $index + 1,
                    'esta_activo' => true,
                ]));
            }
        }

        $this->command->info('✅ Categorías creadas con estructura jerárquica');
    }
}
