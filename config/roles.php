<?php

/**
 * ============================================================
 *  config/roles.php
 *  Fuente única de verdad para permisos de módulos y acciones.
 *
 *  ROLES DEL SISTEMA
 *  -----------------
 *  isAdmin        → Administrador  (id_rol: 1)
 *  isUser         → Usuario        (id_rol: 2)
 *  isColab        → Arquitecto     (id_rol: 3)
 *  isComer        → Comercial      (id_rol: 4)
 *  iscartera      → Cartera        (id_rol: 5)
 *  isAnalista     → Analista       (id_rol: 6)
 *  isContratista  → Contratista    (id_rol: 7)
 *  isTecnico      → Tecnico        (id_rol: 8)
 *  isAlmacenista  → Almacenista    (id_rol: 9)
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
        'proyecto'      => ['isAdmin', 'isUser', 'isColab', 'isComer', 'isAnalista', 'isContratista'],
        'otro_si'       => ['isAdmin', 'isUser', 'iscartera'],
        'tareas'        => ['isAdmin', 'isUser'],

        // ── Grupo Materiales ───────────────────────────────────
        'material'      => ['isAdmin', 'isUser', 'isAlmacenista'],
        'despachos'     => ['isAdmin', 'isUser', 'isAlmacenista'],
        'proveedor'     => ['isAdmin', 'isUser', 'isAlmacenista'],
        'pedidos'       => ['isAdmin', 'isUser', 'isAlmacenista'],
        'solicitud'     => ['isAdmin', 'isUser', 'isColab', 'isAnalista', 'isContratista', 'isTecnico', 'isAlmacenista'],

        // ── Elementos individuales ─────────────────────────────
        'herramienta'   => ['isAdmin', 'isUser'],
        'tracking'      => ['isAdmin', 'isUser'],
        'calendario'    => ['isAdmin', 'isUser'],
        'almacen'       => ['isAdmin', 'isUser'],
        'insumos'       => ['isAdmin', 'isUser', 'isAnalista'],  // ← NUEVO

        // ── Solo Admin ─────────────────────────────────────────
        'configuracion' => ['isAdmin'],
        'user'          => ['isAdmin'],
        'cotizacion'    => ['isAdmin', 'isUser', 'isAnalista'],
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
        'herramienta.index'         => ['isAdmin', 'isUser'],
        'herramienta.create'        => ['isAdmin', 'isUser'],
        'herramienta.edit'          => ['isAdmin', 'isUser'],
        'herramienta.save'          => ['isAdmin', 'isUser'],
        'herramienta.listPrestamos' => ['isAdmin', 'isUser'],
        'herramienta.savePrestamo'  => ['isAdmin', 'isUser'],

        // ── proyecto.* ─────────────────────────────────────────
        'proyecto.index'                => ['isAdmin', 'isUser', 'isColab', 'isComer', 'isAnalista', 'isContratista'],
        'proyecto.create'               => ['isAdmin', 'isUser'],
        'proyecto.save'                 => ['isAdmin', 'isUser'],
        'proyecto.begin'                => ['isAdmin', 'isUser'],
        'proyecto.editStatus'           => ['isAdmin', 'isUser'],
        'proyecto.saveTarea'            => ['isAdmin', 'isUser'],
        'proyecto.editTarea'            => ['isAdmin', 'isUser', 'isColab', 'isContratista'],
        'proyecto.deleteTarea'          => ['isAdmin', 'isUser'],
        'proyecto.listAvances'          => ['isAdmin', 'isUser', 'isColab', 'isContratista'],
        'proyecto.saveAvance'           => ['isAdmin', 'isUser', 'isColab', 'isContratista'],
        'proyecto.deleteAvance'         => ['isAdmin', 'isUser'],
        'proyecto.listaDespachos'       => ['isAdmin', 'isUser', 'isAlmacenista', 'isContratista', 'isColab'],
        'proyecto.pdfDespachos'         => ['isAdmin', 'isUser', 'isAlmacenista', 'isContratista', 'isColab'],
        'proyecto.pdfDespacho'          => ['isAdmin', 'isUser', 'isAlmacenista', 'isContratista', 'isColab'],
        'proyecto.listComparativo'      => ['isAdmin', 'isUser', 'isAnalista'],
        'proyecto.contratoPdf'          => ['isAdmin', 'isUser', 'isComer'],
        'proyecto.excelDespachoProyecto'=> ['isAdmin', 'isUser'],
        'proyecto.excelDespachosGeneral'=> ['isAdmin', 'isUser'],
        'proyecto.trataDatosPDF'        => ['isAdmin', 'isUser'],
        'proyecto.calendarioProyecto'   => ['isAdmin', 'isUser'],
        'proyecto.saveDiaNoLaborado'    => ['isAdmin', 'isUser'],

        // ── tareas.* ───────────────────────────────────────────
        'tareas.index'              => ['isAdmin', 'isUser'],
        'tareas.save'               => ['isAdmin', 'isUser'],
        'tareas.editTarea'          => ['isAdmin', 'isUser', 'isColab', 'isContratista'],
        'tareas.deleteTarea'        => ['isAdmin', 'isUser'],
        'tareas.moverTarea'         => ['isAdmin', 'isUser', 'isColab'],
        'tareas.finTarea'           => ['isAdmin', 'isUser', 'isColab', 'isContratista'],
        'tareas.listAvances'        => ['isAdmin', 'isUser', 'isColab', 'isContratista'],
        'tareas.saveAvance'         => ['isAdmin', 'isUser', 'isColab', 'isContratista'],
        'tareas.deleteAvance'       => ['isAdmin', 'isUser'],

        // ── material.* ─────────────────────────────────────────
        'material.index'            => ['isAdmin', 'isUser', 'isAlmacenista'],
        'material.create'           => ['isAdmin', 'isUser', 'isAlmacenista'],
        'material.edit'             => ['isAdmin', 'isUser', 'isAlmacenista'],
        'material.save'             => ['isAdmin', 'isUser', 'isAlmacenista'],
        'material.editStatus'       => ['isAdmin', 'isUser', 'isAlmacenista'],
        'material.listPrestamos'    => ['isAdmin', 'isUser', 'isAlmacenista'],
        'material.savePrestamo'     => ['isAdmin', 'isUser', 'isAlmacenista'],

        // ── despachos.* ────────────────────────────────────────
        'despachos.index'               => ['isAdmin', 'isUser', 'isAlmacenista'],
        'despachos.save'                => ['isAdmin', 'isUser', 'isAlmacenista'],
        'despachos.historialMateriales' => ['isAdmin', 'isUser', 'isAlmacenista'],
        'despachos.indexDespachos'      => ['isAdmin', 'isUser', 'isAlmacenista'],
        'despachos.pdfDespacho'         => ['isAdmin', 'isUser', 'isAlmacenista'],

        // ── proveedor.* ────────────────────────────────────────
        'proveedor.index'           => ['isAdmin', 'isUser', 'isAlmacenista'],
        'proveedor.create'          => ['isAdmin', 'isUser', 'isAlmacenista'],
        'proveedor.edit'            => ['isAdmin', 'isUser', 'isAlmacenista'],
        'proveedor.save'            => ['isAdmin', 'isUser', 'isAlmacenista'],
        'proveedor.editStatus'      => ['isAdmin', 'isUser', 'isAlmacenista'],

        // ── pedidos.* ──────────────────────────────────────────
        'pedidos.index'             => ['isAdmin', 'isUser', 'isAlmacenista'],
        'pedidos.create'            => ['isAdmin', 'isUser', 'isAlmacenista'],
        'pedidos.save'              => ['isAdmin', 'isUser', 'isAlmacenista'],
        'pedidos.listPedido'        => ['isAdmin', 'isUser', 'isAlmacenista'],
        'pedidos.hPedidoproveedor'  => ['isAdmin', 'isUser', 'isAlmacenista'],

        // ── otro_si.* ──────────────────────────────────────────
        'otro_si.index'             => ['isAdmin', 'isUser', 'iscartera'],
        'otro_si.create'            => ['isAdmin', 'isUser'],
        'otro_si.edit'              => ['isAdmin', 'isUser'],
        'otro_si.save'              => ['isAdmin', 'isUser'],
        'otro_si.otroSiPdf'         => ['isAdmin', 'isUser', 'iscartera'],
        'otro_si.plantilla_otrosi'  => ['isAdmin', 'isUser'],
        'otro_si.valiPlantilla'     => ['isAdmin', 'isUser'],
        'otro_si.sendLinkByEmail'   => ['isAdmin', 'isUser'],

        // ── cartera.* ──────────────────────────────────────────
        'cartera.index'             => ['isAdmin', 'iscartera'],
        'cartera.pagos'             => ['isAdmin', 'iscartera'],
        'cartera.pagosOtroSi'       => ['isAdmin', 'iscartera'],
        'cartera.save'              => ['isAdmin', 'iscartera'],
        'cartera.reciboPDF'         => ['isAdmin', 'iscartera'],
        'cartera.deletePago'        => ['isAdmin', 'iscartera'],

        // ── solicitud.* ────────────────────────────────────────
        'solicitud.index'                   => ['isAdmin', 'isUser', 'isColab', 'isAnalista', 'isContratista', 'isTecnico', 'isAlmacenista'],
        'solicitud.create'                  => ['isAdmin', 'isUser', 'isColab', 'isContratista', 'isAlmacenista', 'isTecnico'],
        'solicitud.save'                    => ['isAdmin', 'isUser', 'isColab', 'isContratista', 'isAlmacenista', 'isTecnico'],
        'solicitud.listaSolicitud'          => ['isAdmin', 'isUser', 'isColab', 'isAnalista', 'isContratista', 'isTecnico', 'isAlmacenista'],
        'solicitud.createDespachoSolicitud' => ['isAdmin', 'isAlmacenista'],
        'solicitud.createAprobarSolicitud'  => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'solicitud.saveSolicitud'           => ['isAdmin', 'isAnalista', 'isAlmacenista'],
        'solicitud.pdfDespacho'             => ['isAdmin', 'isUser', 'isAlmacenista', 'isTecnico'],
        'solicitud.delete'                  => ['isAdmin', 'isUser', 'isColab', 'isContratista', 'isAlmacenista', 'isTecnico'],
        'solicitud.PDFCotizacion'           => ['isAdmin', 'isAnalista', 'isAlmacenista', 'isTecnico'],
        'solicitud.solicitarCotizacion'     => ['isAdmin', 'isAnalista', 'isAlmacenista', 'isTecnico'],

        // ── configuracion.* ────────────────────────────────────
        'configuracion.indexValorArea'      => ['isAdmin'],
        'configuracion.saveValorArea'       => ['isAdmin'],
        'configuracion.listConfigYearModel' => ['isAdmin'],
        'configuracion.indexPorcentajes'    => ['isAdmin'],
        'configuracion.savePorcentajes'     => ['isAdmin'],
        'configuracion.indexAdicionales'    => ['isAdmin'],
        'configuracion.saveAdicionales'     => ['isAdmin'],

        // ── calendario.* ───────────────────────────────────────
        'calendario.index'              => ['isAdmin', 'isUser'],
        'calendario.resumenAnio'        => ['isAdmin', 'isUser'],
        'calendario.mesDatos'           => ['isAdmin', 'isUser'],
        'calendario.marcarNoLaboral'    => ['isAdmin', 'isUser'],
        'calendario.quitarNoLaboral'    => ['isAdmin', 'isUser'],

        // ── almacen.* ──────────────────────────────────────────
        'almacen.index'             => ['isAdmin', 'isUser'],
        'almacen.create'            => ['isAdmin', 'isUser'],
        'almacen.edit'              => ['isAdmin', 'isUser'],
        'almacen.save'              => ['isAdmin', 'isUser'],

        // ── insumos.* ──────────────────────────────────────────  ← NUEVO
        'insumos.index'             => ['isAdmin', 'isUser', 'isAnalista'],
        'insumos.create'            => ['isAdmin', 'isUser', 'isAnalista'],
        'insumos.edit'              => ['isAdmin', 'isUser', 'isAnalista'],
        'insumos.save'              => ['isAdmin', 'isUser', 'isAnalista'],
        'insumos.editStatus'        => ['isAdmin', 'isUser', 'isAnalista'],
        'insumos.entregaInsumo'     => ['isAdmin', 'isUser', 'isAnalista'],
        'insumos.history'           => ['isAdmin', 'isUser', 'isAnalista'],
        'insumos.historyInsumo'     => ['isAdmin', 'isUser', 'isAnalista'],

        // ── tracking.* ─────────────────────────────────────────
        'tracking.index'            => ['isAdmin', 'isUser'],
        'tracking.realtime'         => ['isAdmin', 'isUser'],
        'tracking.history'          => ['isAdmin', 'isUser'],
        'tracking.asination'        => ['isAdmin', 'isUser'],

        // ── cotizacion.* ───────────────────────────────────────
        'cotizacion.index'          => ['isAdmin', 'isUser', 'isAnalista'],
        'cotizacion.create'         => ['isAdmin', 'isUser', 'isAnalista'],

        // ── descargar.db ───────────────────────────────────────
        'descargar.db'              => ['isAdmin'],

    ],

];
