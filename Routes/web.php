<?php

use Illuminate\Support\Facades\Route;
use Modules\Rapat\Http\Controllers\ExportPdfController;
use Modules\Rapat\Http\Controllers\KepegawaianController;
use Modules\Rapat\Http\Controllers\NotulisController;
use Modules\Rapat\Http\Controllers\RapatController;
use Modules\Rapat\Http\Controllers\RapatDashboardController;
use Modules\Rapat\Http\Controllers\RiwayatRapatController;
use Modules\Rapat\Http\Controllers\TindakLanjutRapatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth', 'permission']], function () {
    Route::prefix('rapat')->group(function () {
        Route::get('/dashboard', [RapatDashboardController::class, 'index']);
        Route::prefix('agenda-rapat')->group(function () {
            Route::get('/', [RapatController::class, 'index']);
            //fetch data untuk datatables
            Route::get('/ajax-peserta-rapat', [RapatController::class, 'ajaxPesertaRapat']);
            Route::get('/ajax-selected-peserta/', [RapatController::class, 'ajaxSelectedPesertaRapat']);
            Route::get('/ajax-kepanitiaan/{id}', [KepegawaianController::class, 'ajaxKepanitiaanRapat']);
            //-------------------------------------
            Route::get('/{rapatAgenda:slug}/detail', [RapatController::class, 'show']);
            Route::get('/{file}/download', [RapatController::class, 'downloadLampiran']);
            Route::middleware(['pimpinanRapat'])->group(function () {
                Route::get('/create', [RapatController::class, 'create']);
                Route::post('/store', [RapatController::class, 'store']);
                Route::get('/{rapatAgenda:slug}/edit', [RapatController::class, 'edit']);
                Route::get('/ajax-edit/{rapatAgenda:slug}', [RapatController::class, 'ajaxEditRapat']);
                Route::put('/{rapatAgenda:slug}/update', [RapatController::class, 'update']);
                Route::get('/{rapatAgenda:slug}/batal', [RapatController::class, 'ubahStatusRapat']);
            });
            Route::middleware(['notulis'])->group(function () {
                Route::get('/{rapatAgenda:slug}/tugas', [TindakLanjutRapatController::class, 'isiPenugasan']);
                Route::get('/{rapatAgenda:slug}/tugaskan/{pegawai}', [TindakLanjutRapatController::class, 'tugaskanPesertaRapat']);
                Route::post('/{rapatAgenda:slug}/tugaskan/{pegawai}', [TindakLanjutRapatController::class, 'createTugasPesertaRapat']);
            });
        });
        Route::prefix('tindak-lanjut-rapat')->group(function () {
            Route::get('/', [TindakLanjutRapatController::class, 'index']);
            Route::get('/{rapatAgenda:slug}/detail', [TindakLanjutRapatController::class, 'show']);
            Route::get('/{rapatTindakLanjut:slug}/detail/tugas', [TindakLanjutRapatController::class, 'detailTugas']);
            Route::get('/tugas/{rapatTindakLanjut:slug}/unggah-tugas', [TindakLanjutRapatController::class, 'showUploadTugas']);
            Route::post('/tugas/{rapatTindakLanjut:slug}/unggah-tugas', [TindakLanjutRapatController::class, 'uploadTugas']);
            Route::get('/tugas/{rapatTindakLanjut:slug}/ubah-tugas', [TindakLanjutRapatController::class, 'showEditTugas']);
            Route::put('/tugas/{rapatTindakLanjut:slug}/ubah-tugas', [TindakLanjutRapatController::class, 'editTugas']);
            Route::middleware(['pimpinanRapat'])->group(function () {
                Route::post('/{rapatTindakLanjut:slug}/detail/simpan-tugas', [TindakLanjutRapatController::class, 'simpanTugas']);
            });

        });
        Route::prefix('agenda-rapat/notulis')->group(function () {
            Route::get('/{file}/download', [NotulisController::class, 'downloadNotulen']);
            Route::middleware(['notulis'])->group(function () {
                Route::get('/{rapatAgenda:slug}/unggah-notulen', [NotulisController::class, 'formUnggahNotulen']);
                Route::post('/{rapatAgenda:slug}/unggah-notulen', [NotulisController::class, 'storeNotulen']);
            });
        });
        Route::prefix('/panitia')->group(function () {
            Route::get('/', [KepegawaianController::class, 'index']);
            Route::get('{kepanitiaan}/detail', [KepegawaianController::class, 'detail']);
            Route::get('/download/{kepanitiaan}', [KepegawaianController::class, 'download']);
            Route::middleware(['kepegawaian'])->group(function () {
                Route::get('/create', [KepegawaianController::class, 'create']);
                Route::post('/', [KepegawaianController::class, 'store']);
                Route::get('/{kepanitiaan}/edit', [KepegawaianController::class, 'edit']);
                Route::put('/{kepanitiaan}', [KepegawaianController::class, 'update']);
                Route::patch('/{kepanitiaan}/change-status', [KepegawaianController::class, 'changeStatus']);
            });
        });
        Route::prefix('/riwayat-rapat')->group(function () {
            Route::get('/', [RiwayatRapatController::class, 'index']);
        });
    });
    //untuk generate pdf laporan hasil rapat
    Route::get('/rapat/riwayat-rapat/{rapatAgenda:slug}/generate-pdf', [ExportPdfController::class, 'generateNotulenRapat']);
});
//function untuk menampilkan halaman konfirmasi kesediaan mengikuti rapat
Route::get('/rapat/agenda-rapat/konfirmasi/{token}', [RapatController::class, 'formKonfirmasiKesediaanRapat']);
Route::post('/rapat/agenda-rapat/konfirmasi/{rapatAgenda:slug}/{pegawai}', [RapatController::class, 'konfirmasiKesediaanRapat']);
