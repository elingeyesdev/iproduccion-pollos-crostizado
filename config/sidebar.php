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
                ['label' => 'Recetas y Rendimientos', 'icon' => 'fa-solid fa-mortar-pestle', 'route' => 'recetas.index', 'active' => 'recetas.*'],
                ['label' => 'Tipo de producto', 'icon' => 'fa-solid fa-layer-group', 'route' => 'tipo-productos.index', 'active' => 'tipo-productos.*'],
                ['label' => 'Componente de producto', 'icon' => 'fa-solid fa-puzzle-piece', 'route' => 'componente-productos.index', 'active' => 'componente-productos.*'],
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
                ['label' => 'Orden de produccion', 'icon' => 'fa-solid fa-clipboard-list', 'route' => 'orden-producciones.index', 'active' => 'orden-producciones.*'],
                ['label' => 'Administrar Produccion', 'icon' => 'fa-solid fa-industry', 'route' => 'producciones.index', 'active' => 'producciones.*'],
                ['label' => 'Mermas', 'icon' => 'fa-solid fa-trash-can', 'route' => 'mermas.index', 'active' => 'mermas.*'],
                ['label' => 'Administrar Ventas', 'icon' => 'fa-solid fa-cash-register', 'route' => 'ventas.index', 'active' => 'ventas.*'],
            ],
        ],
        [
            'title' => 'Modulo de Reportes',
            'module_icon' => 'bi bi-graph-up-arrow',
            'items' => [
                ['label' => 'Reporte de insumos', 'icon' => 'fa-solid fa-file-lines', 'route' => 'reportes.insumos', 'active' => 'reportes.insumos'],
                ['label' => 'Reporte de Productos', 'icon' => 'fa-solid fa-file-invoice', 'route' => 'reportes.productos', 'active' => 'reportes.productos'],
                ['label' => 'Reporte mensual de ventas', 'icon' => 'fa-solid fa-chart-column', 'route' => 'reportes.ventas-mensual', 'active' => 'reportes.ventas-mensual'],
                ['label' => 'Cantidad Stock', 'icon' => 'fa-solid fa-warehouse', 'route' => 'reportes.stock', 'active' => 'reportes.stock'],
            ],
        ],
    ],
];
