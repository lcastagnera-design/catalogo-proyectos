<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar - Catálogo de Proyectos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f3f6f9] min-h-screen flex items-center justify-center font-sans text-gray-900 antialiased p-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-[#0054e9] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-[0_4px_16px_rgba(0,0,0,.08)]">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Catálogo de Proyectos</h1>
            <p class="text-gray-500 text-sm mt-1">Ingresá con tu cuenta para continuar</p>
        </div>

        <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-6 sm:p-8">
            @if (session('error'))
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <p class="text-xs text-gray-400 text-center mb-6">Simulación de ingreso (sin OAuth real)</p>

            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                        placeholder="Tu nombre" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9] focus:border-transparent">
                </div>

                <div class="space-y-3">
                    <button type="submit" name="proveedor" value="google"
                        class="w-full flex items-center justify-center gap-3 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-sm font-semibold px-4 py-3 rounded-xl transition-colors shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 01-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                        </svg>
                        Ingresar con Google
                    </button>

                    <button type="submit" name="proveedor" value="microsoft"
                        class="w-full flex items-center justify-center gap-3 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-sm font-semibold px-4 py-3 rounded-xl transition-colors shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <rect x="1" y="1" width="10.5" height="10.5" fill="#F25022"/>
                            <rect x="12.5" y="1" width="10.5" height="10.5" fill="#7FBA00"/>
                            <rect x="1" y="12.5" width="10.5" height="10.5" fill="#00A4EF"/>
                            <rect x="12.5" y="12.5" width="10.5" height="10.5" fill="#FFB900"/>
                        </svg>
                        Ingresar con Microsoft
                    </button>
                </div>
            </form>
        </div>

        <p class="text-xs text-gray-400 text-center mt-6">© {{ date('Y') }} Sistema de Gestión de Proyectos</p>
    </div>

</body>
</html>
