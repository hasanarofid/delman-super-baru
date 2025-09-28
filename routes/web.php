<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
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

// clear view
Route::get('/clear-view', function(){
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    Artisan::call('clear-compiled');
    Artisan::call('config:cache');
    Artisan::call('clear-compiled');
    return 'clear all cache config route';
});
Route::get('/config-cache', function(){
    Artisan::call('config:cache');
    return 'View Cache cleared!';
});
Route::get('/clear-compiled', function(){
    Artisan::call('clear-compiled');
    return 'View Cache cleared!';
});
// call migrate
Route::get('/composer/autoload', function(){
    Artisan::call('shell:composer-dump-autoload');
    return 'Composer autoloader updated!';
});
Route::get('/migrate-fresh', 'MigrationController@migrateFresh');

Route::get('/seed', 'SeedController@seed');
Route::get('/umpan-balik/{generate}', 'UmpanbalikController@umpan');
Route::get('/umpan-balik-view/{generate}', 'UmpanbalikController@umpanview');
Route::post('/kirimumpanbalik', 'UmpanbalikController@saveumpan')->name('kirimumpanbalik');
Route::get('/tanggapan', 'UmpanbalikController@tanggapan')->name('tanggapan');

Route::get('/', function(){
    return redirect('/login');
});


// route panel dashboard admin
Route::get('/', 'AdminController@index')->name('admin.index')->middleware(['auth']);
Route::get('/dashboard', 'AdminController@index')->name('admin.index')->middleware(['auth']);
Route::get('/chart-data', 'AdminController@chartData')->name('admin.chartData')->middleware(['auth']);
Route::get('/chart-data2', 'AdminController@chartData2')->name('admin.chartData2')->middleware(['auth']);
Route::get('/chartDataRaportPendidikan', 'AdminController@chartDataRaportPendidikan')->name('admin.chartDataRaportPendidikan')->middleware(['auth']);
Route::get('/chartTerkonfirmasi', 'AdminController@chartTerkonfirmasi')->name('admin.chartTerkonfirmasi')->middleware(['auth']);
Route::get('/chartpie', 'AdminController@chartpie')->name('admin.chartpie')->middleware(['auth']);

Route::get('/spider-web-data', 'AdminController@getSpiderWebData')->name('admin.spiderWebData')->middleware(['auth']);

// Route::get('/', 'AdminController@index')
//     ->name('admin.index')
//     ->middleware(['auth', 'checkSuperadminOrStakeholder']);

// Route::get('/dashboard', 'AdminController@index')
//     ->name('admin.index')
//     ->middleware(['auth', 'checkSuperadminOrStakeholder']);

// Route::get('/chart-data', 'AdminController@chartData')
//     ->name('admin.chartData')
//     ->middleware(['auth', 'checkSuperadminOrStakeholder']);

// Route::get('/chart-data2', 'AdminController@chartData2')
//     ->name('admin.chartData2')
//     ->middleware(['auth', 'checkSuperadminOrStakeholder']);

// Route::get('/chartDataRaportPendidikan', 'AdminController@chartDataRaportPendidikan')
//     ->name('admin.chartDataRaportPendidikan')
//     ->middleware(['auth', 'checkSuperadminOrStakeholder']);

// Route::get('/chartTerkonfirmasi', 'AdminController@chartTerkonfirmasi')
//     ->name('admin.chartTerkonfirmasi')
//     ->middleware(['auth', 'checkSuperadminOrStakeholder']);

// Route::get('/chartpie', 'AdminController@chartpie')
//     ->name('admin.chartpie')
//     ->middleware(['auth', 'checkSuperadminOrStakeholder']);

// Route::get('/spider-web-data', 'AdminController@getSpiderWebData')
//     ->name('admin.spiderWebData')
//     ->middleware(['auth', 'checkSuperadminOrStakeholder']);

// end panel dashboard admin
// umpan balik


