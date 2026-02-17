<?php

namespace Database\Factories;

use App\Models\Dependencia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory para el modelo Dependencia
 */
class DependenciaFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     *
     * @var string
     */
    protected $model = Dependencia::class;

    /**
     * Definir el estado por defecto del modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = fake()->unique()->randomElement([
            'Secretaría de Hacienda',
            'Secretaría de Gobierno',
            'Secretaría de Educación',
            'Secretaría de Salud',
            'Secretaría de Planeación',
            'Secretaría de Infraestructura',
            'Secretaría de Desarrollo Social',
            'Secretaría de Turismo',
            'Secretaría de Medio Ambiente',
            'Oficina de Control Interno',
            'Oficina Asesora Jurídica',
            'Oficina de Prensa y Comunicaciones',
            'Departamento Administrativo',
            'Dirección de Sistemas',
            'Dirección de Talento Humano',
        ]);

        return [
            'nombre' => $nombre,
            'codigo' => strtoupper(Str::slug(substr($nombre, 0, 10))),
            'descripcion' => fake()->paragraph(),
            'padre_id' => null, // Se puede establecer después para crear jerarquías
            'telefono' => fake()->phoneNumber(),
            'correo' => fake()->unique()->safeEmail(),
            'ubicacion' => fake()->address(),
            'esta_activo' => fake()->boolean(90), // 90% activas
            'orden' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicar que la dependencia es raíz (sin padre)
     *
     * @return static
     */
    public function raiz(): static
    {
        return $this->state(fn (array $attributes) => [
            'padre_id' => null,
        ]);
    }

    /**
     * Indicar que la dependencia tiene un padre
     *
     * @param int|null $padreId
     * @return static
     */
    public function conPadre(?int $padreId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'padre_id' => $padreId ?? Dependencia::factory(),
        ]);
    }

    /**
     * Indicar que la dependencia está inactiva
     *
     * @return static
     */
    public function inactiva(): static
    {
        return $this->state(fn (array $attributes) => [
            'esta_activo' => false,
        ]);
    }
}
