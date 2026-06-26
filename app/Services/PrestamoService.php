<?php

namespace App\Services;

use App\Domain\Enums\EstadoPrestamo;
use App\Domain\Exceptions\LimitePrestamosExcedidoException;
use App\Domain\Exceptions\PrestamoYaDevueltoException;
use App\Domain\Exceptions\StockInsuficienteException;
use App\Domain\Exceptions\UsuarioInactivoException;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use App\Repositories\Contracts\LibroRepositoryInterface;
use App\Repositories\Contracts\PrestamoRepositoryInterface;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PrestamoService
{
    public const DIAS_PRESTAMO_DEFAULT = 14;

    public function __construct(
        private PrestamoRepositoryInterface $prestamos,
        private LibroRepositoryInterface $libros,
        private UsuarioRepositoryInterface $usuarios,
    ) {
    }

    /**
     * Crea un préstamo aplicando todas las reglas de negocio.
     *
     * @throws StockInsuficienteException
     * @throws LimitePrestamosExcedidoException
     * @throws UsuarioInactivoException
     */
    public function crearPrestamo(int $usuarioId, int $libroId, ?string $fechaPrestamo = null): Prestamo
    {
        return DB::transaction(function () use ($usuarioId, $libroId, $fechaPrestamo) {
            $usuario = $this->usuarios->buscarPorId($usuarioId);
            if (!$usuario) {
                throw new RuntimeException("Usuario (id={$usuarioId}) no encontrado.");
            }
            if (!$usuario->estaActivo()) {
                throw UsuarioInactivoException::paraUsuario($usuarioId);
            }

            $activos = $this->prestamos->contarActivosPorUsuario($usuarioId);
            if ($activos >= LimitePrestamosExcedidoException::LIMITE) {
                throw LimitePrestamosExcedidoException::paraUsuario($usuarioId, $activos);
            }

            $libro = Libro::query()->lockForUpdate()->find($libroId);
            if (!$libro) {
                throw new RuntimeException("Libro (id={$libroId}) no encontrado.");
            }

            if ($libro->stock_disponible <= 0) {
                throw StockInsuficienteException::paraLibro($libro->id, $libro->titulo);
            }

            $decrementado = $this->libros->decrementarStock($libro, 1);
            if (!$decrementado) {
                throw StockInsuficienteException::paraLibro($libro->id, $libro->titulo);
            }

            $base = $fechaPrestamo
                ? CarbonImmutable::parse($fechaPrestamo)
                : CarbonImmutable::today();

            return $this->prestamos->crear([
                'usuario_id' => $usuarioId,
                'libro_id' => $libroId,
                'fecha_prestamo' => $base->toDateString(),
                'fecha_devolucion_estimada' => $base->addDays(self::DIAS_PRESTAMO_DEFAULT)->toDateString(),
                'fecha_devolucion_real' => null,
                'estado' => EstadoPrestamo::ACTIVO,
            ]);
        });
    }

    /**
     * Devuelve un préstamo y reincrementa el stock.
     *
     * @throws PrestamoYaDevueltoException
     */
    public function devolverPrestamo(int $prestamoId): Prestamo
    {
        return DB::transaction(function () use ($prestamoId) {
            $prestamo = $this->prestamos->buscarPorId($prestamoId);
            if (!$prestamo) {
                throw new RuntimeException("Préstamo (id={$prestamoId}) no encontrado.");
            }

            if ($prestamo->fueDevuelto()) {
                throw PrestamoYaDevueltoException::paraPrestamo($prestamoId);
            }

            $this->prestamos->actualizar($prestamo, [
                'estado' => EstadoPrestamo::DEVUELTO,
                'fecha_devolucion_real' => CarbonImmutable::today()->toDateString(),
            ]);

            $libro = $prestamo->libro;
            if ($libro) {
                $this->libros->incrementarStock($libro, 1);
            }

            return $prestamo->refresh()->load(['usuario', 'libro']);
        });
    }

    /**
     * Marca como vencidos los préstamos atrasados más de N días.
     */
    public function marcarVencidos(int $diasGracia = 15): int
    {
        $vencidos = $this->prestamos->vencidosPorMarcar($diasGracia);
        $contador = 0;

        foreach ($vencidos as $prestamo) {
            if ($this->prestamos->marcarComoVencido($prestamo)) {
                $contador++;
            }
        }

        return $contador;
    }
}
