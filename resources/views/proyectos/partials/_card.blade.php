@php
    $nComp = $proyecto->componentes_count ?? $proyecto->componentes->count();
    $area = $proyecto->areaSolicitante;
    $areaLabel = $area ? ($area->nivel4 !== null && $area->nivel4 !== '-' && $area->nivel3 !== '-'
        ? $area->nivel3 . ' · ' . $area->nivel4
        : ($area->nivel3 ?? $area->nivel2)) : '';

    $estadoBadge = [
        'planificacion' => 'bg-[#CAF2EC] text-[#2a7a6a]',
        'ejecucion' => 'bg-[#0054e9]/10 text-[#0054e9]',
        'frenado' => 'bg-[#ffc409]/20 text-[#8f6b00]',
        'finalizado' => 'bg-green-100 text-green-700',
        'archivado' => 'bg-gray-100 text-gray-500',
    ][$proyecto->estado] ?? 'bg-gray-100 text-gray-600';
@endphp

<div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 overflow-hidden hover:shadow-[0_8px_24px_rgba(0,0,0,.12)] transition-shadow duration-200 flex flex-col">
    <div class="h-28 bg-gradient-to-br from-[#CAF2EC] to-[#8de2d6] flex items-center justify-center">
        <svg class="w-12 h-12 text-[#55b4a8] opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
    </div>

    <div class="p-5 flex-1 flex flex-col">
        <div class="flex items-start justify-between gap-2 mb-2">
            <h3 class="font-bold text-gray-900 leading-snug line-clamp-2">{{ $proyecto->nombre_proyecto }}</h3>
            <span class="inline-flex shrink-0 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $estadoBadge }}">
                {{ $proyecto->estadoLabel() }}
            </span>
        </div>

        <p class="text-sm text-gray-500 line-clamp-1 mb-3">{{ $proyecto->nombre_proyecto_marca }}</p>

        <div class="space-y-1.5 text-xs text-gray-500 mb-4">
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                {{ $areaLabel ?: 'Área no definida' }}
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m4 10v10M4 7v10l8 4"/></svg>
                {{ $nComp }} {{ $nComp === 1 ? 'componente' : 'componentes' }}
            </div>
        </div>

        {{-- US-05: cambio de estado directo desde el listado (sin entrar a edit). --}}
        @if ($proyecto->estado !== \App\Models\Proyecto::ESTADO_ARCHIVADO)
            <div class="mb-3">
                <form action="{{ route('proyectos.estado', $proyecto) }}" method="POST" class="flex items-center gap-1.5">
                    @csrf
                    <select name="estado" onchange="this.form.submit()"
                        class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs font-medium text-gray-600 focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white">
                        @foreach (\App\Models\Proyecto::ESTADOS_ACTIVOS as $estadoValor)
                            <option value="{{ $estadoValor }}" {{ $proyecto->estado === $estadoValor ? 'selected' : '' }}>
                                {{ \App\Models\Proyecto::ESTADOS_LABELS[$estadoValor] ?? ucfirst($estadoValor) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        @else
            <div class="mb-3 text-xs text-gray-400 italic">Archivado</div>
        @endif

        <div class="flex items-center gap-2 pt-3 border-t border-gray-100 mt-auto">
            <a href="{{ route('proyectos.show', $proyecto) }}" class="flex-1 text-center text-xs font-semibold text-[#0054e9] hover:bg-blue-50 py-2 rounded-lg transition-colors">Ver</a>
            @if ($proyecto->estado !== \App\Models\Proyecto::ESTADO_ARCHIVADO)
                <a href="{{ route('proyectos.edit', $proyecto) }}" class="flex-1 text-center text-xs font-semibold text-gray-600 hover:bg-gray-100 py-2 rounded-lg transition-colors">Editar</a>
                <button type="button" onclick="archivarProyecto('{{ route('proyectos.destroy', $proyecto) }}')"
                    class="text-xs font-semibold text-[#8f6b00] hover:bg-[#ffc409]/10 px-3 py-2 rounded-lg transition-colors">Archivar</button>
            @endif
        </div>
    </div>
</div>
