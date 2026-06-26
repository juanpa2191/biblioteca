<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Biblioteca</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: {
                sans: ['Inter', 'sans-serif'],
                display: ['Space Grotesk', 'sans-serif'],
                mono: ['JetBrains Mono', 'monospace'],
            } } }
        }

        // Si ya hay token, ir directo al dashboard
        if (localStorage.getItem('biblioteca_token')) {
            window.location.href = '/dashboard';
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 p-4">

    <div class="w-full max-w-md"
         x-data="{
             email: 'admin@biblioteca.test',
             password: 'password123',
             cargando: false,
             error: null,
             async login() {
                 this.error = null;
                 this.cargando = true;
                 try {
                     const resp = await fetch('/api/login', {
                         method: 'POST',
                         headers: {
                             'Accept': 'application/json',
                             'Content-Type': 'application/json',
                         },
                         body: JSON.stringify({
                             email: this.email,
                             password: this.password,
                             device_name: 'web',
                         }),
                     });
                     const data = await resp.json();
                     if (!resp.ok) {
                         this.error = data.message || 'Credenciales inválidas.';
                         this.cargando = false;
                         return;
                     }
                     localStorage.setItem('biblioteca_token', data.token);
                     localStorage.setItem('biblioteca_user', JSON.stringify(data.user));
                     window.location.href = '/dashboard';
                 } catch (e) {
                     this.error = 'No se pudo conectar al servidor.';
                     this.cargando = false;
                 }
             }
         }">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center p-3 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-600/30 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h1 class="font-display font-bold text-3xl text-white tracking-tight">Biblioteca</h1>
            <p class="text-sm text-slate-400 mt-2 font-mono">Sistema de Gestión</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-8">
            <h2 class="font-display font-semibold text-xl text-slate-900 mb-1">Inicia sesión</h2>
            <p class="text-xs text-slate-500 mb-6">Ingresa con tu cuenta de bibliotecario</p>

            <form @submit.prevent="login" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Correo electrónico</label>
                    <input type="email" x-model="email" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Contraseña</label>
                    <input type="password" x-model="password" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <div x-show="error" x-cloak class="flex items-start gap-2 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span x-text="error"></span>
                </div>

                <button type="submit" :disabled="cargando"
                        class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-semibold rounded-xl text-sm transition shadow-lg shadow-indigo-600/20 flex items-center justify-center gap-2">
                    <svg x-show="cargando" x-cloak class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="cargando ? 'Ingresando...' : 'Ingresar al sistema'"></span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-400 text-center">
                    <span class="font-mono">admin@biblioteca.test / password123</span>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
