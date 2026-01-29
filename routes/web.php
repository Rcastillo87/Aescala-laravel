<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HerramientaController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\DespachoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\TareasController;
use App\Http\Controllers\ComercialController;
use App\Http\Controllers\OtrosiController;
use App\Http\Controllers\CarteraController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ConfiguracionController;


Route::get('/', function () {
    return view('auth.login');
});

Route::get('firmarContrato/{id}', [ComercialController::class, 'firmarContrato'])->name('firmarContrato');
Route::post('guardarFirma', [ComercialController::class, 'guardarFirma'])->name('guardarFirma');
Route::get('contratoPdf/{id}', [ProyectoController::class, 'contratoPdf'])->name('contratoPdf');

Route::get('firmarOtroSi/{id}', [OtrosiController::class, 'firmarOtroSi'])->name('firmarOtroSi');
Route::post('guardarFirmaOtroSi', [OtrosiController::class, 'guardarFirmaOtroSi'])->name('guardarFirmaOtroSi');
Route::get('otroSiPdfPublic/{id}', [OtrosiController::class, 'otroSiPdfPublic'])->name('otroSiPdfPublic');

// routes/web.php
Route::get('/sw.js', function () {
    return response()->view('sw')
        ->header('Content-Type', 'application/javascript')
        ->header('Cache-Control', 'no-cache, must-revalidate');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/descargar-db', function () {
        $backupDir = storage_path('backups');
        if (!File::exists($backupDir)) {
            return response()->json([
                'error' => 'La carpeta de backups no existe'
            ], 404);
        }
        
        $files = collect(File::files($backupDir))
            ->filter(fn($file) => 
                str_ends_with($file->getFilename(), '.sql') || 
                str_ends_with($file->getFilename(), '.sql.gz')
            )
            ->sortByDesc(fn($file) => $file->getMTime());
        
        if ($files->isEmpty()) {
            return response()->json([
                'error' => 'No hay backups disponibles todavía'
            ], 404);
        }
        
        $latest = $files->first();
        
        Log::info('Backup descargado', [
            'user_id' => auth()->id(),
            'file' => $latest->getFilename(),
            'ip' => request()->ip()
        ]);
        
        return response()->download(
            $latest->getRealPath(), 
            $latest->getFilename()
        );
    })->name('descargar.db');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/index', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::get('/editStatus/{id}', [UserController::class, 'editStatus'])->name('editStatus');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::post('/save', [UserController::class, 'save'])->name('save');
    });

    Route::prefix('comercial')->name('comercial.')->group(function () {
        Route::get('/index', [ComercialController::class, 'index'])->name('index');
        Route::get('/create', [ComercialController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [ComercialController::class, 'edit'])->name('edit');
        Route::post('/save', [ComercialController::class, 'save'])->name('save');
        Route::post('/sendLinkByEmail', [ComercialController::class, 'sendLinkByEmail'])->name('sendLinkByEmail');
    });

    Route::prefix('herramienta')->name('herramienta.')->group(function () {
        Route::get('/index', [HerramientaController::class, 'index'])->name('index');
        Route::get('/create', [HerramientaController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [HerramientaController::class, 'edit'])->name('edit');
        Route::post('/save', [HerramientaController::class, 'save'])->name('save');
        Route::get('/listPrestamos', [HerramientaController::class, 'listPrestamos'])->name('listPrestamos');
        Route::post('/savePrestamo', [HerramientaController::class, 'savePrestamo'])->name('savePrestamo');
    });
    
    Route::prefix('proyecto')->name('proyecto.')->group(function () {
        Route::get('/index', [ProyectoController::class, 'index'])->name('index');
        Route::get('/create', [ProyectoController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [ProyectoController::class, 'edit'])->name('edit');
        Route::post('/save', [ProyectoController::class, 'save'])->name('save');
        Route::post('/begin', [ProyectoController::class, 'begin'])->name('begin');
        Route::post('/editStatus/{id}', [ProyectoController::class, 'editStatus'])->name('editStatus');
        Route::post('/saveTarea', [ProyectoController::class, 'saveTarea'])->name('saveTarea');
        Route::get('/editTarea/{id}', [ProyectoController::class, 'editTarea'])->name('editTarea');
        Route::delete('/deleteTarea', [ProyectoController::class, 'deleteTarea'])->name('deleteTarea');
        Route::get('/listFinanzas', [ProyectoController::class, 'listFinanzas'])->name('listFinanzas');
        Route::post('/savefinanza', [ProyectoController::class, 'savefinanza'])->name('savefinanza');
        Route::get('/listAvances', [ProyectoController::class, 'listAvances'])->name('listAvances');
        Route::post('/saveAvance', [ProyectoController::class, 'saveAvance'])->name('saveAvance');
        Route::delete('/deleteAvance', [ProyectoController::class, 'deleteAvance'])->name('deleteAvance');
        Route::post('/saveCotizacion', [ProyectoController::class, 'saveCotizacion'])->name('saveCotizacion');
        Route::get('/listaCotizacion', [ProyectoController::class, 'listaCotizacion'])->name('listaCotizacion');
        Route::delete('/deleteCotizacion', [ProyectoController::class, 'deleteCotizacion'])->name('deleteCotizacion');
        Route::get('/listaDespachos', [ProyectoController::class, 'listaDespachos'])->name('listaDespachos');
        Route::get('/pdfDespachos', [ProyectoController::class, 'pdfDespachos'])->name('pdfDespachos');
        Route::get('/pdfDespacho', [ProyectoController::class, 'pdfDespacho'])->name('pdfDespacho');
        Route::get('/listComparativo', [ProyectoController::class, 'listComparativo'])->name('listComparativo');
        Route::get('/contratoPdf/{id}', [ProyectoController::class, 'contratoPdf'])->name('contratoPdf');
        Route::get('/excelDespachoProyecto/{id}', [ProyectoController::class, 'excelDespachoProyecto'])->name('excelDespachoProyecto');
        Route::get('/excelDespachosGeneral', [ProyectoController::class, 'excelDespachosGeneral'])->name('excelDespachosGeneral');
        Route::get('/trataDatosPDF/{id}', [ProyectoController::class, 'trataDatosPDF'])->name('trataDatosPDF');
    });

    Route::prefix('tareas')->name('tareas.')->group(function () {
        Route::get('/index', [TareasController::class, 'index'])->name('index');
        Route::post('/save', [TareasController::class, 'save'])->name('save');
        Route::get('/editTarea/{id}', [TareasController::class, 'editTarea'])->name('editTarea');
        Route::delete('/deleteTarea', [TareasController::class, 'deleteTarea'])->name('deleteTarea');
        Route::post('/moverTarea', [TareasController::class, 'moverTarea'])->name('moverTarea');
        Route::post('/finTarea', [TareasController::class, 'finTarea'])->name('finTarea');
        Route::get('/listAvances', [ProyectoController::class, 'listAvances'])->name('listAvances');
        Route::post('/saveAvance', [ProyectoController::class, 'saveAvance'])->name('saveAvance');
        Route::delete('/deleteAvance', [ProyectoController::class, 'deleteAvance'])->name('deleteAvance');
    });

    Route::prefix('material')->name('material.')->group(function () {
        Route::get('/index', [MaterialController::class, 'index'])->name('index');
        Route::get('/editStatus/{id}', [MaterialController::class, 'editStatus'])->name('editStatus');
        Route::get('/create', [MaterialController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [MaterialController::class, 'edit'])->name('edit');
        Route::post('/save', [MaterialController::class, 'save'])->name('save');
        Route::get('/listPrestamos', [MaterialController::class, 'listPrestamos'])->name('listPrestamos');
        Route::post('/savePrestamo', [MaterialController::class, 'savePrestamo'])->name('savePrestamo');
    });

    Route::prefix('despachos')->name('despachos.')->group(function () {
        Route::get('/index', [DespachoController::class, 'index'])->name('index');
        Route::post('/save', [DespachoController::class, 'save'])->name('save');
        Route::get('/selectMaterales', [DespachoController::class, 'selectMaterales'])->name('selectMaterales');
    });

    Route::prefix('proveedor')->name('proveedor.')->group(function () {
        Route::get('/index', [ProveedorController::class, 'index'])->name('index');
        Route::get('/create', [ProveedorController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [ProveedorController::class, 'edit'])->name('edit');
        Route::post('/save', [ProveedorController::class, 'save'])->name('save');
        Route::get('/editStatus/{id}', [ProveedorController::class, 'editStatus'])->name('editStatus');
    });

    Route::prefix('pedidos')->name('pedidos.')->group(function () {
        Route::get('/index', [PedidosController::class, 'index'])->name('index');
        Route::get('/create', [PedidosController::class, 'create'])->name('create');
        Route::post('/save', [PedidosController::class, 'save'])->name('save');
        Route::get('/listPedido', [PedidosController::class, 'listPedido'])->name('listPedido');
        Route::get('/hPedidoproveedor/{id}', [PedidosController::class, 'hPedidoproveedor'])->name('hPedidoproveedor');
    });

    Route::prefix('cotizacion')->name('cotizacion.')->group(function () {
        Route::get('/index', [CotizacionController::class, 'index'])->name('index');
        Route::get('/create', [CotizacionController::class, 'create'])->name('create');
    });

    Route::prefix('otro_si')->name('otro_si.')->group(function () {
        Route::get('/index', [OtrosiController::class, 'index'])->name('index');
        Route::post('/save', [OtrosiController::class, 'save'])->name('save');
        Route::get('/edit/{id}', [OtrosiController::class, 'edit'])->name('edit');
        Route::get('/create', [OtrosiController::class, 'create'])->name('create');
        Route::get('/otroSiPdf/{id}', [OtrosiController::class, 'otroSiPdf'])->name('otroSiPdf');
        Route::get('/plantilla_otrosi', function () {
            $path = storage_path('plantillas/cargue_otro_si.xlsx');
            if (!file_exists($path)) abort(404);
            return response()->download($path);
        })->name('plantilla_otrosi');
        Route::POST('/valiPlantilla', [OtrosiController::class, 'valiPlantilla'])->name('valiPlantilla');
        Route::post('/sendLinkByEmail', [OtrosiController::class, 'sendLinkByEmail'])->name('sendLinkByEmail');
    });

    Route::prefix('cartera')->name('cartera.')->group(function () {
        Route::get('/index', [CarteraController::class, 'index'])->name('index');
        Route::get('/pagos/{id}', [CarteraController::class, 'pagos'])->name('pagos');
        Route::get('/pagosOtroSi/{id}', [CarteraController::class, 'pagosOtroSi'])->name('pagosOtroSi');
        Route::post('/save', [CarteraController::class, 'save'])->name('save');
        Route::get('/reciboPDF/{id}/{tipo}', [CarteraController::class, 'reciboPDF'])->name('reciboPDF');
        Route::post('/deleetePago/{id}', [CarteraController::class, 'deletePago'])->name('deletePago');
    });

    Route::prefix('solicitud')->name('solicitud.')->group(function () {
        Route::get('/index', [SolicitudController::class, 'index'])->name('index');
        Route::get('/create', [SolicitudController::class, 'create'])->name('create');
        Route::post('/save', [SolicitudController::class, 'save'])->name('save');
        Route::get('/listaSolicitud/{id}', [SolicitudController::class, 'listaSolicitud'])->name('listaSolicitud');
        Route::get('/createDespachoSolicitud/{id}', [SolicitudController::class, 'createDespachoSolicitud'])->name('createDespachoSolicitud');
        Route::get('/createAprobarSolicitud/{id}', [SolicitudController::class, 'createAprobarSolicitud'])->name('createAprobarSolicitud');
        Route::post('/saveSolicitud', [SolicitudController::class, 'saveSolicitud'])->name('saveSolicitud');
        Route::get('/pdfDespacho', [ProyectoController::class, 'pdfDespacho'])->name('pdfDespacho');
        Route::delete('/delete/{id}', [SolicitudController::class, 'delete'])->name('delete');
    });


    Route::prefix('configuracion')->name('configuracion.')->group(function () {
        Route::get('/indexValorArea/{año}', [ConfiguracionController::class, 'indexValorArea'])->name('indexValorArea');
        Route::post('/saveValorArea', [ConfiguracionController::class, 'saveValorArea'])->name('saveValorArea');
        Route::get('/listConfigYearModel/{type}/{año}', [ConfiguracionController::class, 'listConfigYearModel'])->name('listConfigYearModel');
        Route::get('/indexPorcentajes/{año}', [ConfiguracionController::class, 'indexPorcentajes'])->name('indexPorcentajes');
        Route::post('/savePorcentajes', [ConfiguracionController::class, 'savePorcentajes'])->name('savePorcentajes');
        Route::get('/indexAdicionales/{año}', [ConfiguracionController::class, 'indexAdicionales'])->name('indexAdicionales');
        Route::post('/saveAdicionales', [ConfiguracionController::class, 'saveAdicionales'])->name('saveAdicionales');
    });

});

require __DIR__.'/auth.php';
