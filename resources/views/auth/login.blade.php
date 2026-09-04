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
