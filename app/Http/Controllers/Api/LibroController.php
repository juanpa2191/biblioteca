<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLibroRequest;
use App\Http\Requests\UpdateLibroRequest;
use App\Http\Resources\LibroResource;
use App\Models\Libro;
use App\Services\LibroService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class LibroController extends Controller
{
    public function __construct(private LibroService $service)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $filtros = $request->only(['titulo', 'autor', 'anio', 'disponibles']);
        $porPagina = (int) $request->input('per_page', 15);

        $libros = $this->service->listarConFiltros($filtros, $porPagina);

        return LibroResource::collection($libros);
    }

    public function show(int $id): JsonResponse
    {
        $libro = $this->service->obtenerConAutores($id);

        if (!$libro) {
            return response()->json(['message' => 'Libro no encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => new LibroResource($libro)]);
    }

    public function store(StoreLibroRequest $request): JsonResponse
    {
        $datos = $request->validated();
        $autorIds = $datos['autor_ids'];
        unset($datos['autor_ids']);

        $libro = $this->service->crear($datos, $autorIds);

        return response()->json(
            ['data' => new LibroResource($libro)],
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateLibroRequest $request, Libro $libro): JsonResponse
    {
        $datos = $request->validated();
        $autorIds = $datos['autor_ids'] ?? null;
        unset($datos['autor_ids']);

        $actualizado = $this->service->actualizar($libro, $datos, $autorIds);

        return response()->json(['data' => new LibroResource($actualizado)]);
    }

    public function destroy(Libro $libro): Response
    {
        $this->service->eliminar($libro);

        return response()->noContent();
    }
}
