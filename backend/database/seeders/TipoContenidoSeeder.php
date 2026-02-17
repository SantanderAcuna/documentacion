<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoContenido;

/**
 * Seeder para Tipos de Contenido
 * 
 * Crea los tipos de contenido predefinidos del sistema
 */
class TipoContenidoSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        $tipos = [
            [
                'nombre' => 'Post',
                'slug' => 'post',
                'descripcion' => 'Publicaciones de blog estándar',
                'icono' => 'fa-file-alt',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Blog',
                'slug' => 'blog',
                'descripcion' => 'Entradas de blog detalladas',
                'icono' => 'fa-blog',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Noticia',
                'slug' => 'noticia',
                'descripcion' => 'Noticias y comunicados',
                'icono' => 'fa-newspaper',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Página',
                'slug' => 'pagina',
                'descripcion' => 'Páginas estáticas de información',
                'icono' => 'fa-file',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Evento',
                'slug' => 'evento',
                'descripcion' => 'Eventos y convocatorias',
                'icono' => 'fa-calendar-alt',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Anuncio',
                'slug' => 'anuncio',
                'descripcion' => 'Anuncios importantes',
                'icono' => 'fa-bullhorn',
                'esta_activo' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoContenido::create($tipo);
        }

        $this->command->info('✅ Tipos de contenido creados: ' . count($tipos));
    }
}
