@extends('proyectos.layouts.app')

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
            <p class="text-gray-500 text-sm">Resumen del portfolio de proyectos (no archivados).</p>
        </div>
        <a href="{{ route('reporte') }}" class="inline-flex items-center gap-1.5 bg-[#0054e9] hover:bg-[#003eb3] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Exportar PDF
        </a>
    </div>

    @if ($totalProyectos === 0)
        <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-12 text-center">
            <h3 class="text-lg font-semibold text-gray-600 mb-1">No hay proyectos cargados</h3>
            <p class="text-sm text-gray-400 mb-4">Creá proyectos para ver las estadísticas del dashboard.</p>
            <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-1.5 bg-[#0054e9] hover:bg-[#003eb3] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                Nuevo proyecto
            </a>
        </div>
    @else
        <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Proyectos activos</p>
                <p class="text-3xl font-bold text-[#0054e9]">{{ $totalProyectos }}</p>
            </div>
            @php
                $maxRep = count($datosReparticion) ? max($datosReparticion) : 0;
                $maxEst = count($datosEstado) ? max($datosEstado) : 0;
            @endphp
            <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Reparticiones</p>
                <p class="text-3xl font-bold text-[#2a7a6a]">{{ count($etiquetasReparticion) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mayor repartición</p>
                <p class="text-3xl font-bold text-[#8f6b00]">{{ $maxRep }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mayor estado</p>
                <p class="text-3xl font-bold text-green-600">{{ $maxEst }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            {{-- Gráfico de torta: proyectos por repartición (US-06) --}}
            <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-5">
                <h2 class="text-sm font-bold text-gray-700 mb-4">Proyectos por repartición</h2>
                <div class="h-80">
                    <canvas id="chartReparticion"></canvas>
                </div>
            </div>

            {{-- Gráfico de barras: proyectos por estado (US-06) --}}
            <div class="bg-white rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,.08)] border border-gray-100 p-5">
                <h2 class="text-sm font-bold text-gray-700 mb-4">Proyectos por estado</h2>
                <div class="h-80">
                    <canvas id="chartEstado"></canvas>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($totalProyectos > 0)
            const etiquetasReparticion = @json($etiquetasReparticion);
            const datosReparticion = @json($datosReparticion);
            const labelsEstados = @json($labelsEstados);
            const datosEstado = @json($datosEstado);

            // Paleta Equipoba.
            const colores = [
                '#0054e9', '#8de2d6', '#ffc409', '#CAF2EC', '#2a7a6a',
                '#7c3aed', '#ef6c9c', '#0ea5e9', '#84cc16', '#f97316',
            ];

            // Torta: proyectos por repartición.
            const ctxPie = document.getElementById('chartReparticion');
            if (ctxPie) {
                new Chart(ctxPie, {
                    type: 'doughnut',
                    data: {
                        labels: etiquetasReparticion,
                        datasets: [{
                            data: datosReparticion,
                            backgroundColor: colores,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { font: { family: 'Archivo' }, boxWidth: 14 }
                            }
                        }
                    }
                });
            }

            // Barras: proyectos por estado.
            const ctxBar = document.getElementById('chartEstado');
            if (ctxBar) {
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: labelsEstados,
                        datasets: [{
                            label: 'Proyectos',
                            data: datosEstado,
                            backgroundColor: '#0054e9',
                            hoverBackgroundColor: '#003eb3',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
            @endif
        });
    </script>
@endpush
