<?php

/**
 * ============================================================
 *  config/roles.php
 *  Fuente única de verdad para permisos de módulos y acciones.
 *
 *  ROLES DEL SISTEMA
 *  -----------------
 *  isAdmin        → Administrador  (id_rol: 1)
 *  isUser         → Coordinador    (id_rol: 2)
 *  isColab        → Arquitecto     (id_rol: 3)
 *  isComer        → Comercial      (id_rol: 4)
 *  iscartera      → Cartera        (id_rol: 5)
 *  isAnalista     → Analista       (id_rol: 6)
 *  isContratista  → Contratista    (id_rol: 7)
 *  isTecnico      → Tecnico        (id_rol: 8)
 *  isAlmacenista  → Almacenista    (id_rol: 9)
 *  isDiseno       → Diseñador    (id_rol: 10)
 * ============================================================
 */

return [

    // ==========================================================
    //  MÓDULOS
    //  Controla qué roles pueden ACCEDER a cada módulo completo.
    //  Usado por:
    //    - Middleware 'role:nombre_modulo'  → protege las rutas
    //    - Gate 'modulo.nombre'             → sidebar con @can
    // ==========================================================
    'modulos' => [

        // ── Grupo Proyectos ────────────────────────────────────
        'comercial'     => ['isAdmin', 'isComer'],
        'cartera'       => ['isAdmin', 'iscartera'],
        'proyecto'      => ['isAdmin', 'isUser', 'isColab', 'isComer', 'isAnalista', 'isContratista', 'isAlmacenista', 'isDiseno'],
        'otro_si'       => ['isAdmin', 'isUser', 'iscartera', 'isDiseno'],
        'tareas'        => ['isAdmin'],

        // ── Grupo Materiales ───────────────────────────────────
        'material'      => ['isAdmin', 'isAlmacenista'],
        'despachos'     => ['isAdmin', 'isAlmacenista'],
        'proveedor'     => ['isAdmin', 'isAlmacenista'],
        'pedidos'       => ['isAdmin', 'isAlmacenista'],
        'solicitud'     => ['isAdmin', 'isColab', 'isAnalista', 'isContratista', 'isTecnico', 'isAlmacenista'],

        // ── Elementos individuales ─────────────────────────────
        'herramienta'   => ['isAdmin'],
        'tracking'      => ['isAdmin'],
        'calendario'    => ['isAdmin'],
        'almacen'       => ['isAdmin'],
        'insumos'       => ['isAdmin', 'isAnalista', 'isAlmacenista'],  // ← NUEVO

        // ── Solo Admin ─────────────────────────────────────────
        'configuracion' => ['isAdmin'],
        'user'          => ['isAdmin'],
        'cotizacion'    => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'cobro'         => ['isAdmin', 'iscartera'],
        'planilla'      => ['isAdmin'],
    ],

    // ==========================================================
    //  ACCIONES
    //  Nombre de la acción = nombre REAL de la ruta en web.php
    //  Usado por:
    //    - Gate::authorize('ruta.nombre') en controladores
    //    - @can('ruta.nombre') en vistas para botones
    // ==========================================================
    'acciones' => [

        // ── user.* ─────────────────────────────────────────────
        'user.index'                => ['isAdmin'],
        'user.create'               => ['isAdmin'],
        'user.edit'                 => ['isAdmin'],
        'user.editStatus'           => ['isAdmin'],
        'user.save'                 => ['isAdmin'],

        // ── comercial.* ────────────────────────────────────────
        'comercial.index'           => ['isAdmin', 'isComer'],
        'comercial.create'          => ['isAdmin', 'isComer'],
        'comercial.edit'            => ['isAdmin', 'isComer'],
        'comercial.save'            => ['isAdmin', 'isComer'],
        'comercial.sendLinkByEmail' => ['isAdmin', 'isComer'],

        // ── herramienta.* ──────────────────────────────────────
        'herramienta.index'         => ['isAdmin'],
        'herramienta.create'        => ['isAdmin'],
        'herramienta.edit'          => ['isAdmin'],
        'herramienta.save'          => ['isAdmin'],
        'herramienta.listPrestamos' => ['isAdmin'],
        'herramienta.savePrestamo'  => ['isAdmin'],

        // ── proyecto.* ─────────────────────────────────────────
        'proyecto.index'                => ['isAdmin', 'isUser', 'isColab', 'isComer', 'isAnalista', 'isContratista', 'isAlmacenista', 'isDiseno'],
        'proyecto.begin'                => ['isAdmin'],
        'proyecto.editStatus'           => ['isAdmin', 'isComer'],
        'proyecto.saveTarea'            => ['isAdmin'],
        'proyecto.editTarea'            => ['isAdmin', 'isColab', 'isContratista'],
        'proyecto.deleteTarea'          => ['isAdmin'],
        'proyecto.listAvances'          => ['isAdmin', 'isColab', 'isContratista'],
        'proyecto.saveAvance'           => ['isAdmin', 'isColab', 'isContratista'],
        'proyecto.deleteAvance'         => ['isAdmin'],
        'proyecto.listaDespachos'       => ['isAdmin', 'isUser', 'isAlmacenista', 'isContratista', 'isColab'],
        'proyecto.pdfDespachos'         => ['isAdmin', 'isUser', 'isAlmacenista', 'isContratista', 'isColab'],
        'proyecto.pdfDespacho'          => ['isAdmin', 'isUser', 'isAlmacenista', 'isContratista', 'isColab'],
        'proyecto.listComparativo'      => ['isAdmin', 'isAnalista'],
        'proyecto.contratoPdf'          => ['isAdmin', 'isUser', 'isComer', 'isDiseno'],
        'proyecto.excelDespachoProyecto'=> ['isAdmin', 'isUser'],
        'proyecto.excelDespachosGeneral'=> ['isAdmin', 'isUser'],
        'proyecto.trataDatosPDF'        => ['isAdmin', 'isUser'],
        'proyecto.calendarioProyecto'   => ['isAdmin'],
        'proyecto.saveDiaNoLaborado'    => ['isAdmin'],

        // ── planilla.* ───────────────────────────────────────────
        'planilla.index'                    => ['isAdmin'],
        'planilla.savePlantilla'            => ['isAdmin'],
        'planilla.saveConfigPlantilla'      => ['isAdmin'],
        'planilla.deleteConfigPlantilla'    => ['isAdmin'],

        // ── tareas.* ───────────────────────────────────────────
        'tareas.index'              => ['isAdmin'],
        'tareas.editTarea'          => ['isAdmin', 'isColab', 'isContratista'],
        'tareas.deleteTarea'        => ['isAdmin'],
        'tareas.moverTarea'         => ['isAdmin', 'isColab'],
        'tareas.finTarea'           => ['isAdmin', 'isColab', 'isContratista'],
        'tareas.listAvances'        => ['isAdmin', 'isColab', 'isContratista'],
        'tareas.saveAvance'         => ['isAdmin', 'isColab', 'isContratista'],
        'tareas.deleteAvance'       => ['isAdmin'],

        // ── material.* ─────────────────────────────────────────
        'material.index'            => ['isAdmin', 'isAlmacenista'],
        'material.create'           => ['isAdmin', 'isAlmacenista'],
        'material.edit'             => ['isAdmin', 'isAlmacenista'],
        'material.save'             => ['isAdmin', 'isAlmacenista'],
        'material.editStatus'       => ['isAdmin', 'isAlmacenista'],
        'material.listPrestamos'    => ['isAdmin', 'isAlmacenista'],
        'material.savePrestamo'     => ['isAdmin', 'isAlmacenista'],

        // ── despachos.* ────────────────────────────────────────
        'despachos.index'               => ['isAdmin', 'isAlmacenista'],
        'despachos.save'                => ['isAdmin', 'isAlmacenista'],
        'despachos.historialMateriales' => ['isAdmin', 'isAlmacenista'],
        'despachos.indexDespachos'      => ['isAdmin', 'isAlmacenista'],
        'despachos.pdfDespacho'         => ['isAdmin', 'isAlmacenista'],

        // ── proveedor.* ────────────────────────────────────────
        'proveedor.index'           => ['isAdmin', 'isAlmacenista'],
        'proveedor.create'          => ['isAdmin', 'isAlmacenista'],
        'proveedor.edit'            => ['isAdmin', 'isAlmacenista'],
        'proveedor.save'            => ['isAdmin', 'isAlmacenista'],
        'proveedor.editStatus'      => ['isAdmin', 'isAlmacenista'],

        // ── pedidos.* ──────────────────────────────────────────
        'pedidos.index'             => ['isAdmin', 'isAlmacenista'],
        'pedidos.create'            => ['isAdmin', 'isAlmacenista'],
        'pedidos.save'              => ['isAdmin', 'isAlmacenista'],
        'pedidos.listPedido'        => ['isAdmin', 'isAlmacenista'],
        'pedidos.hPedidoproveedor'  => ['isAdmin', 'isAlmacenista'],

        // ── otro_si.* ──────────────────────────────────────────
        'otro_si.index'             => ['isAdmin', 'isUser', 'iscartera', 'isDiseno'],
        'otro_si.create'            => ['isAdmin', 'isDiseno'],
        'otro_si.edit'              => ['isAdmin', 'isDiseno'],
        'otro_si.save'              => ['isAdmin', 'isDiseno'],
        'otro_si.otroSiPdf'         => ['isAdmin', 'isUser', 'iscartera', 'isDiseno'],
        'otro_si.sendLinkByEmail'   => ['isAdmin', 'isDiseno'],

        // ── cartera.* ──────────────────────────────────────────
        'cartera.index'             => ['isAdmin', 'iscartera'],
        'cartera.indexEmpy'         => ['isAdmin', 'iscartera'],
        'cartera.pagosProyecto'     => ['isAdmin', 'iscartera'],
        'cartera.save'              => ['isAdmin', 'iscartera'],
        'cartera.reciboPDF'         => ['isAdmin', 'iscartera'],
        'cartera.deletePago'        => ['isAdmin', 'iscartera'],

        // ── cobro.* ──────────────────────────────────────────
        'cobro.index'               => ['isAdmin', 'iscartera'],
        'cobro.deleteCobro'         => ['isAdmin', 'iscartera'],
        'cobro.sendAcuerdoPago'     => ['isAdmin', 'iscartera'],
        'cobro.sendNotificacion'    => ['isAdmin', 'iscartera'],

        // ── solicitud.* ────────────────────────────────────────
        'solicitud.index'                   => ['isAdmin', 'isColab', 'isAnalista', 'isContratista', 'isTecnico', 'isAlmacenista'],
        'solicitud.create'                  => ['isAdmin', 'isColab', 'isContratista', 'isAlmacenista', 'isTecnico'],
        'solicitud.save'                    => ['isAdmin', 'isColab', 'isContratista', 'isAlmacenista', 'isTecnico'],
        'solicitud.listaSolicitud'          => ['isAdmin', 'isColab', 'isAnalista', 'isContratista', 'isTecnico', 'isAlmacenista'],
        'solicitud.createDespachoSolicitud' => ['isAdmin', 'isAlmacenista'],
        'solicitud.createAprobarSolicitud'  => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'solicitud.saveSolicitud'           => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'solicitud.pdfDespacho'             => ['isAdmin', 'isAlmacenista', 'isTecnico'],
        'solicitud.delete'                  => ['isAdmin', 'isColab', 'isContratista', 'isAlmacenista', 'isTecnico'],
        'solicitud.PDFCotizacion'           => ['isAdmin', 'isAnalista', 'isAlmacenista', 'isTecnico'],
        'solicitud.solicitarCotizacion'     => ['isAdmin', 'isAnalista', 'isAlmacenista', 'isTecnico'],

        // ── configuracion.* ────────────────────────────────────
        'configuracion.indexValorArea'      => ['isAdmin'],
        'configuracion.saveValorArea'       => ['isAdmin'],
        'configuracion.listConfigYearModel' => ['isAdmin'],
        'configuracion.indexPorcentajes'    => ['isAdmin'],
        'configuracion.savePorcentajes'     => ['isAdmin'],

        'configuracion.indexValorAreaEnchape'      => ['isAdmin'],
        'configuracion.saveValorAreaEnchape'       => ['isAdmin'],

        //'configuracion.indexAdicionales'    => ['isAdmin'],
        //'configuracion.saveAdicionales'     => ['isAdmin'],

        // ── calendario.* ───────────────────────────────────────
        'calendario.index'              => ['isAdmin'],
        'calendario.resumenAnio'        => ['isAdmin'],
        'calendario.mesDatos'           => ['isAdmin'],
        'calendario.marcarNoLaboral'    => ['isAdmin'],
        'calendario.quitarNoLaboral'    => ['isAdmin'],

        // ── almacen.* ──────────────────────────────────────────
        'almacen.index'             => ['isAdmin'],
        'almacen.create'            => ['isAdmin'],
        'almacen.edit'              => ['isAdmin'],
        'almacen.save'              => ['isAdmin'],

        // ── insumos.* ──────────────────────────────────────────  ← NUEVO
        'insumos.index'             => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'insumos.create'            => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'insumos.edit'              => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'insumos.save'              => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'insumos.editStatus'        => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'insumos.entregaInsumo'     => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'insumos.history'           => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'insumos.historyInsumo'     => ['isAdmin', 'isAnalista', 'isAlmacenista'],

        // ── tracking.* ─────────────────────────────────────────
        'tracking.index'            => ['isAdmin'],
        'tracking.realtime'         => ['isAdmin'],
        'tracking.history'          => ['isAdmin'],
        'tracking.asination'        => ['isAdmin'],

        // ── cotizacion.* ───────────────────────────────────────
        'cotizacion.index'          => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'cotizacion.create'         => ['isAdmin', 'isAnalista', 'isAlmacenista'],

        // ── descargar.db ───────────────────────────────────────
        'descargar.db'              => ['isAdmin'],

    ],

];
