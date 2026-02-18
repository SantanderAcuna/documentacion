<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        Schema::connection(config('activitylog.database_connection'))->create(config('activitylog.table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre_registro')->nullable();
            $table->text('descripcion');
            $table->nullableMorphs('sujeto', 'sujeto');
            $table->nullableMorphs('causante', 'causante');
            $table->json('propiedades')->nullable();
            $table->timestamps();
            $table->index('nombre_registro');
        });
    }

    public function down()
    {
        Schema::connection(config('activitylog.database_connection'))->dropIfExists(config('activitylog.table_name'));
    }
}
