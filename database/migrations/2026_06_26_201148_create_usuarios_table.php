<?php

use App\Domain\Enums\EstadoUsuario;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('email', 150)->unique();
            $table->string('telefono', 30)->nullable();
            $table->date('fecha_registro');
            $table->string('estado', 20)->default(EstadoUsuario::ACTIVO);
            $table->timestamps();
        });

        $estados = "'" . implode("','", EstadoUsuario::all()) . "'";
        DB::statement("ALTER TABLE usuarios ADD CONSTRAINT usuarios_estado_check CHECK (estado IN ($estados))");
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}
