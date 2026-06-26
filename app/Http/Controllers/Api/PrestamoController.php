<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrestamoRequest;
use App\Http\Resources\PrestamoResource;
use App\Repositories\Contracts\PrestamoRepositoryInterface;
use App\Services\PrestamoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PrestamoController extends Controller
{
    public function __construct(
        private PrestamoService $service,
        private PrestamoRepositoryInterface $repository,
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $porPagina = (int) $request->input('per_page', 15);

        return PrestamoResource::collection(
            $this->repository->paginarConRelaciones($porPagina)
        );
    }

    public function store(StorePrestamoRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $prestamo = $this->service->crearPrestamo(
            (int) $datos['usuario_id'],
            (int) $datos['libro_id'],
            $datos['fecha_prestamo'] ?? null
        );

        $prestamo->load(['usuario', 'libro']);

        return response()->json(
            ['data' => new PrestamoResource($prestamo)],
            Response::HTTP_CREATED
        );
    }

    public function devolver(int $id): JsonResponse
    {
        $prestamo = $this->service->devolverPrestamo($id);

        return response()->json(['data' => new PrestamoResource($prestamo)]);
    }
}
