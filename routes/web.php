<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HerramientaController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\MaterialController;

Route::get('/', function () {
    return view('auth.login');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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
        Route::post('/editStatus/{id}', [ProyectoController::class, 'editStatus'])->name('editStatus');
        Route::post('/saveTarea', [ProyectoController::class, 'saveTarea'])->name('saveTarea');
        Route::get('/editTarea/{id}', [ProyectoController::class, 'editTarea'])->name('editTarea');
        Route::get('/listFinanzas', [ProyectoController::class, 'listFinanzas'])->name('listFinanzas');
        Route::post('/savefinanza', [ProyectoController::class, 'savefinanza'])->name('savefinanza');
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

});

require __DIR__.'/auth.php';