<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAutorRequest;
use App\Http\Requests\UpdateAutorRequest;
use App\Http\Resources\AutorResource;
use App\Models\Autor;
use App\Services\AutorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AutorController extends Controller
{
    public function __construct(private AutorService $service)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $porPagina = (int) $request->input('per_page', 15);

        return AutorResource::collection($this->service->listar($porPagina));
    }

    public function show(int $id): JsonResponse
    {
        $autor = $this->service->obtener($id);

        if (!$autor) {
            return response()->json(['message' => 'Autor no encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => new AutorResource($autor)]);
    }

    public function store(StoreAutorRequest $request): JsonResponse
    {
        $autor = $this->service->crear($request->validated());

        return response()->json(
            ['data' => new AutorResource($autor)],
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateAutorRequest $request, Autor $autor): JsonResponse
    {
        $actualizado = $this->service->actualizar($autor, $request->validated());

        return response()->json(['data' => new AutorResource($actualizado)]);
    }

    public function destroy(Autor $autor): Response
    {
        $this->service->eliminar($autor);

        return response()->noContent();
    }
}
