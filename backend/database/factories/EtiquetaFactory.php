<?php

namespace Database\Factories;

use App\Models\Etiqueta;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory para el modelo Etiqueta
 */
class EtiquetaFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     *
     * @var string
     */
    protected $model = Etiqueta::class;

    /**
     * Definir el estado por defecto del modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = fake()->unique()->word();

        return [
            'nombre' => ucfirst($nombre),
            'slug' => Str::slug($nombre),
            'color' => fake()->hexColor(),
        ];
    }
}
