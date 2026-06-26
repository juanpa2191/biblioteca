<?php

use App\Domain\Enums\EstadoPrestamo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePrestamosTable extends Migration
{
    public function up()
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->foreignId('libro_id')->constrained('libros')->restrictOnDelete();
            $table->date('fecha_prestamo');
            $table->date('fecha_devolucion_estimada');
            $table->date('fecha_devolucion_real')->nullable();
            $table->string('estado', 20)->default(EstadoPrestamo::ACTIVO);
            $table->timestamps();

            $table->index('estado');
            $table->index('fecha_devolucion_estimada');
            $table->index(['usuario_id', 'estado']);
        });

        $estados = "'" . implode("','", EstadoPrestamo::all()) . "'";
        DB::statement("ALTER TABLE prestamos ADD CONSTRAINT prestamos_estado_check CHECK (estado IN ($estados))");
    }

    public function down()
    {
        Schema::dropIfExists('prestamos');
    }
}