// route penel dashboard for superadmin
Route::prefix('superadmin')->middleware(['auth', 'checkSuperadminOrStakeholder'])->group(function () {
    // route menu admin
    Route::prefix('admin')->group(function () {
        Route::get('/data', 'AdminController@data')->name('admin.data');
        Route::get('/get-admin', 'AdminController@getdata')->name('admin.list');
        Route::get('/add-admin', 'AdminController@add')->name('admin.add');
        Route::post('/store-admin', 'AdminController@store')->name('admin.store');
        Route::get('/edit-admin/{id}', 'AdminController@edit')->name('admin.edit');
        Route::post('/update-admin/{id}', 'AdminController@update')->name('admin.update');
        Route::get('/hapus-admin{id}', 'AdminController@hapus')->name('admin.hapus');
    });

      Route::prefix('jenisprogram')->group(function () {
        Route::get('/', 'JenisprogramController@index')->name('jenisprogram.index');
        Route::get('/get-jenisprogram', 'JenisprogramController@getdata')->name('jenisprogram.getdata');
        Route::get('/add-jenisprogram', 'JenisprogramController@add')->name('jenisprogram.add');
        Route::post('/store-jenisprogram', 'JenisprogramController@store')->name('jenisprogram.store');
        Route::get('/edit-jenisprogram/{id}', 'JenisprogramController@edit')->name('jenisprogram.edit');
        Route::post('/update-jenisprogram/{id}', 'JenisprogramController@update')->name('jenisprogram.update');
        Route::get('/hapus-jenisprogram{id}', 'JenisprogramController@hapus')->name('jenisprogram.hapus');
    });

    Route::prefix('aspekprogram')->group(function () {
        Route::get('/', 'AspekprogramController@index')->name('aspekprogram.index');
        Route::get('/get-aspekprogram', 'AspekprogramController@getdata')->name('aspekprogram.getdata');
        Route::get('/add-aspekprogram', 'AspekprogramController@add')->name('aspekprogram.add');
        Route::post('/store-aspekprogram', 'AspekprogramController@store')->name('aspekprogram.store');
        Route::get('/edit-aspekprogram/{id}', 'AspekprogramController@edit')->name('aspekprogram.edit');
        Route::post('/update-aspekprogram/{id}', 'AspekprogramController@update')->name('aspekprogram.update');
        Route::get('/hapus-aspekprogram{id}', 'AspekprogramController@hapus')->name('aspekprogram.hapus');
    });

    Route::prefix('rencanatugas')->group(function () {
        Route::get('/', 'RencanaTugasController@index')->name('rencanatugas.index');
        Route::get('/get-rencanatugas', 'RencanaTugasController@getdata')->name('rencanatugas.getdata');
        Route::get('/add-rencanatugas', 'RencanaTugasController@add')->name('rencanatugas.add');
        Route::post('/store-rencanatugas', 'RencanaTugasController@store')->name('rencanatugas.store');
        Route::get('/edit-rencanatugas/{id}', 'RencanaTugasController@edit')->name('rencanatugas.edit');
        Route::post('/update-rencanatugas/{id}', 'RencanaTugasController@update')->name('rencanatugas.update');
        Route::get('/kirim-wa/{id}', 'RencanaTugasController@kirimWa')->name('rencanatugas.kirimwa');
        Route::get('/kirim-wa-sekolah/{id}/{id_sekolah}', 'RencanaTugasController@kirimWaSekolah')->name('rencanatugas.kirimWaSekolah');
    });

    Route::prefix('listumpanbalik')->group(function () {
        Route::get('/', 'ListumpanbalikController@index')->name('listumpanbalik.index');
        Route::get('/get-listumpanbalik', 'ListumpanbalikController@getdata')->name('listumpanbalik.getdata');
        Route::get('/add-listumpanbalik', 'ListumpanbalikController@add')->name('listumpanbalik.add');
        Route::post('/store-listumpanbalik', 'ListumpanbalikController@store')->name('listumpanbalik.store');
        Route::get('/edit-listumpanbalik/{id}', 'ListumpanbalikController@edit')->name('listumpanbalik.edit');
        Route::post('/update-listumpanbalik/{id}', 'ListumpanbalikController@update')->name('listumpanbalik.update');
        Route::get('/hapus-listumpanbalik{id}', 'ListumpanbalikController@hapus')->name('listumpanbalik.hapus');
    });

    Route::prefix('dokumentasipendampingan')->group(function () {
        Route::get('/', 'DokumentasipendampinganController@index')->name('dokumentasipendampingan.index');
        Route::get('/get-dokumentasipendampingan', 'DokumentasipendampinganController@getdata')->name('dokumentasipendampingan.getdata');
        Route::get('/export-pdf', 'DokumentasipendampinganController@exportPDF')->name('dokumentasipendampingan.exportPDF');
    });


    Route::prefix('saranperbaikan')->group(function () {
        Route::get('/', 'SaranperbaikanController@index')->name('saranperbaikan.index');
        Route::get('/get-saranperbaikan', 'SaranperbaikanController@getdata')->name('saranperbaikan.getdata');
    });

    Route::prefix('layanandibutuhkan')->group(function () {
        Route::get('/', 'LayanandibutuhkanController@index')->name('layanandibutuhkan.index');
        Route::get('/get-layanandibutuhkan', 'LayanandibutuhkanController@getdata')->name('layanandibutuhkan.getdata');
    });



    Route::prefix('mastertupoksi')->group(function () {
        Route::get('/', 'MastertupoksiController@index')->name('mastertupoksi.index');
        Route::get('/get-mastertupoksi', 'MastertupoksiController@getdata')->name('mastertupoksi.getdata');
        Route::get('/getkegiatan', 'MastertupoksiController@getkegiatan')->name('mastertupoksi.getkegiatan');
        Route::get('/add-mastertupoksi', 'MastertupoksiController@add')->name('mastertupoksi.add');
        Route::post('/store-mastertupoksi', 'MastertupoksiController@store')->name('mastertupoksi.store');
        Route::get('/edit-mastertupoksi/{id}', 'MastertupoksiController@edit')->name('mastertupoksi.edit');
        Route::post('/update-mastertupoksi/{id}', 'MastertupoksiController@update')->name('mastertupoksi.update');
        Route::get('/hapus-mastertupoksi{id}', 'MastertupoksiController@hapus')->name('mastertupoksi.hapus');
    });

    Route::prefix('pembagiantupoksi')->group(function () {
        Route::get('/', 'PembagianTupoksiController@index')->name('pembagiantupoksi.index');
        Route::get('/getadata', 'PembagianTupoksiController@getdata')->name('pembagiantupoksi.getdata');
        Route::get('/getkegiatan', 'PembagianTupoksiController@getkegiatan')->name('pembagiantupoksi.getkegiatan');

        Route::get('/add', 'PembagianTupoksiController@add')->name('pembagiantupoksi.add');
        Route::post('/store', 'PembagianTupoksiController@store')->name('pembagiantupoksi.store');
        Route::get('/edit/{id}', 'PembagianTupoksiController@edit')->name('pembagiantupoksi.edit');
        Route::post('/update/{id}', 'PembagianTupoksiController@update')->name('pembagiantupoksi.update');
        Route::get('/hapus{id}', 'PembagianTupoksiController@hapus')->name('pembagiantupoksi.hapus');
    });

     // route menu pengawas
     Route::prefix('masterpengawas')->group(function () {
        // route panel menu pengawas
        // dd('masterpengawas');
        Route::get('/', 'PegawasMController@index')->name('masterpengawas.index');
        Route::get('/get-pengawas', 'PegawasMController@getdata')->name('masterpengawas.getdata');
        Route::get('/add-pengawas', 'PegawasMController@add')->name('masterpengawas.add');
        Route::get('/edit-pengawas/{id}', 'PegawasMController@edit')->name('masterpengawas.edit');
        Route::post('/update-pengawas', 'PegawasMController@update')->name('masterpengawas.update');
        Route::get('/import-pengawas', 'PegawasMController@import')->name('masterpengawas.import');
        Route::post('/importfile-pengawas', 'PegawasMController@importfile')->name('masterpengawas.importfile');
        Route::post('/store-pengawas', 'PegawasMController@store')->name('masterpengawas.store');
        Route::post('/store_sekolah', 'PegawasMController@store_sekolah')->name('masterpengawas.store_sekolah');
        Route::get('/hapus-pengawas/{id}', 'PegawasMController@hapus')->name('masterpengawas.hapus');
        Route::get('/excelcontoh-pengawas', 'PegawasMController@excelcontoh')->name('masterpengawas.excelcontoh');
        Route::get('/getpangkat', 'PegawasMController@getpangkat')->name('masterpengawas.getpangkat');
        Route::get('/getRuang', 'PegawasMController@getRuang')->name('masterpengawas.getRuang');
        Route::get('/tesWa', 'PegawasMController@tesWa')->name('masterpengawas.tesWa');
        Route::get('/setSekolahBinaan/{id}', 'PegawasMController@setSekolahBinaan')->name('masterpengawas.setSekolahBinaan');




        // end route panel menu pengawas
    });

    // route menu pengawas
    Route::prefix('sekolah')->group(function () {
    // route panel menu sekolah
        Route::get('/', 'SekolahMController@index')->name('sekolah.index');
        Route::get('/get-sekolah', 'SekolahMController@getdata')->name('sekolah.getdata');
        Route::get('/add-sekolah', 'SekolahMController@add')->name('sekolah.add');
        Route::get('/edit-sekolah/{id}', 'SekolahMController@edit')->name('sekolah.edit');
        Route::post('/update-sekolah', 'SekolahMController@update')->name('sekolah.update');
        Route::get('/import-sekolah', 'SekolahMController@import')->name('sekolah.import');
        Route::post('/importfile-sekolah', 'SekolahMController@importfile')->name('sekolah.importfile');
        Route::post('/store-sekolah', 'SekolahMController@store')->name('sekolah.store');
        Route::get('/hapus-sekolah/{id}', 'SekolahMController@hapus')->name('sekolah.hapus');
        Route::get('/excelcontoh-sekolah', 'SekolahMController@excelcontoh')->name('sekolah.excelcontoh');
    // end route panel menu sekolah
    });

    // route panel menu guru
      Route::prefix('guru')->group(function () {
        Route::get('/', 'GuruMController@index')->name('guru.index');
        Route::get('/get-guru', 'GuruMController@getdata')->name('guru.getdata');
        Route::get('/add-guru', 'GuruMController@add')->name('guru.add');
        Route::get('/edit-guru/{id}', 'GuruMController@edit')->name('guru.edit');
        Route::post('/update-guru', 'GuruMController@update')->name('guru.update');
        Route::get('/import-guru', 'GuruMController@import')->name('guru.import');
        Route::post('/importfile-guru', 'GuruMController@importfile')->name('guru.importfile');
        Route::post('/store-guru', 'GuruMController@store')->name('guru.store');
        Route::get('/hapus-guru/{id}', 'GuruMController@hapus')->name('guru.hapus');
        Route::get('/excelcontoh-guru', 'GuruMController@excelcontoh')->name('guru.excelcontoh');
    });
    // end route panel menu guru

    // route panel menu stakeholder
      Route::prefix('stakeholder')->group(function () {
        Route::get('/', 'StakeholderController@index')->name('stakeholder.index');
        Route::get('/get-stakeholder', 'StakeholderController@getdata')->name('stakeholder.getdata');
        Route::get('/add-stakeholder', 'StakeholderController@add')->name('stakeholder.add');
        Route::get('/edit-stakeholder/{id}', 'StakeholderController@edit')->name('stakeholder.edit');
        Route::post('/update-stakeholder/{id}', 'StakeholderController@update')->name('stakeholder.update');
        Route::get('/import-stakeholder', 'StakeholderController@import')->name('stakeholder.import');
        Route::post('/importfile-stakeholder', 'StakeholderController@importfile')->name('stakeholder.importfile');
        Route::post('/store-stakeholder', 'StakeholderController@store')->name('stakeholder.store');
        Route::get('/hapus-stakeholder/{id}', 'StakeholderController@hapus')->name('stakeholder.hapus');
        Route::get('/excelcontoh-stakeholder', 'StakeholderController@excelcontoh')->name('stakeholder.excelcontoh');
    });
    // end route panel menu stakeholder

    // route wablasthistory

    // end wablasthistory
    Route::prefix('wablasthistory')->group(function () {
        Route::get('/', 'WablasthistoryController@index')->name('wablasthistory.index');
        Route::get('/get-history', 'WablasthistoryController@getdata')->name('wablasthistory.getdata');
    });

    // end route menu admin
});
// end route penel dashboard for superadmin

