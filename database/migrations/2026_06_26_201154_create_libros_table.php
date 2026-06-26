<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLibrosTable extends Migration
{
    public function up()
    {
        Schema::create('libros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 255);
            $table->string('isbn', 20)->unique();
            $table->smallInteger('anio_publicacion');
            $table->unsignedInteger('numero_paginas')->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('stock_disponible')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('titulo');
            $table->index('anio_publicacion');
        });

        DB::statement('ALTER TABLE libros ADD CONSTRAINT libros_stock_no_negativo_check CHECK (stock_disponible >= 0)');
    }

    public function down()
    {
        Schema::dropIfExists('libros');
    }
}
