<?php

namespace App\Exceptions;

use App\Domain\Exceptions\AutorConLibrosException;
use App\Domain\Exceptions\LimitePrestamosExcedidoException;
use App\Domain\Exceptions\PrestamoYaDevueltoException;
use App\Domain\Exceptions\StockInsuficienteException;
use App\Domain\Exceptions\UsuarioInactivoException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        StockInsuficienteException::class,
        LimitePrestamosExcedidoException::class,
        AutorConLibrosException::class,
        UsuarioInactivoException::class,
        PrestamoYaDevueltoException::class,
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->renderable(function (StockInsuficienteException $e, Request $request) {
            if ($request->is('api/*')) {
                return $this->businessError($e->getMessage(), 'stock_insuficiente');
            }
        });

        $this->renderable(function (LimitePrestamosExcedidoException $e, Request $request) {
            if ($request->is('api/*')) {
                return $this->businessError($e->getMessage(), 'limite_prestamos_excedido');
            }
        });

        $this->renderable(function (AutorConLibrosException $e, Request $request) {
            if ($request->is('api/*')) {
                return $this->businessError($e->getMessage(), 'autor_con_libros', 409);
            }
        });

        $this->renderable(function (UsuarioInactivoException $e, Request $request) {
            if ($request->is('api/*')) {
                return $this->businessError($e->getMessage(), 'usuario_inactivo');
            }
        });

        $this->renderable(function (PrestamoYaDevueltoException $e, Request $request) {
            if ($request->is('api/*')) {
                return $this->businessError($e->getMessage(), 'prestamo_ya_devuelto', 409);
            }
        });

        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Recurso no encontrado.',
                    'error' => 'not_found',
                ], 404);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Ruta o recurso no encontrado.',
                    'error' => 'not_found',
                ], 404);
            }
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'No autenticado.',
                    'error' => 'unauthenticated',
                ], 401);
            }
        });
    }

    private function businessError(string $message, string $code, int $status = 422): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'error' => $code,
        ], $status);
    }
}
