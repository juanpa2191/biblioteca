@extends('layouts.app')

@section('title', 'Catálogo de Libros')
@section('page-title', 'Catálogo de Libros')
@section('page-subtitle', 'Administra el inventario, autores y stock de tu biblioteca')

@section('content')
<div x-data="librosPage()" x-init="cargarAutores(); buscar()" class="space-y-6">

    {{-- ====== BARRA DE BÚSQUEDA Y FILTROS ====== --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1 max-w-2xl">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="filtros.titulo" @input.debounce.350ms="buscar()"
                       placeholder="Buscar por título en tiempo real..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <select x-model="filtros.autor" @change="buscar()" class="sm:w-48 px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                <option value="">Todos los autores</option>
                <template x-for="autor in autores" :key="autor.id">
                    <option :value="autor.id" x-text="autor.nombre_completo"></option>
                </template>
            </select>

            <select x-model="filtros.disponibles" @change="buscar()" class="sm:w-44 px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                <option value="">Todos</option>
                <option value="1">Solo con stock</option>
            </select>

            <button x-show="filtros.titulo || filtros.autor || filtros.disponibles"
                    @click="filtros = {titulo:'', autor:'', disponibles:''}; buscar()"
                    x-cloak
                    class="px-3 py-2 bg-slate-100 text-slate-600 font-semibold rounded-xl text-sm hover:bg-slate-200 transition">
                Limpiar
            </button>
        </div>

        <button @click="abrirCrear()"
                class="w-full md:w-auto px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl text-sm hover:bg-indigo-700 transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Nuevo Libro</span>
        </button>
    </div>

    {{-- ====== GRID DE LIBROS ====== --}}
    <div x-show="cargando" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <template x-for="i in 8">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm h-96 animate-pulse"></div>
        </template>
    </div>

    <div x-show="!cargando" x-cloak>
        <div x-show="libros.length === 0" class="py-12 text-center bg-white border border-slate-100 rounded-2xl">
            <p class="text-slate-400">No se encontraron libros con esos filtros.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <template x-for="libro in libros" :key="libro.id">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition duration-200">
                    <div :class="'h-44 p-6 flex flex-col justify-between text-white relative select-none bg-gradient-to-br ' + coverColor(libro.id)">
                        <span class="text-[10px] uppercase font-mono tracking-widest bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-full w-fit">
                            <span x-text="libro.anio_publicacion"></span>
                        </span>
                        <div>
                            <h3 class="font-display font-bold text-lg leading-snug line-clamp-2" x-text="libro.titulo"></h3>
                            <p class="text-xs text-white/80 font-medium mt-1" x-text="libro.autores.map(a => a.nombre_completo).join(', ') || 'Sin autores'"></p>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400">ISBN</span>
                                <span class="font-mono font-medium text-slate-600" x-text="libro.isbn"></span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400">Páginas</span>
                                <span class="font-semibold text-slate-700" x-text="libro.numero_paginas || '—'"></span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500">Stock disponible</span>
                                <span class="text-sm font-mono font-bold"
                                      :class="libro.stock_disponible === 0 ? 'text-rose-600' : (libro.stock_disponible <= 2 ? 'text-amber-600' : 'text-emerald-600')"
                                      x-text="libro.stock_disponible"></span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                                <div class="h-full transition-all duration-300"
                                     :class="libro.stock_disponible === 0 ? 'bg-rose-500' : (libro.stock_disponible <= 2 ? 'bg-amber-500' : 'bg-emerald-500')"
                                     :style="`width: ${Math.min(100, libro.stock_disponible * 15)}%`"></div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <button @click="abrirEditar(libro)" class="flex-1 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 font-semibold rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Editar</span>
                            </button>
                            <button @click="eliminar(libro)" class="p-1.5 bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-600 rounded-xl transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Paginación --}}
        <div x-show="meta.last_page > 1" class="flex items-center justify-between mt-6 px-4">
            <p class="text-xs text-slate-500">
                Mostrando <span class="font-semibold" x-text="meta.from"></span>–<span class="font-semibold" x-text="meta.to"></span> de <span class="font-semibold" x-text="meta.total"></span>
            </p>
            <div class="flex items-center gap-1">
                <button :disabled="meta.current_page === 1" @click="cambiarPagina(meta.current_page - 1)" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">Anterior</button>
                <span class="px-3 py-1.5 text-xs font-mono text-slate-500" x-text="meta.current_page + ' / ' + meta.last_page"></span>
                <button :disabled="meta.current_page === meta.last_page" @click="cambiarPagina(meta.current_page + 1)" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">Siguiente</button>
            </div>
        </div>
    </div>

    {{-- ====== MODAL CREAR / EDITAR ====== --}}
    <div x-show="modal.open" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="cerrarModal()"></div>

        <div class="bg-white rounded-3xl overflow-hidden shadow-2xl sm:max-w-2xl w-full mx-4 z-10 border border-slate-100">
            <div class="px-6 py-4 bg-slate-900 text-white flex justify-between items-center">
                <h3 class="font-display font-bold text-lg" x-text="modal.modo === 'crear' ? 'Añadir Libro al Catálogo' : 'Editar Libro'"></h3>
                <button @click="cerrarModal()" class="text-slate-400 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form @submit.prevent="guardar()" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Título <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="form.titulo" required class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm">
                        <p x-show="errores.titulo" x-cloak class="text-xs text-rose-600 mt-1" x-text="errores.titulo?.[0]"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">ISBN <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="form.isbn" required class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm font-mono">
                        <p x-show="errores.isbn" x-cloak class="text-xs text-rose-600 mt-1" x-text="errores.isbn?.[0]"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Año publicación <span class="text-rose-500">*</span></label>
                        <input type="number" x-model.number="form.anio_publicacion" required min="1450" max="2100" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm">
                        <p x-show="errores.anio_publicacion" x-cloak class="text-xs text-rose-600 mt-1" x-text="errores.anio_publicacion?.[0]"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Páginas</label>
                        <input type="number" x-model.number="form.numero_paginas" min="1" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Stock disponible <span class="text-rose-500">*</span></label>
                        <input type="number" x-model.number="form.stock_disponible" required min="0" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm font-mono">
                        <p x-show="errores.stock_disponible" x-cloak class="text-xs text-rose-600 mt-1" x-text="errores.stock_disponible?.[0]"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Descripción</label>
                    <textarea x-model="form.descripcion" rows="2" class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Autores <span class="text-rose-500">*</span> (en orden de autoría)</label>
                    <div class="border border-slate-200 rounded-xl p-3 max-h-48 overflow-y-auto space-y-1">
                        <template x-for="autor in autores" :key="autor.id">
                            <label class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-slate-50 cursor-pointer">
                                <input type="checkbox" :value="autor.id" x-model.number="form.autor_ids" class="rounded">
                                <span class="text-sm" x-text="autor.nombre_completo"></span>
                                <span class="text-xs text-slate-400" x-text="'(' + autor.nacionalidad + ')'"></span>
                            </label>
                        </template>
                    </div>
                    <p x-show="errores.autor_ids" x-cloak class="text-xs text-rose-600 mt-1" x-text="errores.autor_ids?.[0]"></p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="cerrarModal()" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition">Cancelar</button>
                    <button type="submit" :disabled="guardando"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-semibold rounded-xl text-sm transition shadow-lg shadow-indigo-600/20">
                        <span x-text="guardando ? 'Guardando...' : (modal.modo === 'crear' ? 'Crear Libro' : 'Guardar Cambios')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function librosPage() {
        return {
            cargando: true,
            guardando: false,
            libros: [],
            autores: [],
            meta: { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 },
            filtros: { titulo: '', autor: '', disponibles: '' },
            modal: { open: false, modo: 'crear' },
            form: {
                id: null, titulo: '', isbn: '', anio_publicacion: new Date().getFullYear(),
                numero_paginas: null, descripcion: '', stock_disponible: 1, autor_ids: [],
            },
            errores: {},

            async cargarAutores() {
                try {
                    const data = await apiFetch('/api/autores?per_page=100');
                    this.autores = data.data;
                } catch (e) {
                    showToast('Error cargando autores', 'error');
                }
            },

            async buscar(pagina = 1) {
                this.cargando = true;
                const params = new URLSearchParams();
                params.set('per_page', 12);
                params.set('page', pagina);
                if (this.filtros.titulo) params.set('titulo', this.filtros.titulo);
                if (this.filtros.autor) params.set('autor', this.filtros.autor);
                if (this.filtros.disponibles) params.set('disponibles', this.filtros.disponibles);

                try {
                    const data = await apiFetch('/api/libros?' + params.toString());
                    this.libros = data.data;
                    this.meta = data.meta;
                } catch (e) {
                    showToast('Error buscando libros: ' + e.message, 'error');
                } finally {
                    this.cargando = false;
                }
            },

            cambiarPagina(n) { this.buscar(n); },

            abrirCrear() {
                this.errores = {};
                this.form = {
                    id: null, titulo: '', isbn: '', anio_publicacion: new Date().getFullYear(),
                    numero_paginas: null, descripcion: '', stock_disponible: 1, autor_ids: [],
                };
                this.modal = { open: true, modo: 'crear' };
            },

            abrirEditar(libro) {
                this.errores = {};
                this.form = {
                    id: libro.id,
                    titulo: libro.titulo,
                    isbn: libro.isbn,
                    anio_publicacion: libro.anio_publicacion,
                    numero_paginas: libro.numero_paginas,
                    descripcion: libro.descripcion || '',
                    stock_disponible: libro.stock_disponible,
                    autor_ids: libro.autores.map(a => a.id),
                };
                this.modal = { open: true, modo: 'editar' };
            },

            cerrarModal() { this.modal.open = false; },

            async guardar() {
                this.errores = {};
                this.guardando = true;
                const url = this.modal.modo === 'crear' ? '/api/libros' : `/api/libros/${this.form.id}`;
                const method = this.modal.modo === 'crear' ? 'POST' : 'PUT';
                const payload = { ...this.form };
                delete payload.id;

                try {
                    await apiFetch(url, { method, body: JSON.stringify(payload) });
                    showToast(this.modal.modo === 'crear' ? 'Libro creado' : 'Libro actualizado', 'success');
                    this.cerrarModal();
                    this.buscar(this.meta.current_page);
                } catch (e) {
                    if (e.status === 422 && e.data?.errors) {
                        this.errores = e.data.errors;
                        showToast('Revisa los errores del formulario', 'error');
                    } else {
                        showToast(e.message || 'Error al guardar', 'error');
                    }
                } finally {
                    this.guardando = false;
                }
            },

            async eliminar(libro) {
                if (!confirm(`¿Eliminar el libro "${libro.titulo}"? Se hará soft delete (recuperable).`)) return;
                try {
                    await apiFetch(`/api/libros/${libro.id}`, { method: 'DELETE' });
                    showToast('Libro eliminado', 'success');
                    this.buscar(this.meta.current_page);
                } catch (e) {
                    showToast(e.message || 'Error al eliminar', 'error');
                }
            },
        };
    }
</script>
@endsection
