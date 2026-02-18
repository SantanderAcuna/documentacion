<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory para el modelo Categoria
 */
class CategoriaFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     *
     * @var string
     */
    protected $model = Categoria::class;

    /**
     * Definir el estado por defecto del modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = fake()->unique()->words(2, true);

        return [
            'nombre' => ucfirst($nombre),
            'slug' => Str::slug($nombre),
            'descripcion' => fake()->sentence(),
            'padre_id' => null,
            'color' => fake()->hexColor(),
            'icono' => fake()->randomElement(['fa-folder', 'fa-tag', 'fa-bookmark', 'fa-star']),
            'orden' => fake()->numberBetween(1, 100),
            'esta_activo' => fake()->boolean(95),
        ];
    }

    /**
     * Categoría raíz (sin padre)
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
     * Categoría con padre
     *
     * @param int|null $padreId
     * @return static
     */
    public function conPadre(?int $padreId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'padre_id' => $padreId ?? Categoria::factory(),
        ]);
    }

    /**
     * Categoría inactiva
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
