@extends('proyectos.layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('proyectos.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#0054e9] transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver al catálogo
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-6 sm:p-8 max-w-4xl">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Editar proyecto</h1>

        <form action="{{ route('proyectos.update', $proyecto) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            @include('proyectos.partials._form', ['proyecto' => $proyecto])

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="inline-flex items-center gap-1.5 bg-[#0054e9] hover:bg-[#003eb3] text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar cambios
                </button>
                <a href="{{ route('proyectos.show', $proyecto) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 px-4 py-2.5">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
