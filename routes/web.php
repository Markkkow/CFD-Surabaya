<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CfdController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\PerizinanController;

Route::get('/', [CfdController::class, 'home'])->name('home');
Route::get('/login', [CfdController::class, 'showLogin'])->name('login');
Route::post('/login', [CfdController::class, 'login']);
Route::get('/register', [CfdController::class, 'showRegister'])->name('register');
Route::post('/register', [CfdController::class, 'register']);
Route::post('/logout', [CfdController::class, 'logout'])->name('logout');

// Rute untuk Pedagang yang sudah login
Route::middleware('auth:pedagang')->group(function () {
    Route::get('/pilih-lapak', [CfdController::class, 'pilihLapak'])->name('lapak.index');
    Route::post('/daftar-lapak', [CfdController::class, 'daftarLapak'])->name('lapak.daftar');
    Route::get('/lengkapi-produk', [CfdController::class, 'lengkapiProduk'])->name('produk.lengkapi');
    Route::post('/lengkapi-produk', [CfdController::class, 'simpanProduk'])->name('produk.simpan');
    Route::get('/perizinan', [PerizinanController::class, 'create'])->name('perizinan.create');
    Route::post('/perizinan', [PerizinanController::class, 'store'])->name('perizinan.store');
    Route::get('/laporan-penjualan', [PenjualanController::class, 'laporan'])->name('penjualan.laporan');
    Route::post('/laporan-penjualan/{pendaftaran}', [PenjualanController::class, 'simpanLaporan'])->name('penjualan.simpan');
    Route::get('/analytics-penjualan', [PenjualanController::class, 'analytics'])->name('penjualan.analytics');

    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::patch('/inbox/{inbox}/read', [InboxController::class, 'read'])->name('inbox.read');
    Route::patch('/inbox/read-all', [InboxController::class, 'readAll'])->name('inbox.readAll');
});

// Rute Admin/Petugas untuk mengelola event, lapak, dan verifikasi pendaftaran.
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('/events', [AdminController::class, 'storeEvent'])->name('events.store');
    Route::patch('/events/{event}/status', [AdminController::class, 'toggleEventStatus'])->name('events.status');
    Route::delete('/events/{event}', [AdminController::class, 'destroyEvent'])->name('events.destroy');
    Route::post('/lapak', [AdminController::class, 'storeLapakBaris'])->name('lapak.store');
    Route::patch('/lapak/{lapak}/baris', [AdminController::class, 'updateLapakBaris'])->name('lapak.update');
    Route::delete('/lapak/{lapak}', [AdminController::class, 'destroyLapak'])->name('lapak.destroy');

    Route::get('/verifikasi', [AdminController::class, 'verifikasi'])->name('verifikasi');
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
    Route::patch('/verifikasi/{pendaftaran}/terima', [AdminController::class, 'terimaPendaftaran'])->name('verifikasi.terima');
    Route::patch('/verifikasi/{pendaftaran}/tolak', [AdminController::class, 'tolakPendaftaran'])->name('verifikasi.tolak');
});