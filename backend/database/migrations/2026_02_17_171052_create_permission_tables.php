<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'rol_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permiso_id';

        throw_if(empty($tableNames), 'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), 'Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');

        /**
         * Ver `docs/prerequisites.md` para longitudes sugeridas en 'nombre' y 'nombre_guarda' si se encuentran errores "1071 Specified key was too long".
         */
        Schema::create($tableNames['permissions'], static function (Blueprint $table) {
            $table->id(); // id permiso
            $table->string('nombre');
            $table->string('nombre_guarda');
            $table->timestamps();

            $table->unique(['nombre', 'nombre_guarda']);
        });

        /**
         * Ver `docs/prerequisites.md` para longitudes sugeridas en 'nombre' y 'nombre_guarda' si se encuentran errores "1071 Specified key was too long".
         */
        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
            $table->id(); // id rol
            if ($teams || config('permission.testing')) { // permission.testing es una solución para pruebas sqlite
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('nombre');
            $table->string('nombre_guarda');
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'nombre', 'nombre_guarda']);
            } else {
                $table->unique(['nombre', 'nombre_guarda']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->unsignedBigInteger($pivotPermission);

            $table->string('tipo_modelo');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'tipo_modelo'], 'modelo_tiene_permisos_modelo_id_tipo_modelo_index');

            $table->foreign($pivotPermission)
                ->references('id') // id permiso
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'modelo_tiene_permisos_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'tipo_modelo'],
                    'modelo_tiene_permisos_permiso_tipo_modelo_primary');
            } else {
                $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'tipo_modelo'],
                    'modelo_tiene_permisos_permiso_tipo_modelo_primary');
            }
        });

        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->unsignedBigInteger($pivotRole);

            $table->string('tipo_modelo');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'tipo_modelo'], 'modelo_tiene_roles_modelo_id_tipo_modelo_index');

            $table->foreign($pivotRole)
                ->references('id') // id rol
                ->on($tableNames['roles'])
                ->cascadeOnDelete();
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'modelo_tiene_roles_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'tipo_modelo'],
                    'modelo_tiene_roles_rol_tipo_modelo_primary');
            } else {
                $table->primary([$pivotRole, $columnNames['model_morph_key'], 'tipo_modelo'],
                    'modelo_tiene_roles_rol_tipo_modelo_primary');
            }
        });

        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id') // id permiso
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            $table->foreign($pivotRole)
                ->references('id') // id rol
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            $table->primary([$pivotPermission, $pivotRole], 'rol_tiene_permisos_permiso_id_rol_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        throw_if(empty($tableNames), 'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
