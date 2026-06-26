<?php

namespace App\Console\Commands;

use App\Services\ReporteService;
use Illuminate\Console\Command;

class ReporteBibliotecaCommand extends Command
{
    protected $signature = 'reporte:biblioteca
                            {--top=10 : Cantidad de libros más prestados a mostrar}
                            {--format=table : Formato de salida (table o json)}';

    protected $description = 'Genera un reporte con los libros más prestados, usuarios con préstamos vencidos y libros sin stock.';

    public function handle(ReporteService $reporteService): int
    {
        $top = (int) $this->option('top');
        $formato = $this->option('format');

        $reporte = $reporteService->generarReporte($top);

        if ($formato === 'json') {
            $this->printJson($reporte);
            return self::SUCCESS;
        }

        $this->printTablas($reporte, $top);
        return self::SUCCESS;
    }

    private function printTablas(array $reporte, int $top): void
    {
        $this->newLine();
        $this->info("════════════════════════════════════════════════════════════");
        $this->info("  REPORTE DE BIBLIOTECA — generado: " . now()->format('Y-m-d H:i:s'));
        $this->info("════════════════════════════════════════════════════════════");

        // 1. Libros más prestados
        $this->newLine();
        $this->comment("📚  Top {$top} LIBROS MÁS PRESTADOS");
        $filas = $reporte['libros_mas_prestados']->map(fn ($l) => [
            $l->id,
            \Illuminate\Support\Str::limit($l->titulo, 50),
            $l->stock_disponible,
            $l->total_prestamos ?? 0,
        ])->toArray();

        if (count($filas) === 0) {
            $this->line('  (sin datos)');
        } else {
            $this->table(['ID', 'Título', 'Stock', 'Total préstamos'], $filas);
        }

        // 2. Usuarios con préstamos vencidos
        $this->newLine();
        $this->comment("⚠️   USUARIOS CON PRÉSTAMOS VENCIDOS");
        $filas = $reporte['usuarios_con_prestamos_vencidos']->map(fn ($u) => [
            $u->id,
            $u->nombre,
            $u->email,
            $u->prestamos_vencidos_count ?? 0,
        ])->toArray();

        if (count($filas) === 0) {
            $this->line('  ✓ No hay usuarios con préstamos vencidos.');
        } else {
            $this->table(['ID', 'Nombre', 'Email', 'Vencidos'], $filas);
        }

        // 3. Libros sin stock
        $this->newLine();
        $this->comment("📕  LIBROS SIN STOCK");
        $filas = $reporte['libros_sin_stock']->map(fn ($l) => [
            $l->id,
            \Illuminate\Support\Str::limit($l->titulo, 60),
            $l->isbn,
        ])->toArray();

        if (count($filas) === 0) {
            $this->line('  ✓ Todos los libros tienen stock disponible.');
        } else {
            $this->table(['ID', 'Título', 'ISBN'], $filas);
        }

        $this->newLine();
    }

    private function printJson(array $reporte): void
    {
        $payload = [
            'generado_en' => now()->toIso8601String(),
            'libros_mas_prestados' => $reporte['libros_mas_prestados']->map(fn ($l) => [
                'id' => $l->id,
                'titulo' => $l->titulo,
                'total_prestamos' => $l->total_prestamos ?? 0,
            ])->values(),
            'usuarios_con_prestamos_vencidos' => $reporte['usuarios_con_prestamos_vencidos']->map(fn ($u) => [
                'id' => $u->id,
                'nombre' => $u->nombre,
                'email' => $u->email,
                'vencidos' => $u->prestamos_vencidos_count ?? 0,
            ])->values(),
            'libros_sin_stock' => $reporte['libros_sin_stock']->map(fn ($l) => [
                'id' => $l->id,
                'titulo' => $l->titulo,
                'isbn' => $l->isbn,
            ])->values(),
        ];

        $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
