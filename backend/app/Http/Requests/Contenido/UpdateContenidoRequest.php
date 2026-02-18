<?php

declare(strict_types=1);

namespace App\Http\Requests\Contenido;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * Request de validación para actualizar contenido
 * 
 * Valida todos los datos de entrada para actualización de contenidos.
 * Todos los campos son opcionales para permitir actualizaciones parciales.
 */
final class UpdateContenidoRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para esta petición
     */
    public function authorize(): bool
    {
        // La autorización se maneja en Policies
        return true;
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        $contenidoId = $this->route('contenido') ?? $this->route('id');

        return [
            'tipo_contenido_id' => ['sometimes', 'integer', 'exists:tipos_contenido,id'],
            'dependencia_id' => ['nullable', 'integer', 'exists:dependencias,id'],
            'titulo' => ['sometimes', 'string', 'min:3', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('contenidos', 'slug')->ignore($contenidoId)],
            'resumen' => ['nullable', 'string', 'max:500'],
            'cuerpo' => ['nullable', 'string'],
            'imagen_destacada' => ['nullable', 'string', 'max:500'],
            'numero' => ['nullable', 'string', 'max:50'],
            'fecha_emision' => ['nullable', 'date'],
            'fecha_publicacion' => ['nullable', 'date'],
            'estado' => ['nullable', 'string', 'in:borrador,revision,publicado,archivado'],
            'es_destacado' => ['nullable', 'boolean'],
            'comentarios_habilitados' => ['nullable', 'boolean'],
            'idioma' => ['nullable', 'string', 'size:2', 'in:es,en,fr,pt'],
            'metadatos' => ['nullable', 'array'],
        ];
    }

    /**
     * Mensajes de error personalizados en español
     */
    public function messages(): array
    {
        return [
            'tipo_contenido_id.integer' => 'El tipo de contenido debe ser un número entero.',
            'tipo_contenido_id.exists' => 'El tipo de contenido seleccionado no existe.',
            
            'dependencia_id.integer' => 'La dependencia debe ser un número entero.',
            'dependencia_id.exists' => 'La dependencia seleccionada no existe.',
            
            'titulo.string' => 'El título debe ser una cadena de texto.',
            'titulo.min' => 'El título debe tener al menos :min caracteres.',
            'titulo.max' => 'El título no puede tener más de :max caracteres.',
            
            'slug.string' => 'El slug debe ser una cadena de texto.',
            'slug.max' => 'El slug no puede tener más de :max caracteres.',
            'slug.unique' => 'Este slug ya está en uso.',
            
            'resumen.string' => 'El resumen debe ser una cadena de texto.',
            'resumen.max' => 'El resumen no puede tener más de :max caracteres.',
            
            'cuerpo.string' => 'El cuerpo debe ser una cadena de texto.',
            
            'imagen_destacada.string' => 'La imagen destacada debe ser una cadena de texto.',
            'imagen_destacada.max' => 'La imagen destacada no puede tener más de :max caracteres.',
            
            'numero.string' => 'El número debe ser una cadena de texto.',
            'numero.max' => 'El número no puede tener más de :max caracteres.',
            
            'fecha_emision.date' => 'La fecha de emisión debe ser una fecha válida.',
            'fecha_publicacion.date' => 'La fecha de publicación debe ser una fecha válida.',
            
            'estado.string' => 'El estado debe ser una cadena de texto.',
            'estado.in' => 'El estado debe ser: borrador, revisión, publicado o archivado.',
            
            'es_destacado.boolean' => 'El campo destacado debe ser verdadero o falso.',
            'comentarios_habilitados.boolean' => 'El campo comentarios habilitados debe ser verdadero o falso.',
            
            'idioma.string' => 'El idioma debe ser una cadena de texto.',
            'idioma.size' => 'El idioma debe tener exactamente :size caracteres.',
            'idioma.in' => 'El idioma debe ser: es, en, fr o pt.',
            
            'metadatos.array' => 'Los metadatos deben ser un arreglo.',
        ];
    }

    /**
     * Atributos personalizados para mensajes
     */
    public function attributes(): array
    {
        return [
            'tipo_contenido_id' => 'tipo de contenido',
            'dependencia_id' => 'dependencia',
            'titulo' => 'título',
            'slug' => 'slug',
            'resumen' => 'resumen',
            'cuerpo' => 'cuerpo',
            'imagen_destacada' => 'imagen destacada',
            'numero' => 'número',
            'fecha_emision' => 'fecha de emisión',
            'fecha_publicacion' => 'fecha de publicación',
            'estado' => 'estado',
            'es_destacado' => 'destacado',
            'comentarios_habilitados' => 'comentarios habilitados',
            'idioma' => 'idioma',
            'metadatos' => 'metadatos',
        ];
    }

    /**
     * Manejar fallo de validación
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