// route penel dashboard for admin
// Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
//     // route menu pengawas
//     Route::prefix('masterpengawas')->group(function () {
//         // route panel menu pengawas
//         Route::get('/', 'PegawasMController@index')->name('masterpengawas.index');
//         Route::get('/get-pengawas', 'PegawasMController@getdata')->name('masterpengawas.getdata');
//         Route::get('/add-pengawas', 'PegawasMController@add')->name('masterpengawas.add');
//         Route::get('/edit-pengawas/{id}', 'PegawasMController@edit')->name('masterpengawas.edit');
//         Route::post('/update-pengawas', 'PegawasMController@update')->name('masterpengawas.update');
//         Route::get('/import-pengawas', 'PegawasMController@import')->name('masterpengawas.import');
//         Route::post('/importfile-pengawas', 'PegawasMController@importfile')->name('masterpengawas.importfile');
//         Route::post('/store-pengawas', 'PegawasMController@store')->name('masterpengawas.store');
//         Route::post('/store_sekolah', 'PegawasMController@store_sekolah')->name('masterpengawas.store_sekolah');
//         Route::get('/hapus-pengawas/{id}', 'PegawasMController@hapus')->name('masterpengawas.hapus');
//         Route::get('/excelcontoh-pengawas', 'PegawasMController@excelcontoh')->name('masterpengawas.excelcontoh');
//         Route::get('/getpangkat', 'PegawasMController@getpangkat')->name('masterpengawas.getpangkat');
//         Route::get('/getRuang', 'PegawasMController@getRuang')->name('masterpengawas.getRuang');
//         Route::get('/tesWa', 'PegawasMController@tesWa')->name('masterpengawas.tesWa');
//         Route::get('/setSekolahBinaan/{id}', 'PegawasMController@setSekolahBinaan')->name('masterpengawas.setSekolahBinaan');




