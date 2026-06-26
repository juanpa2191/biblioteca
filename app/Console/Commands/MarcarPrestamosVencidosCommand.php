<?php

namespace App\Console\Commands;

use App\Services\PrestamoService;
use Illuminate\Console\Command;

class MarcarPrestamosVencidosCommand extends Command
{
    protected $signature = 'prestamos:marcar-vencidos
                            {--dias=15 : Días de gracia después de fecha_devolucion_estimada antes de marcar como vencido}';

    protected $description = 'Marca como vencidos los préstamos activos cuya fecha de devolución estimada fue hace más de N días.';

    public function handle(PrestamoService $service): int
    {
        $dias = (int) $this->option('dias');

        $this->info("Buscando préstamos vencidos (más de {$dias} días de atraso)...");

        $marcados = $service->marcarVencidos($dias);

        if ($marcados === 0) {
            $this->line('  ✓ No hay préstamos vencidos para marcar.');
        } else {
            $this->info("  → {$marcados} préstamo(s) marcado(s) como vencido(s).");
        }

        return self::SUCCESS;
    }
}
