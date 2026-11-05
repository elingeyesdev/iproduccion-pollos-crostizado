<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de producción {{ $orden?->numero_orden }}</title>
    <style>
        @page { margin: 28px 34px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { color: #b30000; font-size: 18px; margin: 0; }
        h2 { font-size: 13px; color: #b30000; border-bottom: 1px solid #b30000; padding-bottom: 3px; margin: 18px 0 8px; }
        .encabezado { border-bottom: 2px solid #b30000; padding-bottom: 8px; margin-bottom: 10px; }
        .encabezado small { color: #666; }
        table { width: 100%; border-collapse: collapse; }
        .datos td { padding: 3px 4px; }
        .datos td.etiqueta { font-weight: bold; width: 32%; }
        .tabla th { background: #b30000; color: #fff; padding: 5px; text-align: left; }
        .tabla td { padding: 5px; border-bottom: 1px solid #ddd; }
        .tabla tr:nth-child(even) td { background: #f6f6f6; }
        .derecha, .tabla th.derecha { text-align: right; }
        .resumen td { padding: 5px; border: 1px solid #ddd; }
        .resumen td.etiqueta { background: #f6f6f6; font-weight: bold; width: 55%; }
        .total td { font-weight: bold; background: #fdecec; }
        .merma { color: #b30000; }
        .nota { color: #666; font-size: 9px; margin-top: 4px; }
        .firmas { margin-top: 60px; }
        .firmas td { width: 50%; text-align: center; padding-top: 4px; }
        .linea { border-top: 1px solid #333; width: 70%; margin: 0 auto 3px; }
        .pie { position: fixed; bottom: -10px; left: 0; right: 0; font-size: 9px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="encabezado">
        <h1>Informe de producción</h1>
        <small>{{ config('app.name') }} · Generado el {{ now()->format('d/m/Y H:i') }}</small>
    </div>

    <h2>Datos generales</h2>
    <table class="datos">
        <tr><td class="etiqueta">Orden de producción</td><td>{{ $orden?->numero_orden ?? '-' }}</td></tr>
        <tr><td class="etiqueta">Fecha programada</td><td>{{ $orden?->fecha_programada ? \Illuminate\Support\Carbon::parse($orden->fecha_programada)->format('d/m/Y') : '-' }}</td></tr>
        <tr><td class="etiqueta">Fecha de ejecución</td><td>{{ $produccion->created_at?->format('d/m/Y H:i') ?? '-' }}</td></tr>
        <tr>
            <td class="etiqueta">Regla aplicada</td>
            <td>
                @if($produccion->reglaConversion)
                    {{ $produccion->reglaConversion->insumo?->nombre ?? 'Insumo' }} → {{ $produccion->producto?->nombre ?? 'Resultado' }}
                    ({{ $produccion->reglaConversion->tipo_conversion }})
                @else
                    -
                @endif
            </td>
        </tr>
        <tr><td class="etiqueta">Estado</td><td>{{ (int) $produccion->estado === 1 ? 'Activo' : 'Inactivo' }}</td></tr>
        @if($orden?->observaciones)
            <tr><td class="etiqueta">Observaciones</td><td>{{ $orden->observaciones }}</td></tr>
        @endif
    </table>

    <h2>Insumos consumidos (descontados del stock)</h2>
    <table class="tabla">
        <thead>
            <tr>
                <th>#</th>
                <th>Insumo</th>
                <th class="derecha">Cantidad consumida</th>
                <th>Unidad</th>
                <th class="derecha">Stock actual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($insumosConsumidos as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item['insumo'] }}{{ $item['principal'] ? ' (principal)' : '' }}</td>
                    <td class="derecha">{{ number_format($item['cantidad'], 2) }}</td>
                    <td>{{ $item['unidad'] }}</td>
                    <td class="derecha">{{ number_format($item['stock_actual'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="nota">El stock actual corresponde al momento de generar este informe.</p>

    <h2>Resultado de producción</h2>
    <table class="resumen">
        <tr><td class="etiqueta">Producto obtenido</td><td>{{ $produccion->producto?->nombre ?? '-' }}</td></tr>
        <tr><td class="etiqueta">Cantidad producida</td><td>{{ number_format($resumen['cantidad_obtenida'], 2) }} {{ $resumen['unidad_salida'] }}</td></tr>
        <tr><td class="etiqueta">Total merma</td><td class="merma">- {{ number_format($resumen['total_merma'], 2) }} {{ $resumen['unidad_salida'] }}</td></tr>
        <tr class="total"><td class="etiqueta">Cantidad neta (sin merma)</td><td>{{ number_format($resumen['cantidad_neta'], 2) }} {{ $resumen['unidad_salida'] }}</td></tr>
        <tr><td class="etiqueta">Porcentaje de merma</td><td>{{ number_format($resumen['porcentaje_merma'], 2) }} %</td></tr>
        @if($resumen['rendimiento'] !== null)
            <tr>
                <td class="etiqueta">Rendimiento por {{ $resumen['insumo_principal']['unidad'] }} de {{ $resumen['insumo_principal']['insumo'] }}</td>
                <td>{{ number_format($resumen['rendimiento'], 2) }} {{ $resumen['unidad_salida'] }}</td>
            </tr>
        @endif
    </table>

    <h2>Mermas registradas ({{ $mermas->count() }})</h2>
    @if($mermas->isEmpty())
        <p>No se registraron mermas para este lote.</p>
    @else
        <table class="tabla">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha de registro</th>
                    <th class="derecha">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mermas as $i => $merma)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $merma->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="derecha">{{ number_format((float) $merma->cantidad, 2) }} {{ $resumen['unidad_salida'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="firmas">
        <tr>
            <td><div class="linea"></div>Responsable de producción</td>
            <td><div class="linea"></div>Supervisor</td>
        </tr>
    </table>

    <div class="pie">{{ config('app.name') }} · Informe de producción {{ $orden?->numero_orden }}</div>
</body>
</html>