//         // end route panel menu pengawas
//     });

//     // route menu pengawas
//     Route::prefix('sekolah')->group(function () {
//     // route panel menu sekolah
//         Route::get('/', 'SekolahMController@index')->name('sekolah.index');
//         Route::get('/get-sekolah', 'SekolahMController@getdata')->name('sekolah.getdata');
//         Route::get('/add-sekolah', 'SekolahMController@add')->name('sekolah.add');
//         Route::get('/edit-sekolah/{id}', 'SekolahMController@edit')->name('sekolah.edit');
//         Route::post('/update-sekolah', 'SekolahMController@update')->name('sekolah.update');
//         Route::get('/import-sekolah', 'SekolahMController@import')->name('sekolah.import');
//         Route::post('/importfile-sekolah', 'SekolahMController@importfile')->name('sekolah.importfile');
//         Route::post('/store-sekolah', 'SekolahMController@store')->name('sekolah.store');
//         Route::get('/hapus-sekolah/{id}', 'SekolahMController@hapus')->name('sekolah.hapus');
//         Route::get('/excelcontoh-sekolah', 'SekolahMController@excelcontoh')->name('sekolah.excelcontoh');
//     // end route panel menu sekolah
//     });

//     // route panel menu guru
//       Route::prefix('guru')->group(function () {
//         Route::get('/', 'GuruMController@index')->name('guru.index');
//         Route::get('/get-guru', 'GuruMController@getdata')->name('guru.getdata');
//         Route::get('/add-guru', 'GuruMController@add')->name('guru.add');
//         Route::get('/edit-guru/{id}', 'GuruMController@edit')->name('guru.edit');
//         Route::post('/update-guru', 'GuruMController@update')->name('guru.update');
//         Route::get('/import-guru', 'GuruMController@import')->name('guru.import');
//         Route::post('/importfile-guru', 'GuruMController@importfile')->name('guru.importfile');
//         Route::post('/store-guru', 'GuruMController@store')->name('guru.store');
//         Route::get('/hapus-guru/{id}', 'GuruMController@hapus')->name('guru.hapus');
//         Route::get('/excelcontoh-guru', 'GuruMController@excelcontoh')->name('guru.excelcontoh');
//     });
//     // end route panel menu guru

