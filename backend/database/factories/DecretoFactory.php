<?php

namespace Database\Factories;

use App\Models\Decreto;
use App\Models\Dependencia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Decreto
 */
class DecretoFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     *
     * @var string
     */
    protected $model = Decreto::class;

    /**
     * Definir el estado por defecto del modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fechaEmision = fake()->dateTimeBetween('-2 years', 'now');
        $numeroDecreto = fake()->unique()->numberBetween(1, 999);

        return [
            'numero' => 'DECRETO-' . $numeroDecreto . '-' . date('Y'),
            'titulo' => fake()->sentence(),
            'resumen' => fake()->paragraph(),
            'contenido' => fake()->paragraphs(8, true),
            'fecha_emision' => $fechaEmision,
            'fecha_publicacion' => fake()->dateTimeBetween($fechaEmision, 'now'),
            'dependencia_id' => Dependencia::factory(),
            'usuario_id' => User::factory(),
            'ruta_archivo' => 'decretos/' . date('Y') . '/decreto.pdf',
            'nombre_archivo' => 'decreto-' . $numeroDecreto . '.pdf',
            'estado' => 'publicado',
        ];
    }

    /**
     * Decreto publicado
     *
     * @return static
     */
    public function publicado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'publicado',
        ]);
    }

    /**
     * Decreto en borrador
     *
     * @return static
     */
    public function borrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'borrador',
        ]);
    }
}
