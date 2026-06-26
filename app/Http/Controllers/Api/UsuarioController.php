<?php

namespace App\Http\Controllers\Api;

use App\Domain\Enums\EstadoUsuario;
use App\Http\Controllers\Controller;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UsuarioController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Usuario::query();

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        $porPagina = (int) $request->input('per_page', 15);

        return UsuarioResource::collection(
            $query->orderBy('nombre')->paginate($porPagina)
        );
    }
}
