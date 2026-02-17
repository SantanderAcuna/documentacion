<?php

namespace Database\Factories;

use App\Models\Noticia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory para el modelo Noticia
 */
class NoticiaFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     *
     * @var string
     */
    protected $model = Noticia::class;

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
            'usuario_id' => User::factory(),
            'titulo' => $titulo,
            'slug' => Str::slug($titulo),
            'resumen' => fake()->paragraph(),
            'contenido' => fake()->paragraphs(6, true),
            'estado' => $estado,
            'publicado_en' => $estado === 'publicado' ? fake()->dateTimeBetween('-1 year', 'now') : null,
            'conteo_vistas' => fake()->numberBetween(0, 10000),
            'es_destacado' => fake()->boolean(15),
        ];
    }

    /**
     * Noticia publicada
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
     * Noticia destacada
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
