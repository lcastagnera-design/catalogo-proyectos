@php
    // Cada fila de componente. En create el index es un placeholder que JS
    // renombra; en edit se usa el índice real del componente guardado.
    $idx = $idx ?? 0;
    $componente = $componente ?? new \App\Models\ProyectoComponente();
@endphp

<div class="componente-row border border-gray-200 rounded-2xl bg-gray-50/50">
    <div class="componente-cabezal cursor-pointer flex items-center justify-between gap-3 p-4 sm:p-5 select-none">
        <div class="min-w-0">
            <h3 class="text-sm font-bold text-gray-700">Componente <span class="componente-index">{{ $idx + 1 }}</span></h3>
            <p class="componente-resumen text-xs text-gray-500 truncate mt-0.5">
                Nombre · Tipo · Tecnología · Versión
            </p>
        </div>
        <div class="flex items-center gap-1 shrink-0">
            @if ($idx > 0)
                <button type="button" class="quitar-componente inline-flex items-center gap-1 text-xs font-semibold text-red-500 hover:text-red-700 transition-colors px-2 py-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Quitar
                </button>
            @endif
            <svg class="componente-chevron w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
    </div>

    <div class="componente-cuerpo grid grid-cols-1 sm:grid-cols-2 gap-4 px-4 sm:px-5 pb-4 sm:pb-5">
        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre del componente <span class="text-red-500">*</span></label>
            <input type="text" name="componentes[{{ $idx }}][nombre_componente]" value="{{ old("componentes.$idx.nombre_componente", $componente->nombre_componente) }}"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo de componente <span class="text-red-500">*</span></label>
            <select name="componentes[{{ $idx }}][tipo_componente]"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
                <option value="">Seleccionar tipo…</option>
                @foreach ($tipoComponentes as $tipo)
                    <option value="{{ $tipo }}" {{ old("componentes.$idx.tipo_componente", $componente->tipo_componente) === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tecnología <span class="text-red-500">*</span></label>
            <select name="componentes[{{ $idx }}][tecnologia]"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
                <option value="">Seleccionar tecnología…</option>
                @foreach ($tecnologias as $tecnologia)
                    <option value="{{ $tecnologia->nombre }}" {{ old("componentes.$idx.tecnologia", $componente->tecnologia) === $tecnologia->nombre ? 'selected' : '' }}>{{ $tecnologia->nombre }} ({{ $tecnologia->categoria }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Versión <span class="text-red-500">*</span></label>
            <input type="text" name="componentes[{{ $idx }}][version]" value="{{ old("componentes.$idx.version", $componente->version) }}" placeholder="Ej: 2.1.0"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Exposición a Internet <span class="text-red-500">*</span></label>
            <select name="componentes[{{ $idx }}][exposicion_internet]"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
                <option value="">Seleccionar…</option>
                @foreach ($exposiciones as $expo)
                    <option value="{{ $expo }}" {{ old("componentes.$idx.exposicion_internet", $componente->exposicion_internet) === $expo ? 'selected' : '' }}>{{ $expo }}</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Repositorio GitLab <span class="text-red-500">*</span></label>
            <input type="text" name="componentes[{{ $idx }}][repositorio_gitlab]" value="{{ old("componentes.$idx.repositorio_gitlab", $componente->repositorio_gitlab) }}" placeholder="https://gitlab.com/grupo/repo"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1">URL dev <span class="text-red-500">*</span></label>
            <input type="text" name="componentes[{{ $idx }}][url_dev]" value="{{ old("componentes.$idx.url_dev", $componente->url_dev) }}" placeholder="https://dev.ejemplo.gob.ar"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre normalizado <span class="text-red-500">*</span></label>
            <input type="text" name="componentes[{{ $idx }}][nombre_normalizado]" value="{{ old("componentes.$idx.nombre_normalizado", $componente->nombre_normalizado) }}" placeholder="nombre-normalizado-01"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1">URL OpenShift <span class="text-red-500">*</span></label>
            <input type="text" name="componentes[{{ $idx }}][url_openshift]" value="{{ old("componentes.$idx.url_openshift", $componente->url_openshift) }}" placeholder="https://openshift.ejemplo.gob.ar/app"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Observaciones</label>
            <textarea name="componentes[{{ $idx }}][observaciones]" rows="2"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0054e9]/30 focus:border-[#0054e9] bg-white transition-colors resize-y">{{ old("componentes.$idx.observaciones", $componente->observaciones) }}</textarea>
        </div>
    </div>
</div>
