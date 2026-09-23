<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $mesActual = now()->month;
        $anioActual = now()->year;
        $mesAnterior = now()->subMonth()->month;
        $anioAnterior = now()->subMonth()->year;

        $usuariosActivos = User::count();
        $usuariosMesAnt = User::whereMonth('created_at', $mesAnterior)
            ->whereYear('created_at', $anioAnterior)
            ->count();
        $pctUsuarios = $usuariosMesAnt > 0
            ? round((($usuariosActivos - $usuariosMesAnt) / $usuariosMesAnt) * 100, 1)
            : 0;

        $totalClientes = Cliente::count();
        $clientesMesAnt = Cliente::whereMonth('created_at', $mesAnterior)
            ->whereYear('created_at', $anioAnterior)
            ->count();
        $pctClientes = $clientesMesAnt > 0
            ? round((($totalClientes - $clientesMesAnt) / $clientesMesAnt) * 100, 1)
            : 0;

        $totalProductos = Producto::count();
        $productosMesAnt = Producto::whereMonth('created_at', $mesAnterior)
            ->whereYear('created_at', $anioAnterior)
            ->count();
        $pctProductos = $productosMesAnt > 0
            ? round((($totalProductos - $productosMesAnt) / $productosMesAnt) * 100, 1)
            : 0;

        $ventasMes = Venta::whereMonth('fecha_venta', $mesActual)
            ->whereYear('fecha_venta', $anioActual)
            ->sum('total');
        $ventasMesAnt = Venta::whereMonth('fecha_venta', $mesAnterior)
            ->whereYear('fecha_venta', $anioAnterior)
            ->sum('total');
        $pctVentas = $ventasMesAnt > 0
            ? round((($ventasMes - $ventasMesAnt) / $ventasMesAnt) * 100, 1)
            : 0;

        return view('dashboard', [
            'stats' => [
                'usuarios' => [
                    'valor' => $usuariosActivos,
                    'pct' => $pctUsuarios,
                ],
                'clientes' => [
                    'valor' => $totalClientes,
                    'pct' => $pctClientes,
                ],
                'productos' => [
                    'valor' => $totalProductos,
                    'pct' => $pctProductos,
                ],
                'ventas' => [
                    'valor' => $ventasMes,
                    'pct' => $pctVentas,
                ],
            ],
        ]);
    }
}
