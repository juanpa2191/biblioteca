@extends('layouts.app')

@section('title', 'Préstamos')
@section('page-title', 'Control de Préstamos')
@section('page-subtitle', 'Registra entregas, procesa devoluciones y monitorea plazos')

@section('content')
<div x-data="prestamosPage()" x-init="cargar()" class="space-y-6">

    {{-- ====== HEADER ====== --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <select x-model="filtroEstado" @change="cargar(1)" class="px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="devuelto">Devueltos</option>
                <option value="vencido">Vencidos</option>
            </select>
            <span class="text-xs text-slate-500 hidden sm:inline">
                <span class="font-bold" x-text="meta.total"></span> préstamos registrados
            </span>
        </div>

        <button @click="abrirCrear()"
                class="w-full md:w-auto px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl text-sm hover:bg-indigo-700 transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Nuevo Préstamo</span>
        </button>
    </div>

    {{-- ====== TABLA ====== --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Libro</th>
                        <th class="px-6 py-4">Usuario</th>
                        <th class="px-6 py-4">Fechas</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    <template x-if="cargando">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">Cargando...</td>
                        </tr>
                    </template>
                    <template x-if="!cargando && prestamos.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">No hay préstamos para mostrar.</td>
                        </tr>
                    </template>

                    <template x-for="p in prestamos" :key="p.id">
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 font-mono text-slate-400 text-xs font-bold">
                                <span x-text="'#PR-' + String(p.id).padStart(5, '0')"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div :class="'w-8 h-11 rounded text-white flex flex-col justify-end p-1 text-[7px] font-bold overflow-hidden select-none shadow-sm flex-shrink-0 bg-gradient-to-br ' + coverColor(p.libro_id)">
                                        <span class="leading-none block truncate" x-text="p.libro?.titulo || 'Libro'"></span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-slate-900 leading-tight" x-text="p.libro?.titulo"></h4>
                                        <span class="text-xs text-slate-400 font-mono" x-text="p.libro?.isbn"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <h4 class="font-semibold text-slate-800 leading-tight" x-text="p.usuario?.nombre"></h4>
                                <span class="text-xs text-slate-400 font-mono" x-text="p.usuario?.email"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-slate-400">Salida:</span>
                                        <span class="font-mono font-medium text-slate-700" x-text="formatDate(p.fecha_prestamo)"></span>
                                    </div>
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-slate-400">Plazo:</span>
                                        <span class="font-mono font-medium text-slate-700" x-text="formatDate(p.fecha_devolucion_estimada)"></span>
                                    </div>
                                    <template x-if="p.fecha_devolucion_real">
                                        <div class="flex items-center gap-1 text-xs text-emerald-600">
                                            <span>Retornado:</span>
                                            <span class="font-mono font-bold" x-text="formatDate(p.fecha_devolucion_real)"></span>
                                        </div>
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                                      :class="{
                                          'bg-emerald-50 text-emerald-800 border-emerald-100': p.estado === 'devuelto',
                                          'bg-rose-50 text-rose-800 border-rose-100': p.estado === 'vencido',
                                          'bg-blue-50 text-blue-800 border-blue-100': p.estado === 'activo',
                                      }"
                                      x-text="p.estado.charAt(0).toUpperCase() + p.estado.slice(1)"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <template x-if="p.estado !== 'devuelto'">
                                    <button @click="devolver(p)"
                                            class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-semibold transition">
                                        Marcar devuelto
                                    </button>
                                </template>
                                <template x-if="p.estado === 'devuelto'">
                                    <span class="text-xs text-slate-400">—</span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div x-show="meta.last_page > 1" class="flex items-center justify-between p-4 border-t border-slate-100">
            <p class="text-xs text-slate-500">
                Mostrando <span class="font-semibold" x-text="meta.from"></span>–<span class="font-semibold" x-text="meta.to"></span> de <span class="font-semibold" x-text="meta.total"></span>
            </p>
            <div class="flex items-center gap-1">
                <button :disabled="meta.current_page === 1" @click="cargar(meta.current_page - 1)" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">Anterior</button>
                <span class="px-3 py-1.5 text-xs font-mono text-slate-500" x-text="meta.current_page + ' / ' + meta.last_page"></span>
                <button :disabled="meta.current_page === meta.last_page" @click="cargar(meta.current_page + 1)" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">Siguiente</button>
            </div>
        </div>
    </div>

    {{-- ====== MODAL NUEVO PRÉSTAMO ====== --}}
    <div x-show="modal.open" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="cerrarModal()"></div>

        <div class="bg-white rounded-3xl overflow-hidden shadow-2xl sm:max-w-lg w-full mx-4 z-10 border border-slate-100">
            <div class="px-6 py-4 bg-slate-900 text-white flex justify-between items-center">
                <h3 class="font-display font-bold text-lg">Registrar Nuevo Préstamo</h3>
                <button @click="cerrarModal()" class="text-slate-400 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form @submit.prevent="guardar()" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Libro <span class="text-rose-500">*</span></label>
                    <select x-model.number="form.libro_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                        <option value="">Selecciona un libro con stock...</option>
                        <template x-for="libro in librosDisponibles" :key="libro.id">
                            <option :value="libro.id"
                                    x-text="libro.titulo + ' (' + libro.stock_disponible + ' disp.)'"></option>
                        </template>
                    </select>
                    <p x-show="errores.libro_id" x-cloak class="text-xs text-rose-600 mt-1" x-text="errores.libro_id?.[0]"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Usuario solicitante <span class="text-rose-500">*</span></label>
                    <select x-model.number="form.usuario_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                        <option value="">Selecciona un usuario activo...</option>
                        <template x-for="usuario in usuariosActivos" :key="usuario.id">
                            <option :value="usuario.id" x-text="usuario.nombre + ' (' + usuario.email + ')'"></option>
                        </template>
                    </select>
                    <p x-show="errores.usuario_id" x-cloak class="text-xs text-rose-600 mt-1" x-text="errores.usuario_id?.[0]"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Fecha del préstamo</label>
                    <input type="date" x-model="form.fecha_prestamo" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm font-mono">
                    <p class="text-[10px] text-slate-400 mt-1">Si se deja vacío usa la fecha de hoy. La devolución estimada es +14 días.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="cerrarModal()" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition">Cancelar</button>
                    <button type="submit" :disabled="guardando"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-semibold rounded-xl text-sm transition shadow-lg shadow-indigo-600/20">
                        <span x-text="guardando ? 'Registrando...' : 'Registrar Préstamo'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function prestamosPage() {
        return {
            cargando: true,
            guardando: false,
            prestamos: [],
            librosDisponibles: [],
            usuariosActivos: [],
            meta: { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 },
            filtroEstado: '',
            modal: { open: false },
            form: { libro_id: '', usuario_id: '', fecha_prestamo: '' },
            errores: {},

            async cargar(pagina = 1) {
                this.cargando = true;
                const params = new URLSearchParams();
                params.set('per_page', 15);
                params.set('page', pagina);

                try {
                    const data = await apiFetch('/api/prestamos?' + params.toString());
                    let lista = data.data;
                    if (this.filtroEstado) {
                        lista = lista.filter(p => p.estado === this.filtroEstado);
                    }
                    this.prestamos = lista;
                    this.meta = data.meta;
                } catch (e) {
                    showToast('Error cargando préstamos: ' + e.message, 'error');
                } finally {
                    this.cargando = false;
                }
            },

            async abrirCrear() {
                this.errores = {};
                this.form = { libro_id: '', usuario_id: '', fecha_prestamo: '' };

                try {
                    const [librosResp, usuariosResp] = await Promise.all([
                        apiFetch('/api/libros?disponibles=1&per_page=100'),
                        apiFetch('/api/usuarios?estado=activo&per_page=100'),
                    ]);
                    this.librosDisponibles = librosResp.data;
                    this.usuariosActivos = usuariosResp.data;
                    this.modal.open = true;
                } catch (e) {
                    showToast('Error cargando datos del formulario', 'error');
                }
            },

            cerrarModal() { this.modal.open = false; },

            async guardar() {
                this.errores = {};
                this.guardando = true;
                const payload = {
                    libro_id: this.form.libro_id,
                    usuario_id: this.form.usuario_id,
                };
                if (this.form.fecha_prestamo) payload.fecha_prestamo = this.form.fecha_prestamo;

                try {
                    await apiFetch('/api/prestamos', { method: 'POST', body: JSON.stringify(payload) });
                    showToast('Préstamo registrado', 'success');
                    this.cerrarModal();
                    this.cargar(1);
                } catch (e) {
                    if (e.status === 422 && e.data?.errors) {
                        this.errores = e.data.errors;
                        showToast('Revisa los errores', 'error');
                    } else {
                        showToast(e.message || 'Error al registrar préstamo', 'error');
                    }
                } finally {
                    this.guardando = false;
                }
            },

            async devolver(prestamo) {
                if (!confirm(`¿Marcar el préstamo #${prestamo.id} como devuelto?`)) return;
                try {
                    await apiFetch(`/api/prestamos/${prestamo.id}/devolver`, { method: 'PUT' });
                    showToast('Préstamo devuelto · stock restaurado', 'success');
                    this.cargar(this.meta.current_page);
                } catch (e) {
                    showToast(e.message || 'Error al devolver', 'error');
                }
            },

            formatDate(d) {
                if (!d) return '';
                const parts = d.split('-');
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            },
        };
    }
</script>
@endsection
