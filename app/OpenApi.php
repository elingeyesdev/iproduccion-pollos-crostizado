<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'API Producción Pollos Krosty',
    description: 'API para gestión de productos, insumos, recetas, producción, ventas, usuarios y autenticación.'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Token obtenido en POST /api/login. Ingresar solo el token, sin la palabra Bearer.'
)]
class OpenApi
{
}
