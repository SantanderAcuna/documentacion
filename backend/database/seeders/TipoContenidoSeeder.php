<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoContenido;

/**
 * Seeder para Tipos de Contenido
 * 
 * Crea TODOS los tipos de contenido del sistema.
 * La tabla 'contenidos' es universal y maneja todos estos tipos.
 */
class TipoContenidoSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        $tipos = [
            // Contenido editorial
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
            
            // Documentos oficiales
            [
                'nombre' => 'Decreto',
                'slug' => 'decreto',
                'descripcion' => 'Decretos municipales',
                'icono' => 'fa-gavel',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Gaceta',
                'slug' => 'gaceta',
                'descripcion' => 'Gacetas oficiales',
                'icono' => 'fa-book',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Circular',
                'slug' => 'circular',
                'descripcion' => 'Circulares administrativas',
                'icono' => 'fa-file-circle-check',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Acta',
                'slug' => 'acta',
                'descripcion' => 'Actas de reuniones',
                'icono' => 'fa-file-signature',
                'esta_activo' => true,
            ],
            
            // Transparencia
            [
                'nombre' => 'Contrato',
                'slug' => 'contrato',
                'descripcion' => 'Contratos y convenios (SECOP)',
                'icono' => 'fa-handshake',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Resolución',
                'slug' => 'resolucion',
                'descripcion' => 'Resoluciones administrativas',
                'icono' => 'fa-stamp',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Acuerdo',
                'slug' => 'acuerdo',
                'descripcion' => 'Acuerdos municipales',
                'icono' => 'fa-file-contract',
                'esta_activo' => true,
            ],
            
            // Otros
            [
                'nombre' => 'Publicación',
                'slug' => 'publicacion',
                'descripcion' => 'Publicaciones generales',
                'icono' => 'fa-share-nodes',
                'esta_activo' => true,
            ],
            [
                'nombre' => 'Documento',
                'slug' => 'documento',
                'descripcion' => 'Documentos generales',
                'icono' => 'fa-file-pdf',
                'esta_activo' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoContenido::create($tipo);
        }

        $this->command->info('✅ Tipos de contenido creados: ' . count($tipos));
        $this->command->line('');
        $this->command->info('📋 Tipos disponibles:');
        $this->command->line('   Contenido Editorial: Post, Blog, Noticia, Página, Evento, Anuncio');
        $this->command->line('   Documentos Oficiales: Decreto, Gaceta, Circular, Acta');
        $this->command->line('   Transparencia: Contrato, Resolución, Acuerdo');
        $this->command->line('   Otros: Publicación, Documento');
    }
}
