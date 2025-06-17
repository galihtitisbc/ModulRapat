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
        Route::get('/dashboard', [RapatDashboardController::class, 'index'])->name('rapat.dashboard');

        //route untuk agenda rapat yang digunakan untuk CRUD, upload notulen pada agenda rapat
        Route::prefix('agenda-rapat')->group(function () {
            Route::get('/', [RapatController::class, 'index'])->name('rapat.agenda.index');
            //fetch data untuk datatables
            Route::get('/ajax-peserta-rapat', [RapatController::class, 'ajaxPesertaRapat'])->name('rapat.agenda.ajax.peserta');
            Route::get('/ajax-selected-peserta/', [RapatController::class, 'ajaxSelectedPesertaRapat'])->name('rapat.agenda.ajax.selected.peserta');
            Route::get('/ajax-kepanitiaan/{id}', [KepegawaianController::class, 'ajaxKepanitiaanRapat'])->name('rapat.agenda.ajax.kepanitiaan');
            //-------end fetch data untuk datatable
            Route::get('/{rapatAgenda:slug}/detail', [RapatController::class, 'show'])->name('rapat.agenda.detail');
            // Route::middleware([PimpinanRapatMiddleware::class])->group(function () {
            Route::get('/create', [RapatController::class, 'create'])->name('rapat.agenda.create');
            Route::post('/store', [RapatController::class, 'store'])->name('rapat.agenda.store');
            Route::get('/{rapatAgenda:slug}/edit', [RapatController::class, 'edit'])->name('rapat.agenda.edit');
            Route::get('/ajax-edit/{rapatAgenda:slug}', [RapatController::class, 'ajaxEditRapat'])->name('rapat.agenda.ajax.edit');
            Route::put('/{rapatAgenda:slug}/update', [RapatController::class, 'update'])->name('rapat.agenda.update');
            Route::get('/{rapatAgenda:slug}/batal', [RapatController::class, 'ubahStatusRapat'])->name('rapat.agenda.batal');
            // });
            // Route::middleware([NotulisMiddleware::class])->group(function () {
            Route::get('/{rapatAgenda:slug}/tugas', [TindakLanjutRapatController::class, 'isiPenugasan'])->name('rapat.agenda.tugas');
            Route::get('/{rapatAgenda:slug}/tugaskan/{pegawai}', [TindakLanjutRapatController::class, 'tugaskanPesertaRapat'])->name('rapat.agenda.tugaskan.form');
            Route::post('/{rapatAgenda:slug}/tugaskan/{pegawai}', [TindakLanjutRapatController::class, 'createTugasPesertaRapat'])->name('rapat.agenda.tugaskan.submit');
            //route yang digunakan notulis rapat untuk unggah notulen rapat
            Route::prefix('notulis')->group(function () {
                Route::get('/{rapatAgenda:slug}/unggah-notulen', [NotulisController::class, 'formUnggahNotulen'])->name('rapat.notulis.unggah.form');
                Route::post('/{rapatAgenda:slug}/unggah-notulen', [NotulisController::class, 'storeNotulen'])->name('rapat.notulis.unggah.submit');
            });
            // });
        });
        //---------end route untuk agenda rapat yang digunakan untuk CRUD, upload notulen pada agenda rapat

        // route untuk tindak lanjut rapat yang digunakan untuk menugaskan, melihat, unggah penugasan
        // tindak lanjut rapat
        Route::prefix('tindak-lanjut-rapat')->group(function () {
            Route::get('/', [TindakLanjutRapatController::class, 'index'])->name('rapat.tindak-lanjut.index');
            Route::post('/{rapatAgenda:slug}/tidak-ada-tugas', [TindakLanjutRapatController::class, 'tidakAdaTugas'])->name('rapat.tindak-lanjut.tidak-ada-tugas');
            Route::get('/{rapatAgenda:slug}/detail', [TindakLanjutRapatController::class, 'show'])->name('rapat.tindak-lanjut.detail');
            Route::get('/{rapatTindakLanjut:slug}/detail/tugas', [TindakLanjutRapatController::class, 'detailTugas'])->name('rapat.tindak-lanjut.detail.tugas');
            Route::get('/tugas/{rapatTindakLanjut:slug}/unggah-tugas', [TindakLanjutRapatController::class, 'showUploadTugas'])->name('rapat.tindak-lanjut.tugas.unggah.form');
            Route::post('/tugas/{rapatTindakLanjut:slug}/unggah-tugas', [TindakLanjutRapatController::class, 'uploadTugas'])->name('rapat.tindak-lanjut.tugas.unggah.submit');
            Route::get('/tugas/{rapatTindakLanjut:slug}/ubah-tugas', [TindakLanjutRapatController::class, 'showEditTugas'])->name('rapat.tindak-lanjut.tugas.edit.form');
            Route::put('/tugas/{rapatTindakLanjut:slug}/ubah-tugas', [TindakLanjutRapatController::class, 'editTugas'])->name('rapat.tindak-lanjut.tugas.edit.submit');
            // Route::middleware([PimpinanRapatMiddleware::class])->group(function () {
            Route::post('/{rapatTindakLanjut:slug}/detail/simpan-tugas', [TindakLanjutRapatController::class, 'simpanTugas'])->name('rapat.tindak-lanjut.simpan-tugas');
            // });
        });
        //----- end route untuk tindak lanjut rapat

        Route::prefix('/panitia')->group(function () {
            Route::get('/', [KepegawaianController::class, 'index'])->name('rapat.panitia.index');
            Route::get('{kepanitiaan}/detail', [KepegawaianController::class, 'detail'])->name('rapat.panitia.detail');
            Route::get('/download/{kepanitiaan}', [KepegawaianController::class, 'download'])->name('rapat.panitia.download');
            // Route::middleware([KepegawaianMiddleware::class])->group(function () {
            Route::get('/create', [KepegawaianController::class, 'create'])->name('rapat.panitia.create');
            Route::post('/', [KepegawaianController::class, 'store'])->name('rapat.panitia.store');
            Route::get('/{kepanitiaan}/edit', [KepegawaianController::class, 'edit'])->name('rapat.panitia.edit');
            Route::put('/{kepanitiaan}', [KepegawaianController::class, 'update'])->name('rapat.panitia.update');
            Route::patch('/{kepanitiaan}/change-status', [KepegawaianController::class, 'changeStatus'])->name('rapat.panitia.change-status');
            // });
        });

        Route::prefix('/riwayat-rapat')->group(function () {
            Route::get('/', [RiwayatRapatController::class, 'index'])->name('rapat.riwayat.index');
        });
    });
    //untuk generate pdf laporan hasil rapat
    Route::get('/rapat/riwayat-rapat/{rapatAgenda:slug}/generate-pdf', [ExportPdfController::class, 'generateNotulenRapat'])->name('rapat.riwayat.generate-pdf');
});
//untuk download file notulen rapat
Route::get('/rapat/notulis/{file}/download', [NotulisController::class, 'downloadNotulen'])->name('rapat.notulis.download');
Route::get('/rapat/agenda-rapat/{file}/download', [RapatController::class, 'downloadLampiran'])->name('rapat.agenda.download');

//function untuk menampilkan halaman konfirmasi kesediaan mengikuti rapat
Route::get('/rapat/agenda-rapat/konfirmasi/{token}', [RapatController::class, 'formKonfirmasiKesediaanRapat'])->name('rapat.konfirmasi.form');
Route::post('/rapat/agenda-rapat/konfirmasi/{rapatAgenda:slug}/{pegawai}', [RapatController::class, 'konfirmasiKesediaanRapat'])->name('rapat.konfirmasi.submit');
