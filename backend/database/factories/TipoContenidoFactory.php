<?php

namespace Database\Factories;

use App\Models\TipoContenido;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory para el modelo TipoContenido
 */
class TipoContenidoFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     *
     * @var string
     */
    protected $model = TipoContenido::class;

    /**
     * Definir el estado por defecto del modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = fake()->unique()->randomElement([
            'Post',
            'Blog',
            'Noticia',
            'Página',
            'Evento',
            'Anuncio',
            'Comunicado',
        ]);

        return [
            'nombre' => $nombre,
            'slug' => Str::slug($nombre),
            'descripcion' => fake()->sentence(),
            'icono' => fake()->randomElement(['fa-file', 'fa-newspaper', 'fa-calendar', 'fa-bullhorn']),
            'esta_activo' => true,
        ];
    }

    /**
     * Tipo de contenido inactivo
     *
     * @return static
     */
    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'esta_activo' => false,
        ]);
    }
}
