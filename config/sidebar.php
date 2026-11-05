<?php

return [
    'crud_sections' => [
        [
            'title' => 'Modulo de Acceso y Seguridad',
            'module_icon' => 'bi bi-shield-lock',
            'items' => [
                ['label' => 'Administrar usuario', 'icon' => 'fa-solid fa-user-gear', 'route' => 'users.index', 'active' => 'users.*'],
                ['label' => 'Administrar rol', 'icon' => 'fa-solid fa-key', 'route' => 'roles.index', 'active' => 'roles.*'],
                ['label' => 'Asignar rol usuario', 'icon' => 'fa-solid fa-user-check', 'route' => 'role-assignments.index', 'active' => 'role-assignments.*'],
                ['label' => 'Iniciar sesion', 'icon' => 'fa-solid fa-right-to-bracket', 'route' => 'login', 'active' => 'login'],
            ],
        ],
        [
            'title' => 'Modulo de parametrizacion',
            'module_icon' => 'bi bi-sliders2-vertical',
            'items' => [
                ['label' => 'Administrar insumos', 'icon' => 'fa-solid fa-boxes-stacked', 'route' => 'insumos.index', 'active' => 'insumos.*'],
                ['label' => 'Administrar producto', 'icon' => 'fa-solid fa-drumstick-bite', 'route' => 'productos.index', 'active' => 'productos.*'],
                ['label' => 'Producto-Composición', 'icon' => 'fa-solid fa-layer-group', 'route' => 'productos-composicion.index', 'active' => 'productos-composicion.*'],
                ['label' => 'Reglas de conversión', 'icon' => 'fa-solid fa-calculator', 'route' => 'regla-conversiones.index', 'active' => 'regla-conversiones.*'],
                ['label' => 'Tipo de producto', 'icon' => 'fa-solid fa-layer-group', 'route' => 'tipo-productos.index', 'active' => 'tipo-productos.*'],
                ['label' => 'Administrar Unidad de Medida', 'icon' => 'fa-solid fa-ruler', 'route' => 'unidad-medidas.index', 'active' => 'unidad-medidas.*'],
                ['label' => 'Administrar Cliente', 'icon' => 'fa-solid fa-users', 'route' => 'clientes.index', 'active' => 'clientes.*'],
            ],
        ],
        [
            'title' => 'Modulo de transaccionales',
            'module_icon' => 'bi bi-cart-check',
            'items' => [
                ['label' => 'Ingreso insumos', 'icon' => 'fa-solid fa-truck-ramp-box', 'route' => 'ingreso-insumos.index', 'active' => 'ingreso-insumos.*'],
                ['label' => 'Ingreso bebidas', 'icon' => 'fa-solid fa-bottle-water', 'route' => 'ingreso-bebidas.index', 'active' => 'ingreso-bebidas.*'],
                ['label' => 'Órdenes de producción', 'icon' => 'fa-solid fa-file-invoice', 'route' => 'orden-producciones.index', 'active' => 'orden-producciones.*'],
                ['label' => 'Ejecutar Producción', 'icon' => 'fa-solid fa-fire-burner', 'route' => 'producciones.index', 'active' => 'producciones.*'],
                ['label' => 'Mermas', 'icon' => 'fa-solid fa-trash-can', 'route' => 'mermas.index', 'active' => 'mermas.*'],
                ['label' => 'Administrar Ventas', 'icon' => 'fa-solid fa-cash-register', 'route' => 'ventas.index', 'active' => 'ventas.*'],
            ],
        ],
        [
            'title' => 'Módulo de Reportes',
            'module_icon' => 'bi bi-file-earmark-bar-graph',
            'items' => [
                ['label' => 'Reporte de insumos', 'icon' => 'fa-solid fa-file-lines', 'route' => 'reportes.insumos', 'active' => 'reportes.insumos'],
                ['label' => 'Reporte de Productos', 'icon' => 'fa-solid fa-file-invoice', 'route' => 'reportes.productos', 'active' => 'reportes.productos'],
                ['label' => 'Reporte mensual de ventas', 'icon' => 'fa-solid fa-chart-column', 'route' => 'reportes.ventas-mensual', 'active' => 'reportes.ventas-mensual'],
                ['label' => 'Reporte mensual de producción', 'icon' => 'fa-solid fa-industry', 'route' => 'reportes.produccion-mensual', 'active' => 'reportes.produccion-mensual'],
                ['label' => 'Cantidad Stock', 'icon' => 'fa-solid fa-warehouse', 'route' => 'reportes.stock', 'active' => 'reportes.stock'],
            ],
        ],
        [
            'title' => 'Módulo de KPI - Indicadores de gestión',
            'module_icon' => 'bi bi-graph-up-arrow',
            'items' => [
                ['label' => 'Indicadores de gestión (KPI)', 'icon' => 'fa-solid fa-gauge-high', 'route' => 'reportes.indicadores', 'active' => 'reportes.indicadores'],
            ],
        ],
    ],
];
