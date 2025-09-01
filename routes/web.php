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

Route::get('/', function () {
    return view('auth.login');
});

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
        $backupDir = '/home/user/backups/databases/aescala';

        if (!File::exists($backupDir)) {
            abort(404, 'No hay copias disponibles.');
        }

        $files = collect(File::files($backupDir))
            ->sortByDesc(fn($file) => $file->getMTime());

        $latest = $files->first();

        if (!$latest) {
            abort(404, 'No se encontró ninguna copia de seguridad.');
        }

        return response()->download($latest->getRealPath(), $latest->getFilename());
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
    });

    Route::prefix('cotizacion')->name('cotizacion.')->group(function () {
        Route::get('/index', [CotizacionController::class, 'index'])->name('index');
        Route::get('/create', [CotizacionController::class, 'create'])->name('create');
    });

});

require __DIR__.'/auth.php';