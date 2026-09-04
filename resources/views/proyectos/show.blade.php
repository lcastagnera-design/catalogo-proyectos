@extends('proyectos.layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('proyectos.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#0054e9] transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver al catálogo
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 overflow-hidden">
        <div class="h-32 bg-gradient-to-br from-[#CAF2EC] to-[#8de2d6] flex items-center justify-center">
            <svg class="w-16 h-16 text-[#55b4a8] opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>

        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-2">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $proyecto->nombre_proyecto }}</h1>
                        @php
                            $estadoBadge = [
                                'planificacion' => 'bg-[#CAF2EC] text-[#2a7a6a]',
                                'ejecucion' => 'bg-[#0054e9]/10 text-[#0054e9]',
                                'frenado' => 'bg-[#ffc409]/20 text-[#8f6b00]',
                                'finalizado' => 'bg-green-100 text-green-700',
                                'archivado' => 'bg-gray-100 text-gray-500',
                            ][$proyecto->estado] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="inline-flex shrink-0 px-3 py-0.5 rounded-full text-sm font-semibold {{ $estadoBadge }}">
                            {{ $proyecto->estadoLabel() }}
                        </span>
                    </div>
                    @if ($proyecto->nombre_proyecto_marca)
                        <p class="text-gray-500 font-medium mt-1">{{ $proyecto->nombre_proyecto_marca }}</p>
                    @endif
                </div>
                <a href="{{ route('proyectos.edit', $proyecto) }}" class="shrink-0 inline-flex items-center gap-1.5 bg-[#0054e9] hover:bg-[#003eb3] text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Editar
                </a>
            </div>

            @if ($proyecto->areaSolicitante)
                @php
                    $area = $proyecto->areaSolicitante;
                    $label = $area->nivel4 !== null && $area->nivel4 !== '-' && $area->nivel3 !== '-'
                        ? $area->nivel3 . ' · ' . $area->nivel4
                        : ($area->nivel3 ?? $area->nivel2);
                @endphp
                <div class="flex flex-wrap items-center gap-2 mb-8">
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-[#CAF2EC] text-[#2a7a6a]">{{ $label }}</span>
                </div>
            @endif

            <div class="mb-8">
                <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Componentes</h2>

                @forelse ($proyecto->componentes as $componente)
                    <div class="border border-gray-200 rounded-2xl p-5 mb-4 bg-gray-50/50">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <h3 class="font-bold text-gray-900">{{ $componente->nombre_componente }}</h3>
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#0054e9]/10 text-[#0054e9]">{{ $componente->tipo_componente }}</span>
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-600">{{ $componente->tecnologia }} {{ $componente->version }}</span>
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-600">{{ $componente->exposicion_internet }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            <div>
                                <span class="text-gray-400">Repositorio:</span>
                                <a href="{{ $componente->repositorio_gitlab }}" target="_blank" rel="noopener" class="text-[#0054e9] hover:underline break-all">{{ $componente->repositorio_gitlab }}</a>
                            </div>
                            <div>
                                <span class="text-gray-400">URL dev:</span>
                                <a href="{{ $componente->url_dev }}" target="_blank" rel="noopener" class="text-[#0054e9] hover:underline break-all">{{ $componente->url_dev }}</a>
                            </div>
                            <div>
                                <span class="text-gray-400">URL OpenShift:</span>
                                <a href="{{ $componente->url_openshift }}" target="_blank" rel="noopener" class="text-[#0054e9] hover:underline break-all">{{ $componente->url_openshift }}</a>
                            </div>
                            <div>
                                <span class="text-gray-400">Nombre normalizado:</span>
                                <span class="text-gray-700 font-medium">{{ $componente->nombre_normalizado }}</span>
                            </div>
                            @if ($componente->observaciones)
                                <div class="sm:col-span-2">
                                    <span class="text-gray-400">Observaciones:</span>
                                    <span class="text-gray-700 whitespace-pre-line">{{ $componente->observaciones }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Este proyecto no tiene componentes cargados.</p>
                @endforelse
            </div>

            <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('proyectos.edit', $proyecto) }}" class="inline-flex items-center gap-1.5 bg-[#0054e9] hover:bg-[#003eb3] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Editar
                </a>
                <button type="button" onclick="abrirArchivarShow()"
                    class="inline-flex items-center gap-1.5 bg-[#ffc409]/20 hover:bg-[#ffc409]/30 text-[#8f6b00] text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Archivar
                </button>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación de archivado (US-04) --}}
    <div id="archivar-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" onclick="cerrarArchivarShow()"></div>
        <div class="relative bg-white rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,.12)] border border-gray-100 max-w-md w-full p-6">
            <div class="w-12 h-12 bg-[#CAF2EC] rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#2a7a6a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">¿Archivar este proyecto?</h3>
            <p class="text-sm text-gray-500 mb-6">El proyecto no se elimina: queda archivado en el sistema para auditoría.</p>

            <form action="{{ route('proyectos.destroy', $proyecto) }}" method="POST" class="flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="cerrarArchivarShow()" class="text-sm font-medium text-gray-500 hover:text-gray-700 px-4 py-2.5">Cancelar</button>
                <button type="submit" class="inline-flex items-center gap-1.5 bg-[#0054e9] hover:bg-[#003eb3] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                    Archivar proyecto
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    window.abrirArchivarShow = function () {
        const modal = document.getElementById('archivar-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };
    window.cerrarArchivarShow = function () {
        const modal = document.getElementById('archivar-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.cerrarArchivarShow && window.cerrarArchivarShow();
    });
</script>
@endpush