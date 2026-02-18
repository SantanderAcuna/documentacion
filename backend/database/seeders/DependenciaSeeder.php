<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dependencia;

/**
 * Seeder para Dependencias
 * 
 * Crea la estructura organizacional de la Alcaldía de Santa Marta
 */
class DependenciaSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        // Despacho del Alcalde (Raíz)
        $despacho = Dependencia::create([
            'nombre' => 'Despacho del Alcalde',
            'codigo' => 'DESP-ALC',
            'descripcion' => 'Despacho principal de la Alcaldía Distrital de Santa Marta',
            'padre_id' => null,
            'telefono' => '(57) 5 4381780',
            'correo' => 'despacho@santamarta.gov.co',
            'ubicacion' => 'Edificio Alcaldía - Piso 3',
            'esta_activo' => true,
            'orden' => 1,
        ]);

        // Secretarías principales
        $secretarias = [
            [
                'nombre' => 'Secretaría de Gobierno',
                'codigo' => 'SEC-GOB',
                'descripcion' => 'Gestión de gobierno y orden público',
                'telefono' => '(57) 5 4381781',
                'correo' => 'gobierno@santamarta.gov.co',
            ],
            [
                'nombre' => 'Secretaría de Hacienda',
                'codigo' => 'SEC-HAC',
                'descripcion' => 'Gestión financiera y presupuestal',
                'telefono' => '(57) 5 4381782',
                'correo' => 'hacienda@santamarta.gov.co',
            ],
            [
                'nombre' => 'Secretaría de Planeación',
                'codigo' => 'SEC-PLA',
                'descripcion' => 'Planificación y desarrollo territorial',
                'telefono' => '(57) 5 4381783',
                'correo' => 'planeacion@santamarta.gov.co',
            ],
            [
                'nombre' => 'Secretaría de Educación',
                'codigo' => 'SEC-EDU',
                'descripcion' => 'Gestión del sistema educativo distrital',
                'telefono' => '(57) 5 4381784',
                'correo' => 'educacion@santamarta.gov.co',
            ],
            [
                'nombre' => 'Secretaría de Salud',
                'codigo' => 'SEC-SAL',
                'descripcion' => 'Gestión del sistema de salud',
                'telefono' => '(57) 5 4381785',
                'correo' => 'salud@santamarta.gov.co',
            ],
            [
                'nombre' => 'Secretaría de Infraestructura',
                'codigo' => 'SEC-INF',
                'descripcion' => 'Obras públicas y desarrollo vial',
                'telefono' => '(57) 5 4381786',
                'correo' => 'infraestructura@santamarta.gov.co',
            ],
            [
                'nombre' => 'Secretaría de Desarrollo Social',
                'codigo' => 'SEC-DES',
                'descripcion' => 'Programas sociales y comunitarios',
                'telefono' => '(57) 5 4381787',
                'correo' => 'desarrollo@santamarta.gov.co',
            ],
            [
                'nombre' => 'Secretaría de Medio Ambiente',
                'codigo' => 'SEC-AMB',
                'descripcion' => 'Gestión ambiental y sostenibilidad',
                'telefono' => '(57) 5 4381788',
                'correo' => 'ambiente@santamarta.gov.co',
            ],
        ];

        foreach ($secretarias as $index => $secretaria) {
            Dependencia::create(array_merge($secretaria, [
                'padre_id' => $despacho->id,
                'ubicacion' => 'Edificio Alcaldía - Piso ' . ($index + 1),
                'esta_activo' => true,
                'orden' => $index + 2,
            ]));
        }

        // Oficinas asesoras
        $oficinas = [
            [
                'nombre' => 'Oficina Asesora Jurídica',
                'codigo' => 'OF-JUR',
                'descripcion' => 'Asesoría legal y defensa judicial',
                'correo' => 'juridica@santamarta.gov.co',
            ],
            [
                'nombre' => 'Oficina de Control Interno',
                'codigo' => 'OF-CI',
                'descripcion' => 'Control interno y auditoría',
                'correo' => 'control@santamarta.gov.co',
            ],
            [
                'nombre' => 'Oficina de Prensa y Comunicaciones',
                'codigo' => 'OF-COM',
                'descripcion' => 'Comunicaciones institucionales',
                'correo' => 'prensa@santamarta.gov.co',
            ],
        ];

        foreach ($oficinas as $index => $oficina) {
            Dependencia::create(array_merge($oficina, [
                'padre_id' => $despacho->id,
                'telefono' => '(57) 5 438' . (1790 + $index),
                'ubicacion' => 'Edificio Alcaldía - Piso 2',
                'esta_activo' => true,
                'orden' => 100 + $index,
            ]));
        }

        $this->command->info('✅ Dependencias creadas: ' . (1 + count($secretarias) + count($oficinas)));
    }
}
