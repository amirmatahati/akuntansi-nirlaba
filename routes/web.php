<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/* ACCOUNTING */
Route::get('/list-ref', 'finance\PerkiraanController@index')->name('perkiraan.list');
Route::post('/update-ref/{id}', 'finance\PerkiraanController@update')->name('perkiraan.update');
Route::post('/insert-ref', 'finance\PerkiraanController@store')->name('perkiraan.insert');
Route::get('/list-asset', 'finance\PerkiraanController@AssetList')->name('perkiraan.asset');
Route::get('/search-asset', 'finance\PerkiraanController@SearchAsset')->name('search.asset');
Route::get('/search-akun-asset', 'finance\PerkiraanController@GetAkunAll')->name('search.asset-akun');
Route::post('/search-akun-byid', 'finance\PerkiraanController@AkunByiD')->name('search.asset-byid');

/* JURNAL */
Route::get('/jurnal-add', 'jurnal\JurnalController@create')->name('add.jurnal');
Route::post('/insert-jurnal', 'jurnal\JurnalController@store')->name('insert.jurnal');
Route::get('/list-jurnal', 'jurnal\JurnalController@index')->name('list.jurnal');
Route::get('/edit-jurnal/{id}', 'jurnal\JurnalController@edit')->name('edit.jurnal');
Route::post('/update-jurnal/{id}', 'jurnal\JurnalController@update')->name('update.jurnal');
Route::get('/search-akun', 'jurnal\JurnalController@GetAkunAll')->name('search.asset-akun1');

/* BUKU BESAR */
Route::get('/buku-besar-priode', 'jurnal\BukuBesarController@SearchBukuBesar')->name('buku.besarsearch');
Route::get('pdf-buku-besar','pdf\FinanceController@BukuBesar')->name('print.buku_besar');

/* NERACA SALDO */
Route::get('/neraca-saldo-priode', 'jurnal\NeracaSaldoController@SearchNeracaSaldo')->name('neraca.saldosearch');
Route::get('/aktivitas-transaksi', 'jurnal\NeracaSaldoController@AktivitasNew')->name('aktivitas_transaksi');

/* LABA RUGI */
Route::get('/laba-rugi-priode', 'jurnal\LabaRugiController@LabaRugiSearch')->name('search.labarugi');
Route::get('/serach-perubahan-modal', 'jurnal\PerubahanModalConroller@perubahanModalSearch')->name('search.perubahanmodal');
Route::get('/asset-view-date-in', 'jurnal\NeracaPenutupController@SearchposisiAsset')->name('search.assetposisi');

/* AJP */
Route::get('/jurnal-penyesuaian', 'jurnal\AJPController@AJP')->name('list.ajp');
Route::get('/add-ajp', 'jurnal\AJPController@createAJP')->name('add.ajp');
Route::post('/insert-ajp', 'jurnal\AJPController@storeAJP')->name('insert.ajp');

/* KAS MASUK */
Route::get('/kas-masuk', 'finance\ArusKasController@create')->name('no_kas_masuk');
Route::post('/insert-kas-masuk', 'finance\ArusKasController@store')->name('insert_kas_masuk');
Route::get('/get-saldo-by-id/{id}', 'finance\ArusKasController@GetSaldo')->name('saldobyid');