//     // route panel menu stakeholder
//       Route::prefix('stakeholder')->group(function () {
//         Route::get('/', 'StakeholderController@index')->name('stakeholder.index');
//         Route::get('/get-stakeholder', 'StakeholderController@getdata')->name('stakeholder.getdata');
//         Route::get('/add-stakeholder', 'StakeholderController@add')->name('stakeholder.add');
//         Route::get('/edit-stakeholder/{id}', 'StakeholderController@edit')->name('stakeholder.edit');
//         Route::post('/update-stakeholder/{id}', 'StakeholderController@update')->name('stakeholder.update');
//         Route::get('/import-stakeholder', 'StakeholderController@import')->name('stakeholder.import');
//         Route::post('/importfile-stakeholder', 'StakeholderController@importfile')->name('stakeholder.importfile');
//         Route::post('/store-stakeholder', 'StakeholderController@store')->name('stakeholder.store');
//         Route::get('/hapus-stakeholder/{id}', 'StakeholderController@hapus')->name('stakeholder.hapus');
//         Route::get('/excelcontoh-stakeholder', 'StakeholderController@excelcontoh')->name('stakeholder.excelcontoh');
//     });
//     // end route panel menu stakeholder
// });
// end

// route halaman pengawas
Route::middleware(['web', 'pengawas'])->group(function () {


    Route::get('/pengawas', 'PengawasController@index')->name('pengawas.index');
    Route::get('/editprofile', 'PengawasController@editprofile')->name('pengawas.editprofile');
    Route::post('/updateprofile', 'PengawasController@updateprofile')->name('pengawas.updateprofile');
    Route::post('/ubahpassword', 'PengawasController@ubahpassword')->name('pengawas.ubahpassword');
    Route::get('/chart-data-pengawas', 'PengawasController@chartData')->name('pengawas.chartData');
    Route::get('/chart-data2-pengawas', 'PengawasController@chartData2')->name('pengawas.chartData2');
    Route::get('/dashboard', 'PengawasController@dashboard')->name('pengawas.dashboard');
    Route::get('/spider-web-data-pengawas', 'PengawasController@getSpiderWebDataPengawas')->name('pengawas.spiderWebData');
    Route::get('/chartTerkonfirmasi-pengawas', 'PengawasController@chartTerkonfirmasiPengawas')->name('pengawas.chartTerkonfirmasi');
    Route::get('/chartDataRaportPendidikan-pengawas', 'PengawasController@chartDataRaportPendidikan')->name('pengawas.chartDataRaportPendidikan');
    Route::get('/chartpie-pengawas', 'PengawasController@chartpie')->name('pengawas.chartpie');


    Route::prefix('pengawas/listumpanbalik')->group(function () { // tambahkan prefix pengawas
        Route::get('/', 'ListumpanbalikController@indexpengawas')->name('pengawas.listumpanbalik.index');
        Route::get('/get-listumpanbalik', 'ListumpanbalikController@getdatapengawas')->name('pengawas.listumpanbalik.getdata');
    });

    Route::prefix('pengawas/dokumentasipendampingan')->group(function () {
        Route::get('/', 'DokumentasipendampinganController@indexpengawas')->name('pengawas.dokumentasipendampingan.index');
        Route::get('/get-dokumentasipendampingan', 'DokumentasipendampinganController@getdatapengawas')->name('pengawas.dokumentasipendampingan.getdata');
        Route::get('/export-pdf-pengawas', 'DokumentasipendampinganController@exportPDFPengawas')->name('pengawas.dokumentasipendampingan.exportPDF');
    });


    Route::prefix('pengawas/layanandibutuhkan')->group(function () {
        Route::get('/', 'LayanandibutuhkanController@indexpengawas')->name('pengawas.layanandibutuhkan.index');
        Route::get('/get-layanandibutuhkan', 'LayanandibutuhkanController@getdatapengawas')->name('pengawas.layanandibutuhkan.getdata');
    });


    // route panel menu pengawas rencanakerja
    Route::prefix('rencanakerja')->group(function () {
        Route::get('/', 'RencanaKerjaController@index')->name('pengawas.rencanakerja');
        Route::get('/chart-rencanakerja', 'RencanaKerjaController@getchart')->name('pengawas.rencanakerja.chart');
    });
    // end route panel menu pengawas rencanakerja

    // route panel menu pengawas activitas
    Route::prefix('activitas')->group(function () {
        Route::get('/', 'ActivitasController@index')->name('pengawas.activitas');
        Route::get('/chart-activitas', 'ActivitasController@getchart')->name('pengawas.activitas.chart');
    });
    // end route panel menu pengawas activitas

    // route panel menu pengawas umpanbalik
    Route::prefix('masterumpanbalik')->group(function () {
        Route::get('/', 'MasterumpanbalikController@index')->name('pengawas.masterumpanbalik');
        Route::get('/chart-masterumpanbalik', 'MasterumpanbalikController@getchart')->name('pengawas.masterumpanbalik.chart');
    });
    // end route panel menu pengawas umpanbalik




    // route panel menu pengawas datapengawas
    Route::prefix('datapengawas')->group(function () {
        Route::get('/', 'DatapengawasController@index')->name('pengawas.datapengawas');
    });
    // end route panel menu pengawas datapengawas

    // route panel menu pengawas perencanaan
    Route::prefix('perencanaan')->group(function () {
        Route::get('/', 'PerencanaanController@index')->name('pengawas.perencanaan');
        Route::post('/save-perencanaan', 'PerencanaanController@save')->name('pengawas.perencanaan.save-perencanaan');
        Route::post('/update-perencanaan', 'PerencanaanController@update')->name('pengawas.perencanaan.update');
        Route::get('/get-perencanaan', 'PerencanaanController@getdata')->name('pengawas.perencanaan.getdata');
        Route::get('/edit-perencanaan/{id}', 'PerencanaanController@edit')->name('pengawas.perencanaan.edit');
        Route::delete('/hapus-perencanaan/{id}', 'PerencanaanController@hapus')->name('pengawas.perencanaan.hapus');
    });
    // end route panel menu pengawas perencanaan

    // route panel menu pengawas pelaporan
    Route::prefix('pelaporan')->group(function () {
        Route::get('/', 'PelaporanController@index')->name('pengawas.pelaporan');
        Route::post('/save-pelaporan', 'PelaporanController@save')->name('pengawas.pelaporan.save-pelaporan');
        Route::post('/simpansubkategory', 'PelaporanController@simpansubkategory')->name('pengawas.perencanaan.simpansubkategory');
        Route::post('/update-pelaporan', 'PelaporanController@update')->name('pengawas.pelaporan.update');
        Route::get('/get-pelaporan', 'PelaporanController@getdata')->name('pengawas.pelaporan.getdata');
        Route::get('/edit-pelaporan/{id}', 'PelaporanController@edit')->name('pengawas.pelaporan.edit');
        Route::get('/hapus-pelaporan/{id}', 'PelaporanController@hapus')->name('pengawas.pelaporan.hapus');
        Route::get('/get-subcategories', 'PelaporanController@getSubcategories')->name('pengawas.pelaporan.getSubKategori');
        Route::get('/get-programKerja', 'PelaporanController@getProgramKerja')->name('pengawas.pelaporan.getProgramKerja');
        Route::get('/get-getProgramKerjaSasaran', 'PelaporanController@getProgramKerjaSasaran')->name('pengawas.pelaporan.getProgramKerjaSasaran');

    });
    // end route panel menu pengawas pelaporan

    // route panel menu pengawas pelaporan
    // Route::prefix('pelaporan')->group(function () {
    //     Route::get('/', 'PelaporanController@index')->name('pengawas.pelaporan');
    // });
    // end route panel menu pengawas pelaporan

     // route panel menu pengawas sekolahbinaan
     Route::prefix('sekolahbinaan')->group(function () {
        Route::get('/', 'SekolahbinaanController@index')->name('pengawas.sekolahbinaan');
    });
    // end route panel menu pengawas sekolahbinaan

    // route panel menu pengawas umpanbalik
    Route::prefix('umpanbalik')->group(function () {
        Route::get('/', 'UmpanbalikController@index')->name('pengawas.umpanbalik');
        Route::get('/get-umpanbalik', 'UmpanbalikController@getdata')->name('pengawas.umpanbalik.getdata');
        Route::get('/show-umpanbalik/{id}', 'UmpanbalikController@show')->name('pengawas.umpanbalik.show');
    });
    // end route panel menu pengawas umpanbalik


    // login logout pengawas
    Route::get('/pengawas/login', 'Auth\LoginController@showPengawasLoginForm')->name('pengawas.login');
    Route::post('/pengawas/login', 'Auth\LoginController@superPengawasLogin')->name('superPengawasLogin');
    Route::post('/pengawas/logout', 'Auth\LoginController@logoutpengawas')->name('pengawas.logout');
});

// Route::prefix('pengawas')->middleware(['auth', 'pengawas'])->group(function () {

// });
// end route halaman pengawas

Auth::routes();
Route::get('laporan/{filename}', function ($filename) {
    $path = storage_path('app/public/laporan/' . $filename);

    if (!File::exists($path)) {
        Log::error('Image file not found: ' . $path);
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->name('laporan');

Route::get('fotopengawas/{filename}', function ($filename) {
    $path = storage_path('app/public/pengawas/' . $filename);

    if (!File::exists($path)) {
        Log::error('Image file not found: ' . $path);
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->name('fotopengawas');

Route::get('favicon/{filename?}', function ($filename) {
    $path = storage_path('app/public/favicon/' . $filename);
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->name('favicon');


Route::get('umpanbalikfoto/{filename}', function ($filename) {
    $path = storage_path('app/public/umpanbalik/' . $filename);

    if (!File::exists($path)) {
        Log::error('Image file not found: ' . $path);
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->name('umpanbalikfoto');
