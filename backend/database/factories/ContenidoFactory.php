<?php

namespace Database\Factories;

use App\Models\Contenido;
use App\Models\TipoContenido;
use App\Models\Dependencia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory para el modelo Contenido - UNIVERSAL
 * 
 * Genera contenidos de cualquier tipo: posts, blogs, noticias, decretos, gacetas, etc.
 */
class ContenidoFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     *
     * @var string
     */
    protected $model = Contenido::class;

    /**
     * Definir el estado por defecto del modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titulo = fake()->sentence();
        $estado = fake()->randomElement(['borrador', 'publicado', 'archivado']);

        return [
            'tipo_contenido_id' => TipoContenido::factory(),
            'dependencia_id' => Dependencia::factory(),
            'usuario_id' => User::factory(),
            'titulo' => $titulo,
            'slug' => Str::slug($titulo),
            'resumen' => fake()->paragraph(),
            'cuerpo' => fake()->paragraphs(5, true),
            'estado' => $estado,
            'publicado_en' => $estado === 'publicado' ? fake()->dateTimeBetween('-1 year', 'now') : null,
            'es_destacado' => fake()->boolean(20),
            'comentarios_habilitados' => fake()->boolean(80),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ESTADOS GENERALES
    |--------------------------------------------------------------------------
    */

    /**
     * Contenido publicado
     *
     * @return static
     */
    public function publicado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'publicado',
            'publicado_en' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Contenido en borrador
     *
     * @return static
     */
    public function borrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'borrador',
            'publicado_en' => null,
        ]);
    }

    /**
     * Contenido destacado
     *
     * @return static
     */
    public function destacado(): static
    {
        return $this->state(fn (array $attributes) => [
            'es_destacado' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TIPOS ESPECÍFICOS - CONTENIDO EDITORIAL
    |--------------------------------------------------------------------------
    */

    /**
     * Tipo: Post
     *
     * @return static
     */
    public function post(): static
    {
        return $this->state(function (array $attributes) {
            $tipoContenido = TipoContenido::where('slug', 'post')->first() 
                ?? TipoContenido::factory()->create(['slug' => 'post', 'nombre' => 'Post']);
            
            return [
                'tipo_contenido_id' => $tipoContenido->id,
                'imagen_destacada' => 'storage/posts/' . date('Y') . '/' . fake()->slug() . '.jpg',
            ];
        });
    }

    /**
     * Tipo: Blog
     *
     * @return static
     */
    public function blog(): static
    {
        return $this->state(function (array $attributes) {
            $tipoContenido = TipoContenido::where('slug', 'blog')->first() 
                ?? TipoContenido::factory()->create(['slug' => 'blog', 'nombre' => 'Blog']);
            
            return [
                'tipo_contenido_id' => $tipoContenido->id,
                'cuerpo' => fake()->paragraphs(10, true),
                'imagen_destacada' => 'storage/blogs/' . date('Y') . '/' . fake()->slug() . '.jpg',
            ];
        });
    }

    /**
     * Tipo: Noticia
     *
     * @return static
     */
    public function noticia(): static
    {
        return $this->state(function (array $attributes) {
            $tipoContenido = TipoContenido::where('slug', 'noticia')->first() 
                ?? TipoContenido::factory()->create(['slug' => 'noticia', 'nombre' => 'Noticia']);
            
            return [
                'tipo_contenido_id' => $tipoContenido->id,
                'imagen_destacada' => 'storage/noticias/' . date('Y') . '/' . fake()->slug() . '.jpg',
                'conteo_vistas' => fake()->numberBetween(0, 10000),
            ];
        });
    }

    /**
     * Tipo: Página
     *
     * @return static
     */
    public function pagina(): static
    {
        return $this->state(function (array $attributes) {
            $tipoContenido = TipoContenido::where('slug', 'pagina')->first() 
                ?? TipoContenido::factory()->create(['slug' => 'pagina', 'nombre' => 'Página']);
            
            return [
                'tipo_contenido_id' => $tipoContenido->id,
                'cuerpo' => fake()->paragraphs(8, true),
                'comentarios_habilitados' => false,
            ];
        });
    }

    /**
     * Tipo: Evento
     *
     * @return static
     */
    public function evento(): static
    {
        return $this->state(function (array $attributes) {
            $tipoContenido = TipoContenido::where('slug', 'evento')->first() 
                ?? TipoContenido::factory()->create(['slug' => 'evento', 'nombre' => 'Evento']);
            
            $fechaInicio = fake()->dateTimeBetween('now', '+3 months');
            
            return [
                'tipo_contenido_id' => $tipoContenido->id,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => fake()->dateTimeBetween($fechaInicio, '+1 week'),
                'ubicacion' => fake()->address(),
                'imagen_destacada' => 'storage/eventos/' . date('Y') . '/' . fake()->slug() . '.jpg',
            ];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | TIPOS ESPECÍFICOS - DOCUMENTOS OFICIALES
    |--------------------------------------------------------------------------
    */

    /**
     * Tipo: Decreto
     *
     * @return static
     */
    public function decreto(): static
    {
        return $this->state(function (array $attributes) {
            $tipoContenido = TipoContenido::where('slug', 'decreto')->first() 
                ?? TipoContenido::factory()->create(['slug' => 'decreto', 'nombre' => 'Decreto']);
            
            $numero = 'DECRETO-' . fake()->unique()->numberBetween(1, 999) . '-' . date('Y');
            $fechaEmision = fake()->dateTimeBetween('-2 years', 'now');
            
            return [
                'tipo_contenido_id' => $tipoContenido->id,
                'numero' => $numero,
                'fecha_emision' => $fechaEmision,
                'fecha_publicacion' => fake()->dateTimeBetween($fechaEmision, 'now'),
                'ruta_archivo' => 'storage/decretos/' . date('Y') . '/' . Str::slug($numero) . '.pdf',
                'nombre_archivo' => Str::slug($numero) . '.pdf',
                'cuerpo' => fake()->paragraphs(8, true),
                'estado' => 'publicado',
            ];
        });
    }

    /**
     * Tipo: Gaceta
     *
     * @return static
     */
    public function gaceta(): static
    {
        return $this->state(function (array $attributes) {
            $tipoContenido = TipoContenido::where('slug', 'gaceta')->first() 
                ?? TipoContenido::factory()->create(['slug' => 'gaceta', 'nombre' => 'Gaceta']);
            
            $numero = 'GACETA-' . fake()->unique()->numberBetween(1, 999) . '-' . date('Y');
            $fechaEmision = fake()->dateTimeBetween('-2 years', 'now');
            
            return [
                'tipo_contenido_id' => $tipoContenido->id,
                'numero' => $numero,
                'fecha_emision' => $fechaEmision,
                'fecha_publicacion' => fake()->dateTimeBetween($fechaEmision, 'now'),
                'ruta_archivo' => 'storage/gacetas/' . date('Y') . '/' . Str::slug($numero) . '.pdf',
                'nombre_archivo' => Str::slug($numero) . '.pdf',
                'cuerpo' => fake()->paragraphs(10, true),
                'estado' => 'publicado',
            ];
        });
    }

    /**
     * Tipo: Circular
     *
     * @return static
     */
    public function circular(): static
    {
        return $this->state(function (array $attributes) {
            $tipoContenido = TipoContenido::where('slug', 'circular')->first() 
                ?? TipoContenido::factory()->create(['slug' => 'circular', 'nombre' => 'Circular']);
            
            $numero = 'CIRCULAR-' . fake()->unique()->numberBetween(1, 999) . '-' . date('Y');
            $fechaEmision = fake()->dateTimeBetween('-1 year', 'now');
            
            return [
                'tipo_contenido_id' => $tipoContenido->id,
                'numero' => $numero,
                'fecha_emision' => $fechaEmision,
                'ruta_archivo' => 'storage/circulares/' . date('Y') . '/' . Str::slug($numero) . '.pdf',
                'nombre_archivo' => Str::slug($numero) . '.pdf',
                'cuerpo' => fake()->paragraphs(5, true),
                'estado' => 'publicado',
            ];
        });
    }

    /**
     * Tipo: Acta
     *
     * @return static
     */
    public function acta(): static
    {
        return $this->state(function (array $attributes) {
            $tipoContenido = TipoContenido::where('slug', 'acta')->first() 
                ?? TipoContenido::factory()->create(['slug' => 'acta', 'nombre' => 'Acta']);
            
            $numero = 'ACTA-' . fake()->unique()->numberBetween(1, 999) . '-' . date('Y');
            $fechaEmision = fake()->dateTimeBetween('-1 year', 'now');
            
            return [
                'tipo_contenido_id' => $tipoContenido->id,
                'numero' => $numero,
                'fecha_emision' => $fechaEmision,
                'tipo_reunion' => fake()->randomElement(['ordinaria', 'extraordinaria', 'junta_directiva']),
                'asistentes' => [
                    ['nombre' => fake()->name(), 'cargo' => fake()->jobTitle()],
                    ['nombre' => fake()->name(), 'cargo' => fake()->jobTitle()],
                    ['nombre' => fake()->name(), 'cargo' => fake()->jobTitle()],
                ],
                'ubicacion' => fake()->company(),
                'ruta_archivo' => 'storage/actas/' . date('Y') . '/' . Str::slug($numero) . '.pdf',
                'nombre_archivo' => Str::slug($numero) . '.pdf',
                'cuerpo' => fake()->paragraphs(6, true),
                'estado' => 'publicado',
            ];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | TIPOS ESPECÍFICOS - TRANSPARENCIA
    |--------------------------------------------------------------------------
    */

    /**
     * Tipo: Contrato
     *
     * @return static
     */
    public function contrato(): static
    {
        return $this->state(function (array $attributes) {
            $tipoContenido = TipoContenido::where('slug', 'contrato')->first() 
                ?? TipoContenido::factory()->create(['slug' => 'contrato', 'nombre' => 'Contrato']);
            
            $numero = 'CONT-' . fake()->unique()->numberBetween(1, 9999) . '-' . date('Y');
            $fechaInicio = fake()->dateTimeBetween('-1 year', 'now');
            
            return [
                'tipo_contenido_id' => $tipoContenido->id,
                'numero' => $numero,
                'nombre_contratista' => fake()->company(),
                'identificacion_contratista' => fake()->unique()->numerify('###########'),
                'tipo_contrato' => fake()->randomElement(['obra', 'servicios', 'suministro', 'consultoria']),
                'monto' => fake()->randomFloat(2, 1000000, 50000000),
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => fake()->dateTimeBetween($fechaInicio, '+2 years'),
                'url_secop' => 'https://www.colombiacompra.gov.co/contrato/' . $numero,
                'ruta_archivo' => 'storage/contratos/' . date('Y') . '/' . Str::slug($numero) . '.pdf',
                'nombre_archivo' => Str::slug($numero) . '.pdf',
                'cuerpo' => fake()->paragraphs(5, true),
                'estado' => 'publicado',
            ];
        });
    }
}
