<?php

namespace Database\Factories;

use App\Models\SolicitudPqrs;
use App\Models\Dependencia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo SolicitudPqrs
 */
class SolicitudPqrsFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     *
     * @var string
     */
    protected $model = SolicitudPqrs::class;

    /**
     * Definir el estado por defecto del modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_radicado' => 'PQRS-' . date('Y') . '-' . fake()->unique()->numberBetween(1, 99999),
            'tipo_solicitud' => fake()->randomElement(['peticion', 'queja', 'reclamo', 'sugerencia']),
            'nombre_ciudadano' => fake()->name(),
            'correo_ciudadano' => fake()->safeEmail(),
            'telefono_ciudadano' => fake()->phoneNumber(),
            'asunto' => fake()->sentence(),
            'descripcion' => fake()->paragraph(),
            'estado' => fake()->randomElement(['recibida', 'en_proceso', 'respondida', 'cerrada']),
            'prioridad' => fake()->randomElement(['baja', 'media', 'alta']),
            'dependencia_id' => Dependencia::factory(),
            'radicado_en' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Solicitud recibida
     *
     * @return static
     */
    public function recibida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'recibida',
        ]);
    }

    /**
     * Solicitud en proceso
     *
     * @return static
     */
    public function enProceso(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'en_proceso',
        ]);
    }

    /**
     * Solicitud respondida
     *
     * @return static
     */
    public function respondida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'respondida',
        ]);
    }
}
