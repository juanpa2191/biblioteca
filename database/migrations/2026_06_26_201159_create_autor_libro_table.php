<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutorLibroTable extends Migration
{
    public function up()
    {
        Schema::create('autor_libro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')->constrained('autores')->cascadeOnDelete();
            $table->foreignId('libro_id')->constrained('libros')->cascadeOnDelete();
            $table->unsignedSmallInteger('orden_autor')->default(1);
            $table->timestamps();

            $table->unique(['autor_id', 'libro_id']);
            $table->index(['libro_id', 'orden_autor']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('autor_libro');
    }
}
