<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Biblioteca') - Sistema de Gestión</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    {{-- ===================================================================
         Helpers JS globales: apiFetch + toast notifications + token guard
         =================================================================== --}}
    <script>
        // Token guard: si no hay token, redirigir al login
        (function () {
            const token = localStorage.getItem('biblioteca_token');
            if (!token && window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        })();

        /**
         * Wrapper de fetch() para la API REST.
         *   - Agrega Authorization: Bearer <token>
         *   - Agrega Accept: application/json
         *   - Si recibe 401, limpia token y redirige a login
         *   - Parsea JSON automáticamente
         *   - Lanza error en !ok con el body de respuesta
         *
         * Uso:
         *   const data = await apiFetch('/api/libros?per_page=10');
         *   await apiFetch('/api/libros', { method: 'POST', body: JSON.stringify({...}) });
         */
        window.apiFetch = async function (url, options = {}) {
            const token = localStorage.getItem('biblioteca_token');
            const headers = Object.assign({
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }, options.headers || {});
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const resp = await fetch(url, Object.assign({}, options, { headers }));

            if (resp.status === 401) {
                localStorage.removeItem('biblioteca_token');
                localStorage.removeItem('biblioteca_user');
                window.location.href = '/login';
                throw new Error('No autenticado');
            }

            if (resp.status === 204) return null;

            const data = await resp.json();
            if (!resp.ok) {
                const err = new Error(data.message || 'Error en la petición');
                err.status = resp.status;
                err.data = data;
                throw err;
            }
            return data;
        };

        /** Sistema simple de toasts. Uso: showToast('Listo', 'success') */
        window.toastQueue = [];
        window.showToast = function (mensaje, tipo = 'success') {
            window.dispatchEvent(new CustomEvent('toast', { detail: { mensaje, tipo } }));
        };

        /** Genera una clase de gradiente determinística a partir de un id (para portadas) */
        window.coverColor = function (id) {
            const paleta = [
                'from-blue-500 to-indigo-700',
                'from-emerald-500 to-teal-700',
                'from-rose-500 to-red-700',
                'from-amber-400 to-orange-600',
                'from-violet-500 to-purple-800',
                'from-slate-700 to-slate-900',
                'from-fuchsia-500 to-pink-700',
                'from-cyan-500 to-blue-700',
            ];
            return paleta[Number(id) % paleta.length];
        };

        /** Cierra sesión: borra token y redirige a login */
        window.cerrarSesion = async function () {
            try { await apiFetch('/api/logout', { method: 'POST' }); } catch (e) {}
            localStorage.removeItem('biblioteca_token');
            localStorage.removeItem('biblioteca_user');
            window.location.href = '/login';
        };
    </script>
</head>
<body class="h-full text-slate-800 flex flex-col md:flex-row antialiased">

    {{-- =================== SIDEBAR =================== --}}
    <aside class="w-full md:w-64 bg-slate-900 text-white flex-shrink-0 flex flex-col border-r border-slate-800 shadow-xl">
        <div class="p-6 border-b border-slate-800 flex items-center gap-3">
            <div class="p-2 bg-indigo-600 rounded-lg text-white shadow-md shadow-indigo-600/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h1 class="font-display font-bold text-lg tracking-tight leading-none">Biblioteca</h1>
                <span class="text-xs text-indigo-400 font-medium font-mono">Sistema de Gestión</span>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-150 {{ Request::routeIs('dashboard') ? 'bg-indigo-600 text-white font-medium' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('libros.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-150 {{ Request::routeIs('libros.*') ? 'bg-indigo-600 text-white font-medium' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Catálogo de Libros</span>
            </a>

            <a href="{{ route('prestamos.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-150 {{ Request::routeIs('prestamos.*') ? 'bg-indigo-600 text-white font-medium' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Préstamos</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 bg-slate-950/40 text-center text-xs text-slate-500">
            <button onclick="cerrarSesion()" class="w-full px-3 py-2 rounded-lg bg-slate-800 hover:bg-rose-600/80 text-slate-300 hover:text-white font-medium transition duration-150 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Cerrar sesión</span>
            </button>
        </div>
    </aside>

    {{-- =================== MAIN =================== --}}
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between">
            <div>
                <h2 class="font-display font-semibold text-lg text-slate-900">@yield('page-title')</h2>
                <p class="text-xs text-slate-500">@yield('page-subtitle', 'Gestión integral de tu biblioteca')</p>
            </div>

            <div class="flex items-center gap-3" x-data="{ user: JSON.parse(localStorage.getItem('biblioteca_user') || '{}') }">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-slate-800" x-text="user.name || 'Bibliotecario'"></p>
                    <span class="text-xs text-indigo-600 font-mono" x-text="user.email || ''"></span>
                </div>
                <div class="w-10 h-10 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold font-display"
                     x-text="(user.name || 'BA').split(' ').map(s=>s[0]).join('').slice(0,2).toUpperCase()"></div>
            </div>
        </header>

        {{-- Toasts global --}}
        <div x-data="{ toasts: [] }"
             @toast.window="
                const id = Date.now();
                toasts.push({ id, mensaje: $event.detail.mensaje, tipo: $event.detail.tipo });
                setTimeout(() => { toasts = toasts.filter(t => t.id !== id); }, 4000);
             "
             class="fixed top-4 right-4 z-[100] space-y-2 max-w-sm">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-show="true"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="flex items-start gap-3 p-4 rounded-xl shadow-lg border"
                     :class="{
                        'bg-emerald-50 border-emerald-200 text-emerald-800': toast.tipo === 'success',
                        'bg-rose-50 border-rose-200 text-rose-800': toast.tipo === 'error',
                        'bg-blue-50 border-blue-200 text-blue-800': toast.tipo === 'info',
                     }">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path x-show="toast.tipo === 'success'" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        <path x-show="toast.tipo === 'error'"   stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5 18.5a2 2 0 002 2h10a2 2 0 002-2L12 4 5 18.5z" />
                        <path x-show="toast.tipo === 'info'"    stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm font-medium" x-text="toast.mensaje"></p>
                </div>
            </template>
        </div>

        <div class="px-8 py-6 flex-1">
            @yield('content')
        </div>
    </main>
</body>
</html>
