<?php
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// php artisan serve --host 192.168.0.42:5000
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('dologin', 'ApiController@doLogin');
Route::post('fblogin', 'ApiController@fbLogin');
Route::post('applelogin', 'ApiController@appleLogin');


Route::post('/user/update', 'ApiController@userUpdate');
Route::post('/user/store', 'ApiController@userStore');
Route::post('/user/olvide', 'ApiController@passReset');
Route::post('/user/clave', 'ApiController@cambiarClave');

//users delete
Route::post('/user/delete', 'ApiController@deleteUser');

Route::post('importacion', 'ApiController@enviarImportacion');


Route::post('/refresh_token','ApiController@refreshToken');
Route::get('/ciudades/{ciudad?}/', 'ApiController@getCiudades');
Route::get('/barrios/{ciudad?}/', 'ApiController@getBarrios');

Route::get('/categorias/{categoria?}/', 'ApiController@getCategorias');
Route::get('/cat/seis/', 'ApiController@get6Categorias');
Route::get('/articulos/', 'ApiController@getArticulos');
Route::get('/articulos/precios/', 'ApiController@articulosPrecios');
Route::get('/articulos/imagenes/', 'ApiController@articulosImagenes');
Route::get('/banners', 'ApiController@getBanners');
Route::post('/vender', 'ApiController@vender');

Route::get('/parametros', 'ApiController@getParametros');
Route::get('/empresas', 'ApiController@getEmpresas');
Route::get('/valoraciones/get', 'ApiController@getValoraciones');
Route::post('/valoraciones/store', 'ApiController@setValoraciones');
//empoints
//historial de articulos
Route::get('/compras/historial/articulos/', 'ApiController@comprasHistorial');
Route::get('/articulos/imagen/descripcion/{articulo}', 'ApiController@articuloImagenDescripcion');
Route::get('/delivery/coste', 'ApiController@deliveryCoste');
Route::get('/tags/articulos/{articulo}', 'ApiController@tagsArticulos');

