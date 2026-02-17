<?php

namespace Database\Factories;

use App\Models\Contenido;
use App\Models\TipoContenido;
use App\Models\Dependencia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory para el modelo Contenido
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
}
