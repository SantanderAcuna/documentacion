<?php

namespace Database\Factories;

use App\Models\Contrato;
use App\Models\Dependencia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Contrato
 */
class ContratoFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     *
     * @var string
     */
    protected $model = Contrato::class;

    /**
     * Definir el estado por defecto del modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fechaInicio = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'numero_contrato' => 'CONT-' . fake()->unique()->numberBetween(1, 9999) . '-' . date('Y'),
            'titulo' => fake()->sentence(),
            'nombre_contratista' => fake()->company(),
            'identificacion_contratista' => fake()->unique()->numerify('###########'),
            'tipo_contrato' => fake()->randomElement(['obra', 'servicios', 'suministro', 'consultoria']),
            'monto' => fake()->randomFloat(2, 1000000, 50000000),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => fake()->dateTimeBetween($fechaInicio, '+2 years'),
            'descripcion' => fake()->paragraph(),
            'estado' => 'activo',
            'dependencia_id' => Dependencia::factory(),
            'usuario_id' => User::factory(),
        ];
    }

    /**
     * Contrato activo
     *
     * @return static
     */
    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'activo',
        ]);
    }

    /**
     * Contrato completado
     *
     * @return static
     */
    public function completado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'completado',
        ]);
    }

    /**
     * Contrato terminado
     *
     * @return static
     */
    public function terminado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'terminado',
        ]);
    }
}
