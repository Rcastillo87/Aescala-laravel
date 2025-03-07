<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\InventarioMaterialController;
use App\Http\Controllers\HerramientaController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\FinanzaController;

// Middleware de autenticación personalizado (equivalente a 'auth' en Express)
Route::middleware('auth:api')->group(function () {

    // Rutas de Usuario
    Route::get('/user/lista_users', [UserController::class, 'lista_users']);
    Route::get('/user/lista_usersxrol', [UserController::class, 'lista_usersxrol']);
    Route::post('/user/insert_user', [UserController::class, 'insert_user']);
    Route::get('/user/lista_roles', [UserController::class, 'lista_roles']);

    // Rutas de Proyecto
    Route::get('/proyecto/lista_proyectosxtipo', [ProyectoController::class, 'lista_proyectosxtipo']);
    Route::get('/proyecto/un_proyecto', [ProyectoController::class, 'un_proyecto']);
    Route::post('/proyecto/insert_proyecto', [ProyectoController::class, 'insert_proyecto']);
    Route::get('/proyecto/lista_estados', [ProyectoController::class, 'lista_estados']);
    Route::get('/proyecto/lista_dpt_y_ciudades', [ProyectoController::class, 'lista_dpt_y_ciudades']);

    // Rutas de Tarea
    Route::get('/tarea/lista_tipos_tarea', [TareaController::class, 'lista_tipos_tarea']);
    Route::get('/tarea/lista_tarea_estados', [TareaController::class, 'lista_tarea_estados']);
    Route::get('/tarea/lista_tareas', [TareaController::class, 'lista_tareas']);
    Route::post('/tarea/insert_tarea', [TareaController::class, 'insert_tarea']);
    Route::get('/tarea/lista_tarea_avances', [TareaController::class, 'lista_tarea_avaces']);
    Route::post('/tarea/insert_tarea_avance', [TareaController::class, 'insert_tarea_avace']);
    Route::delete('/tarea/delete_tarea_avance', [TareaController::class, 'delete_tarea_avace']);

    // Rutas de Inventario Material
    Route::get('/inventario_material/lista_inventario_material', [InventarioMaterialController::class, 'lista_inventario_material']);
    Route::get('/inventario_material/lista_inventario_unidades', [InventarioMaterialController::class, 'lista_inventario_unidades']);
    Route::post('/inventario_material/insert_inventario_material', [InventarioMaterialController::class, 'insert_inventario_material']);
    Route::get('/inventario_material/lista_inventario_solicitud', [InventarioMaterialController::class, 'lista_inventario_solicitud']);
    Route::post('/inventario_material/insert_inventario_solicitud', [InventarioMaterialController::class, 'insert_inventario_solicitud']);

    // Rutas de Herramienta
    Route::get('/herramienta/lista_herramientas', [HerramientaController::class, 'lista_herramientas']);
    Route::post('/herramienta/insert_herramienta', [HerramientaController::class, 'insert_herramienta']);
    Route::post('/herramienta/insert_prestamo', [HerramientaController::class, 'insert_prestamo']);

    // Rutas de Cotización
    Route::get('/cotizacion/lista_cotizacion', [CotizacionController::class, 'lista_cotizacionxproyecto']);
    Route::get('/cotizacion/una_cotizacion', [CotizacionController::class, 'uno_cotizacionxproyecto']);
    Route::get('/cotizacion/comparativo_cotixentregas', [CotizacionController::class, 'comparar_cotixdespachos']);
    Route::post('/cotizacion/insert_cotizacion', [CotizacionController::class, 'insert_cotizacion']);
    Route::delete('/cotizacion/delete_cotizacion', [CotizacionController::class, 'delete_cotizacion']);

    // Rutas de Finanzas
    Route::get('/finanzas/lista_finanzas', [FinanzaController::class, 'lista_finanzas']);
    Route::get('/finanzas/uno_finanza', [FinanzaController::class, 'uno_finanza']);
    Route::get('/finanzas/finanzas_proyecto', [FinanzaController::class, 'finanzas_proyecto']);
    Route::post('/finanzas/insert_finanza', [FinanzaController::class, 'insert_finanza']);
    Route::delete('/finanzas/delete_finanza', [FinanzaController::class, 'delete_finanza']);

    // Rutas de Proveedores
    Route::get('/inventario_proveedores/lista_proveedores', [InventarioMaterialController::class, 'lista_proveedores']);
    Route::post('/inventario_proveedores/insert_proveedor', [InventarioMaterialController::class, 'insert_proveedor']);
    Route::post('/inventario_proveedores/insert_pedidos', [InventarioMaterialController::class, 'insert_pedidos']);
    Route::get('/inventario_proveedores/lista_pedidos_proveedor', [InventarioMaterialController::class, 'lista_pedidos_proveedor']);
    Route::get('/inventario_proveedores/lista_items_pedido', [InventarioMaterialController::class, 'lista_items_pedido']);
    Route::delete('/inventario_proveedores/delete_items_pedido', [InventarioMaterialController::class, 'delete_items_pedido']);
});

// Rutas públicas
Route::post('/loguin', [UserController::class, 'loguin']);
Route::get('/descarga/data_base', [UserController::class, 'data_base']);