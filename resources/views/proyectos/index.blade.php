@extends('proyectos.layouts.app')

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Catálogo de Proyectos</h1>
            <p class="text-gray-500 text-sm">Explorá y gestioná todos los proyectos del catálogo.</p>
        </div>
        <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-1.5 bg-[#0054e9] hover:bg-[#003eb3] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo proyecto
        </a>
    </div>

    {{-- US-02: Filtros rápidos (búsqueda, área, estado, trimestre, orden). --}}
    <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-4 sm:p-5 mb-6">
        <form action="{{ route('proyectos.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-xs font-semibold text-gray-600 mb-1">Buscar</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre de proyecto..." class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] transition-colors bg-gray-50">
            </div>

            <div class="w-full lg:w-56">
                <label for="area" class="block text-xs font-semibold text-gray-600 mb-1">Área solicitante</label>
                <select id="area" name="area" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
                    <option value="">Todas las áreas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area }}" {{ request('area') === $area ? 'selected' : '' }}>{{ $area }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full lg:w-44">
                <label for="estado" class="block text-xs font-semibold text-gray-600 mb-1">Estado</label>
                <select id="estado" name="estado" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
                    <option value="">Todos los estados</option>
                    @foreach ($estados as $estadoValor)
                        <option value="{{ $estadoValor }}" {{ request('estado') === $estadoValor ? 'selected' : '' }}>
                            {{ \App\Models\Proyecto::ESTADOS_LABELS[$estadoValor] ?? ucfirst($estadoValor) }}
                        </option>
                    @endforeach
                    <option value="archivado" {{ request('estado') === 'archivado' ? 'selected' : '' }}>Archivado</option>
                </select>
            </div>

            <div class="w-full lg:w-36">
                <label for="trimestre" class="block text-xs font-semibold text-gray-600 mb-1">Trimestre</label>
                <select id="trimestre" name="trimestre" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
                    <option value="">Todos</option>
                    @foreach (['Q1' => 'Trimestre 1', 'Q2' => 'Trimestre 2', 'Q3' => 'Trimestre 3', 'Q4' => 'Trimestre 4'] as $q => $label)
                        <option value="{{ $q }}" {{ request('trimestre') === $q ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full lg:w-56">
                <label for="orden" class="block text-xs font-semibold text-gray-600 mb-1">Última actualización</label>
                <select id="orden" name="orden" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
                    <option value="">Recientes primero</option>
                    <option value="updated_desc" {{ request('orden') === 'updated_desc' ? 'selected' : '' }}>Ultima actualización: recientes</option>
                    <option value="updated_asc" {{ request('orden') === 'updated_asc' ? 'selected' : '' }}>Ultima actualización: antiguos</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-[#0054e9] hover:bg-[#003eb3] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm whitespace-nowrap">Filtrar</button>
                @if (request()->anyFilled(['search', 'area', 'estado', 'trimestre', 'orden']))
                    <a href="{{ route('proyectos.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2.5 text-center whitespace-nowrap">Limpiar</a>
                @endif
            </div>
        </form>

        {{-- US-04: toggle para mostrar archivados. --}}
        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" name="archivados" value="1" {{ request()->boolean('archivados') ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-[#0054e9] focus:ring-[#0054e9]"
                       onchange="window.location.href='{{ route('proyectos.index', array_merge(request()->except(['archivados', 'page']), ['archivados' => request()->boolean('archivados') ? '' : '1'])) }}'">
                <span class="text-sm font-medium text-gray-600">Mostrar proyectos archivados</span>
            </label>
        </div>
    </div>

    @if ($proyectos->isEmpty())
        <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <h3 class="text-lg font-semibold text-gray-600 mb-1">No se encontraron proyectos</h3>
            <p class="text-sm text-gray-400 mb-4">Intentá con otros filtros o creá un nuevo proyecto.</p>
            <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-1.5 bg-[#0054e9] hover:bg-[#003eb3] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo proyecto
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($proyectos as $proyecto)
                @include('proyectos.partials._card', compact('proyecto'))
            @endforeach
        </div>

        <div class="mt-8">
            {{ $proyectos->withQueryString()->links() }}
        </div>
    @endif

    {{-- Modal de confirmación de archivado (US-04) --}}
    <div id="archivar-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" onclick="cerrarArchivar()"></div>
        <div class="relative bg-white rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,.12)] border border-gray-100 max-w-md w-full p-6">
            <div class="w-12 h-12 bg-[#CAF2EC] rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#2a7a6a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">¿Archivar este proyecto?</h3>
            <p class="text-sm text-gray-500 mb-6">El proyecto no se elimina: queda archivado en el sistema para auditoría y podés volver a verlo desde el listado activando "Mostrar proyectos archivados".</p>

            <form id="archivar-form" action="" method="POST" class="flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="cerrarArchivar()" class="text-sm font-medium text-gray-500 hover:text-gray-700 px-4 py-2.5">Cancelar</button>
                <button type="submit" class="inline-flex items-center gap-1.5 bg-[#0054e9] hover:bg-[#003eb3] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                    Archivar proyecto
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    window.archivarProyecto = function (url) {
        document.getElementById('archivar-form').action = url;
        const modal = document.getElementById('archivar-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };
    window.cerrarArchivar = function () {
        const modal = document.getElementById('archivar-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };
    // Cerrar con tecla Escape.
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.cerrarArchivar && window.cerrarArchivar();
    });
</script>
@endpush
