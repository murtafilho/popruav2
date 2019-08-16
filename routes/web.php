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
  return redirect('home');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('enderecos', 'EnderecoController');

Route::resource('pontos', 'PontoController');

Route::resource('vistorias', 'VistoriaController');

Route::get('autocomplete/endereco','EnderecoController@autoComplete')->name('autocomplete.endereco');

Route::get('autocomplete/ponto','PontoController@autoComplete')->name('autocomplete.ponto');

Route::get('vistoria_detail/{ponto_id}','VistoriaController@createDetail')->name('vistorias.create.detail');

Route::get('ponto/{ponto_id}','PontoController@index2')->name('ponto');


Route::get('fotos/{vistoria_id}', 'FotoController@index')->name('fotos');
Route::post('fotos/', 'FotoController@store')->name('fotos.store');
Route::delete('fotos/{id}', 'FotoController@destroy')->name('fotos.destroy');


Route::resource('categories', 'CategoryController');

Route::get('saneamento','saneamentoController@executar');

Route::get('migrar/{id}','PontoController@migrar')->name('pontos.migrar');
Route::get('processar_migracao','PontoController@processar_migracao')->name('processar.migracao');

Route::get('geo/{ponto_id}','GeoController@index')->name('geo');

Route::get('setSearch/{ponto_id}','GeoController@setSearch')->name('setsearch');

Route::get('converter', 'GeoController@converter')->name('converter');

Route::get('georreferenciar', 'GeoController@georreferenciar')->name('georreferenciar');

Route::get('markers','Geocontroller@markers')->name('markers');