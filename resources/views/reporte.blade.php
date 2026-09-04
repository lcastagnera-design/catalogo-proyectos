<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Proyectos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #0054e9;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header .logo {
            background: #0054e9;
            color: #ffffff;
            font-weight: bold;
            font-size: 14px;
            padding: 8px 10px;
            border-radius: 6px;
        }
        .header h1 {
            font-size: 18px;
            color: #0054e9;
        }
        .header .fecha {
            font-size: 11px;
            color: #6b7280;
            text-align: right;
        }
        h2 {
            font-size: 14px;
            color: #0054e9;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }
        .seccion {
            margin-bottom: 22px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #0054e9;
            color: #ffffff;
            text-align: left;
            padding: 7px 8px;
            font-size: 11px;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        tr:nth-child(even) td { background: #f3f6f9; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .resumen-grid {
            width: 100%;
            margin-top: 8px;
        }
        .resumen-grid td, .resumen-grid th { border: 1px solid #e5e7eb; }
        .total {
            font-weight: bold;
            background: #CAF2EC !important;
            color: #2a7a6a;
        }
        .footer {
            margin-top: 28px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            <div class="logo">CP</div>
            <h1>Catálogo de Proyectos</h1>
        </div>
        <div class="fecha">
            Reporte del portfolio<br>
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="seccion">
        <h2>Resumen por repartición</h2>
        <table class="resumen-grid">
            <thead>
                <tr>
                    <th>Reparticion</th>
                    <th style="text-align:right;">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($porReparticion as $reparticion => $cantidad)
                    <tr>
                        <td>{{ $reparticion }}</td>
                        <td style="text-align:right;">{{ $cantidad }}</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td>Total</td>
                    <td style="text-align:right;">{{ $totalProyectos }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="seccion">
        <h2>Resumen por estado</h2>
        <table class="resumen-grid">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th style="text-align:right;">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estadosOrden as $estado)
                    <tr>
                        <td>{{ \App\Models\Proyecto::ESTADOS_LABELS[$estado] ?? ucfirst($estado) }}</td>
                        <td style="text-align:right;">{{ $porEstado[$estado] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="seccion">
        <h2>Listado de proyectos</h2>
        <table>
            <thead>
                <tr>
                    <th style="width:30%;">Proyecto</th>
                    <th>Reparticion</th>
                    <th>Estado</th>
                    <th style="width:12%;">Componentes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($proyectos as $proyecto)
                    <tr>
                        <td>
                            <strong>{{ $proyecto->nombre_proyecto }}</strong><br>
                            <span style="color:#6b7280;">{{ $proyecto->nombre_proyecto_marca }}</span>
                        </td>
                        <td>{{ $proyecto->areaSolicitante
                            ? ($proyecto->areaSolicitante->nivel4 !== null && $proyecto->areaSolicitante->nivel4 !== '-' && $proyecto->areaSolicitante->nivel3 !== '-'
                                ? $proyecto->areaSolicitante->nivel3 . ' · ' . $proyecto->areaSolicitante->nivel4
                                : ($proyecto->areaSolicitante->nivel3 ?? $proyecto->areaSolicitante->nivel2))
                            : 'Sin área' }}</td>
                        <td>
                            @php
                                $badgeBg = [
                                    'planificacion' => '#CAF2EC',
                                    'ejecucion' => '#0054e9',
                                    'frenado' => '#ffc409',
                                    'finalizado' => '#22c55e',
                                ][$proyecto->estado] ?? '#e5e7eb';
                                $badgeColor = $proyecto->estado === 'ejecucion' ? '#ffffff' : '#1f2937';
                            @endphp
                            <span class="badge" style="background:{{ $badgeBg }}; color:{{ $badgeColor }};">{{ $proyecto->estadoLabel() }}</span>
                        </td>
                        <td style="text-align:right;">{{ $proyecto->componentes_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:#9ca3af;">No hay proyectos activos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        Catálogo de Proyectos - Sistema de Gestión de Proyectos
    </div>
</body>
</html>
