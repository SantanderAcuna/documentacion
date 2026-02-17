<?php

namespace Database\Factories;

use App\Models\Medio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Medio
 */
class MedioFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente a la factory
     *
     * @var string
     */
    protected $model = Medio::class;

    /**
     * Definir el estado por defecto del modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombreArchivo = fake()->slug() . '.jpg';

        return [
            'nombre' => fake()->words(3, true),
            'nombre_archivo' => $nombreArchivo,
            'tipo_mime' => 'image/jpeg',
            'disco' => 'public',
            'ruta' => 'medios/' . date('Y') . '/' . $nombreArchivo,
            'tamano' => fake()->numberBetween(50000, 5000000),
            'tipo_medio' => 'imagen',
            'texto_alternativo' => fake()->sentence(),
        ];
    }

    /**
     * Medio de tipo imagen
     *
     * @return static
     */
    public function imagen(): static
    {
        return $this->state(function (array $attributes) {
            $nombreArchivo = fake()->slug() . '.jpg';
            
            return [
                'nombre_archivo' => $nombreArchivo,
                'tipo_mime' => 'image/jpeg',
                'ruta' => 'medios/' . date('Y') . '/' . $nombreArchivo,
                'tipo_medio' => 'imagen',
            ];
        });
    }

    /**
     * Medio de tipo video
     *
     * @return static
     */
    public function video(): static
    {
        return $this->state(function (array $attributes) {
            $nombreArchivo = fake()->slug() . '.mp4';
            
            return [
                'nombre_archivo' => $nombreArchivo,
                'tipo_mime' => 'video/mp4',
                'ruta' => 'medios/' . date('Y') . '/' . $nombreArchivo,
                'tipo_medio' => 'video',
                'tamano' => fake()->numberBetween(1000000, 50000000),
            ];
        });
    }

    /**
     * Medio de tipo documento
     *
     * @return static
     */
    public function documento(): static
    {
        return $this->state(function (array $attributes) {
            $nombreArchivo = fake()->slug() . '.pdf';
            
            return [
                'nombre_archivo' => $nombreArchivo,
                'tipo_mime' => 'application/pdf',
                'ruta' => 'medios/' . date('Y') . '/' . $nombreArchivo,
                'tipo_medio' => 'documento',
                'tamano' => fake()->numberBetween(100000, 10000000),
            ];
        });
    }

    /**
     * Medio de tipo audio
     *
     * @return static
     */
    public function audio(): static
    {
        return $this->state(function (array $attributes) {
            $nombreArchivo = fake()->slug() . '.mp3';
            
            return [
                'nombre_archivo' => $nombreArchivo,
                'tipo_mime' => 'audio/mpeg',
                'ruta' => 'medios/' . date('Y') . '/' . $nombreArchivo,
                'tipo_medio' => 'audio',
                'tamano' => fake()->numberBetween(500000, 15000000),
            ];
        });
    }
}
