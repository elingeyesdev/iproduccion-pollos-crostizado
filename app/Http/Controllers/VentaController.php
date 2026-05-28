<?php

namespace App\Http\Controllers;

use App\Http\Requests\VentaRequest;
use App\Models\Venta;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\TipoProducto;
use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\VentaPago;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $ventas = Venta::query()
            ->with(['cliente:id,nombre', 'pagos:id,venta_id,metodo_pago,monto'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('fecha_venta', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('venta.index', compact('ventas', 'search'));
    }

    public function create(): View
    {
        $venta = new Venta();
        $clientes = Cliente::where('estado', 1)->orderBy('nombre')->get();
        $tipoProductos = TipoProducto::where('estado', 1)->orderBy('nombre')->get();
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();

        return view('venta.create', compact('venta', 'clientes', 'tipoProductos', 'productos'));
    }

    public function store(VentaRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['fecha_venta'] = $data['fecha_venta'] ?? Carbon::now()->toDateString();
            $data['descuento'] = $data['descuento'] ?? 0;
            $pagoEfectivo = (float) ($data['pago_efectivo'] ?? 0);
            $pagoQr = (float) ($data['pago_qr'] ?? 0);

            if ($pagoEfectivo <= 0 && $pagoQr <= 0) {
                throw new \RuntimeException('Debes registrar al menos un pago (efectivo o QR).');
            }

            $totalPagado = round($pagoEfectivo + $pagoQr, 2);
            $totalVenta = round((float) $data['total'], 2);

            if (abs($totalPagado - $totalVenta) > 0.01) {
                throw new \RuntimeException('La suma de pagos debe ser igual al total de la venta.');
            }
            
            $venta = Venta::create([
                'cliente_id' => $data['cliente_id'],
                'fecha_venta' => $data['fecha_venta'],
                'total' => $data['total'],
                'estado' => $data['estado'] ?? 1,
                'descuento' => $data['descuento'],
            ]);

            $productoIds = collect($request->productos)->map(fn ($id) => (int) $id)->all();
            $productosMap = Producto::query()
                ->whereIn('id', $productoIds)
                ->get(['id', 'precio'])
                ->keyBy('id');

            // Crear detalles
            foreach ($request->productos as $index => $producto_id) {
                $producto = $productosMap->get((int) $producto_id);

                if (!$producto) {
                    throw new \RuntimeException('Producto no encontrado para la venta.');
                }

                $precioUnitario = (float) $producto->precio;
                $cantidad = (float) $request->cantidades[$index];

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto_id,
                    'precio_unitario' => $precioUnitario,
                    'cantidad' => $cantidad,
                    'sub_total' => $precioUnitario * $cantidad,
                    'estado' => 1
                ]);
            }

            if ($pagoEfectivo > 0) {
                VentaPago::create([
                    'venta_id' => $venta->id,
                    'metodo_pago' => 'Efectivo',
                    'monto' => $pagoEfectivo,
                    'estado' => 1,
                ]);
            }

            if ($pagoQr > 0) {
                VentaPago::create([
                    'venta_id' => $venta->id,
                    'metodo_pago' => 'QR',
                    'monto' => $pagoQr,
                    'estado' => 1,
                ]);
            }

            DB::commit();

            return Redirect::route('ventas.index')
                ->with('success', 'Venta creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()
                ->withInput()
                ->with('error', 'Error al crear la venta: ' . $e->getMessage());
        }
    }

    public function show(Venta $venta): View
    {
        $venta->load(['cliente', 'detalles.producto', 'pagos']);
        return view('venta.show', compact('venta'));
    }

    public function edit(Venta $venta): View
    {
        $clientes = Cliente::where('estado', 1)->orderBy('nombre')->get();
        return view('venta.edit', compact('venta', 'clientes'));
    }

    public function update(VentaRequest $request, Venta $venta): RedirectResponse
    {
        $venta->update($request->validated());

        return Redirect::route('ventas.index')
            ->with('success', 'Venta actualizada correctamente.');
    }

    public function destroy(Venta $venta): RedirectResponse
    {
        $venta->update(['estado' => 0]);

        return Redirect::route('ventas.index')
            ->with('success', 'Venta eliminada correctamente.');
    }
}
