@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Panel de Control')
@section('page-subtitle', 'Resumen en tiempo real del estado de la biblioteca')

@section('content')
<div x-data="dashboardPage()" x-init="cargar()" class="space-y-8">

    {{-- Loading skeleton --}}
    <div x-show="cargando" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <template x-for="i in 4">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm h-28 animate-pulse"></div>
        </template>
    </div>

    <div x-show="!cargando" x-cloak class="space-y-8">

        {{-- ====== TARJETAS DE STATS ====== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition duration-200">
                <div class="p-4 bg-blue-50 text-blue-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Libros Catalogados</span>
                    <h3 class="text-3xl font-display font-bold text-slate-900 mt-1" x-text="stats.libros"></h3>
                    <p class="text-xs text-blue-600 font-medium mt-1">
                        <span x-text="stats.libros_disponibles"></span> disponibles para préstamo
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition duration-200">
                <div class="p-4 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Usuarios</span>
                    <h3 class="text-3xl font-display font-bold text-slate-900 mt-1" x-text="stats.usuarios"></h3>
                    <p class="text-xs text-emerald-600 font-medium mt-1">
                        <span x-text="stats.usuarios_activos"></span> activos actualmente
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition duration-200">
                <div class="p-4 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Préstamos Activos</span>
                    <h3 class="text-3xl font-display font-bold text-slate-900 mt-1" x-text="stats.prestamos_activos"></h3>
                    <p class="text-xs text-slate-500 font-medium mt-1">
                        <span x-text="stats.prestamos_devueltos"></span> devueltos · <span x-text="stats.prestamos_vencidos"></span> vencidos
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition duration-200">
                <div class="p-4 bg-rose-50 text-rose-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Préstamos Vencidos</span>
                    <h3 class="text-3xl font-display font-bold text-slate-900 mt-1" x-text="stats.prestamos_vencidos"></h3>
                    <p class="text-xs text-rose-600 font-medium mt-1">Requieren seguimiento</p>
                </div>
            </div>
        </div>

        {{-- ====== ÚLTIMOS PRÉSTAMOS + ALERTAS ====== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm lg:col-span-2 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-display font-bold text-lg text-slate-900">Últimos Préstamos</h3>
                        <p class="text-xs text-slate-500">Movimientos más recientes del sistema</p>
                    </div>
                    <a href="{{ route('prestamos.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1.5 transition">
                        <span>Ver todos</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="divide-y divide-slate-100 flex-1">
                    <template x-if="ultimosPrestamos.length === 0">
                        <div class="py-8 text-center text-slate-400 text-sm">No hay préstamos registrados.</div>
                    </template>
                    <template x-for="prestamo in ultimosPrestamos" :key="prestamo.id">
                        <div class="py-4 flex items-center justify-between px-2 rounded-xl transition duration-150 hover:bg-slate-50/50">
                            <div class="flex items-center gap-4">
                                <div :class="'w-10 h-14 rounded-lg text-white flex flex-col justify-end p-1.5 shadow-sm text-[8px] font-bold overflow-hidden select-none bg-gradient-to-br ' + coverColor(prestamo.libro_id)">
                                    <span class="leading-none tracking-tight block truncate" x-text="prestamo.libro?.titulo || 'Libro'"></span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-800" x-text="prestamo.libro?.titulo"></h4>
                                    <p class="text-xs text-slate-500 flex items-center gap-1">
                                        <span>Solicitado por:</span>
                                        <span class="text-indigo-600 font-medium" x-text="prestamo.usuario?.nombre"></span>
                                    </p>
                                    <span class="text-[10px] font-mono text-slate-400" x-text="formatDate(prestamo.fecha_prestamo)"></span>
                                </div>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                      :class="{
                                          'bg-emerald-50 text-emerald-800 border-emerald-100': prestamo.estado === 'devuelto',
                                          'bg-rose-50 text-rose-800 border-rose-100': prestamo.estado === 'vencido',
                                          'bg-blue-50 text-blue-800 border-blue-100': prestamo.estado === 'activo',
                                      }"
                                      x-text="prestamo.estado.charAt(0).toUpperCase() + prestamo.estado.slice(1)"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-6 rounded-2xl text-white shadow-lg shadow-slate-900/20">
                    <h3 class="font-display font-bold text-lg mb-4">Acciones Rápidas</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('libros.index') }}" class="p-3 bg-white/10 rounded-xl text-center hover:bg-white/20 transition duration-150 flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs font-medium">Nuevo Libro</span>
                        </a>
                        <a href="{{ route('prestamos.index') }}" class="p-3 bg-white/10 rounded-xl text-center hover:bg-white/20 transition duration-150 flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs font-medium">Nuevo Préstamo</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="font-display font-bold text-sm text-slate-900 mb-4">Alertas de Stock</h3>
                    <div class="space-y-3">
                        <template x-if="librosBajoStock.length === 0">
                            <p class="text-xs text-slate-400 text-center py-4">Inventario con stock saludable.</p>
                        </template>
                        <template x-for="libro in librosBajoStock" :key="libro.id">
                            <div class="flex items-center justify-between p-3 bg-amber-50/50 border border-amber-100 rounded-xl">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 block truncate max-w-[150px]" x-text="libro.titulo"></h4>
                                    <span class="text-[10px] text-slate-500 font-mono" x-text="libro.isbn"></span>
                                </div>
                                <span class="inline-flex px-2 py-0.5 bg-amber-100 text-amber-800 rounded font-bold text-[10px]">
                                    <span x-text="libro.stock_disponible"></span> disp.
                                </span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function dashboardPage() {
        return {
            cargando: true,
            stats: {
                libros: 0, libros_disponibles: 0, libros_sin_stock: 0,
                usuarios: 0, usuarios_activos: 0,
                prestamos_activos: 0, prestamos_devueltos: 0, prestamos_vencidos: 0,
            },
            ultimosPrestamos: [],
            librosBajoStock: [],
            async cargar() {
                try {
                    const data = await apiFetch('/api/dashboard/stats');
                    this.stats = data.totales;
                    this.ultimosPrestamos = data.ultimos_prestamos;
                    this.librosBajoStock = data.libros_bajo_stock;
                } catch (e) {
                    showToast('Error cargando dashboard: ' + e.message, 'error');
                } finally {
                    this.cargando = false;
                }
            },
            formatDate(d) {
                if (!d) return '';
                const parts = d.split('-');
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }
        };
    }
</script>
@endsection
