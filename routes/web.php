
<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatatanController;
use App\Http\Controllers\HaloController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BerandaController::class, 'index']);

Route::get('/halo', [HaloController::class, 'index']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegistration'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/catatan', [CatatanController::class, 'index'])->name('catatan.index');
    Route::get('/catatan/tambah', [CatatanController::class, 'create'])->name('catatan.create');
    Route::post('/catatan', [CatatanController::class, 'store'])->name('catatan.store');
    Route::get('/catatan/{catatan}', [CatatanController::class, 'show'])->name('catatan.show');
    Route::get('/catatan/{catatan}/edit', [CatatanController::class, 'edit'])->name('catatan.edit');
    Route::put('/catatan/{catatan}', [CatatanController::class, 'update'])->name('catatan.update');
    Route::delete('/catatan/{catatan}', [CatatanController::class, 'destroy'])->name('catatan.destroy');
});

Route::get('/about', function () {
    return view('about');
});
