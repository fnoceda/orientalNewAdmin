<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



      
    Route::post('/resetear/contraseña', 'UsersController@passReset');
    Route::get('/', function () { return view('welcome'); });
    // Auth::routes();
    Auth::routes(["register" => false]);

    Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::put('/profile', 'ProfileController@update')->name('profile.update');
    Route::get('/about', function () { return view('about'); })->name('about');

    Route::get('/privacidad', function () { return view('privacidad'); })->name('privacidad');
    Route::get('/contacto', function () { return view('contacto'); })->name('contacto');
    Route::post('/contacto', 'ContactoController@send')->name('send');
    //deslogear
    Route::get('/resstar', 'HomeController@reestar');

    Route::get('/corrrre', 'HomeController@correo');

    Route::get('/drop/user', 'HomeController@deleteUser');
    //*baja de usuario
    Route::get('/user/baja', 'UsersBajaController@index');
    Route::post('/user/autenticar', 'UsersBajaController@autenticarUsuario')->name('autenticar');  
    Route::post('/user/baja/user', 'UsersBajaController@bajaUsuario')->name('baja');  




Route::group(['middleware' => ['auth','perfil']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    // Route::get('/user/delete', 'ApiController@deleteUser');

    // Route::get('/set_language/{lang}','Controller@set_language')->name('set_language');
    // //rutas de abms
    Route::get('/abms/{base}', 'AbmController@index')->name('abms');
    Route::get('/abms/{base}/data', 'AbmController@data')->name('abms.data');
    Route::get('/abms/{base}/create', 'AbmController@create');
    Route::post('/abms/{base}', 'AbmController@store');
    Route::get('/abms/{base}/show/{id}', 'AbmController@show')->name('abms.show');
    Route::get('/abms/{base}/{id}', 'AbmController@edit');
    Route::patch('/abms/{base}/{id}', 'AbmController@update');
    Route::delete('/abms/{base}/{id}', 'AbmController@destroy');
    // //abm empresa manual
    Route::post('/abms/empresa/guardar', 'AbmController@guardar');
    Route::post('/abms/empresa/update', 'AbmController@actualizar_empresa');
    //abm manual de colores
    Route::get('/abms/colores/colors/index', 'AbmController@colorindex');
    Route::get('/abms/colores/colors/create', 'AbmController@colorcreate');
    Route::post('/abms/colores/colors/guardar', 'AbmController@colorguardar');
    Route::get('/abms/colores/colors/edit/{id}', 'AbmController@coloredit');
    Route::post('/abms/colores/colors/update', 'AbmController@colorupdate');
    Route::get('/abms/colores/colors/delete/{id}', 'AbmController@deletecolor');

    // //parametros
    Route::get('/admin/parametros/', 'parametrosController@index');
    Route::get('/admin/parametros/create', 'parametrosController@create');
    Route::get('/admin/parametros/edit/{clave}', 'parametrosController@edit');
    Route::post('/admin/parametros/update/', 'parametrosController@update');
    //Categorias
    Route::resource('/admin/categorias', 'CategoriasController');
    Route::post('/admin/categorias/agregar', 'CategoriasController@agregarCategorias')->name('categorias.agregar');
    // //Categoria de  Articulos
    Route::get('/categorias/articulos', 'ArticulosController@index');
    Route::get('/categorias/articulos/guardar', 'ArticulosController@guardar_categoria');
    Route::get('/categoria/articulos/eliminar/{id}', 'ArticulosController@delete');
    //ordenar categorias
    Route::get('/categorias/ordenar', 'ArticulosController@indexOrdenar');
    Route::post('/categorias/buscar', 'ArticulosController@colador');
    Route::post('/categoria/categorias/ordenar/categorias', 'ArticulosController@ordenarCategorias');
    //rutas provisorias de apuro
    Route::post('/categorias/articulos/save', 'ArticulosController@guardar_categoria_momentanea');
    Route::get('/categorias/articulos/edit', 'ArticulosController@');
    Route::get('/categorias/articulos/update', 'ArticulosController@');
    //articulos
    Route::get('/articulos/listar', 'ArticulosController@articulos');
    Route::get('/articulos/save/Guardar/', 'ArticulosController@guardarArticulo');
    Route::post('/imagenes/imagenes/guardar', 'ArticulosController@guardarimagen');
    Route::get('/imagenes/imagenes/eliminar_imagen', 'ArticulosController@eliminar_imagen');
    Route::get('/imagenes/imagenes/eliminar', 'ArticulosController@eliminar');
    Route::post('/imagenes/imagenes/ordenar', 'ArticulosController@ordenar');
    Route::post('/colores/colors/guardar', 'ArticulosController@ordenarcolores');
    //dtatable
    Route::get('/articulos/get/data/', 'ArticulosController@datatable');
    //articulos filtrar
    Route::post('/articulos/listar/filtro', 'ArticulosController@filtro_categorias');
    // //iconos y etiquetas
    Route::get('/admin/images/{base}', 'iconosEtiqetasController@index');
    Route::get('/admin/images/create/{tabla}', 'iconosEtiqetasController@create');
    Route::get('/admin/images/edit/{tabla}/{id}', 'iconosEtiqetasController@edit');
    Route::get('/admin/images/delete/{tabla}/{id}', 'iconosEtiqetasController@delete');
    Route::post('/admin/images/guardar/{tabla}', 'iconosEtiqetasController@guardar');
    Route::post('/admin/imagenes/update/', 'iconosEtiqetasController@updated');
    //banners
    Route::get('/banners/images/', 'bannersController@index');
    Route::get('/banners/images/create', 'bannersController@create');
    Route::post('/banners/images/guardar/', 'bannersController@guardar');
    Route::get('/banners/images/edit/{id}', 'bannersController@edit');
    Route::get('/banners/images/delete/{id}', 'bannersController@delete');
    Route::post('/banners/imagenes/update/', 'bannersController@updated');


    //banners
    Route::get('ventas/', 'VentasController@index');
    Route::get('ventas/edit/{id}', 'VentasController@edit')->name('ventas.edit');
    Route::get('ventas/delete/{id}', 'VentasController@destroy');
    Route::get('ventas/cambiar/estado', 'VentasController@cambiar_estado');
    Route::get('ventas/data/datatable', 'VentasController@data')->name('ventas.data');
    //combos
    Route::get('combos/', 'combosController@index');
    Route::get('combos/save', 'combosController@combosDetalles');
    Route::get('combos/delete/{id}', 'combosController@delete');
    Route::get('combos/edit/{id}', 'combosController@edit');
    Route::get('combos/update', 'combosController@update');
    //inventario justes
    Route::get('ajustes/', 'ajustesController@index');
    Route::get('ajustes/buscar', 'ajustesController@colador');
    Route::post('ajustes/guardar/cambios', 'ajustesController@guardar');

    Route::get('/impresion', 'impresionController@imprimir');

    //usuarios
    Route::get('/users/list', 'UsersController@index');
    Route::get('/users/clientes/list', 'UsersController@usersList')->name('users.clientes.list');
    Route::post('/usuarios/agregar', 'UsersController@agregar');
    Route::post('/usuarios/editar', 'UsersController@editar');
    //contraseña individual
    Route::get('/usuario', 'UsersController@cambiarContraseña');
    Route::post('/usuario/pass/', 'UsersController@guardarContraseña');
    // //reset contraseñas

    // //actualizacion abril 2024
    // //articulo_imagen
    Route::get('/articulos/descripcion/{id?}', 'ArticulosController@articuloDescripcion')->name('articulo.descripcion');
    Route::post('/articulos/descripcion/delete', 'ArticulosController@DeleteDescripcion')->name('delete.descripcion');
    Route::post('/articulo/imagen/descripcion/guardar', 'ArticulosController@guardarImagenDescripcion')->name('safe.description');
    // //abms de delivery
    Route::get('delivery/', 'DeliveryController@index')->name('delivery');
    Route::get('delivery/edit/{id}', 'DeliveryController@edit')->name('delivery.edit');
    Route::get('delivery/data/datatable', 'DeliveryController@datatable')->name('delivery.datatable');
    Route::post('delivery/save', 'DeliveryController@store')->name('delivery.store');
    Route::post('delivery/update', 'DeliveryController@update')->name('delivery.update');
    Route::post('delivery/delete', 'DeliveryController@destroy')->name('delivery.delete');
    //plazo entrega
    Route::get('plazo/entrega/', 'PlazoEntregaController@index')->name('plazo.entrega');
    Route::get('plazo/entrega/edit/{id}', 'PlazoEntregaController@edit')->name('plazo.entrega.edit');
    Route::get('plazo/entrega/data/datatable', 'PlazoEntregaController@datatable')->name('plazo.entrega.datatable');
    Route::post('plazo/entrega/save', 'PlazoEntregaController@store')->name('plazo.entrega.store');
    Route::post('plazo/entrega/update', 'PlazoEntregaController@update')->name('plazo.entrega.update');
    Route::post('plazo/entrega/delete', 'PlazoEntregaController@destroy')->name('plazo.entrega.delete');
    //tags
    Route::get('/tags', 'TagsController@index')->name('tags');
    Route::get('/tags/edit/{id}', 'TagsController@edit')->name('tags.edit');
    Route::get('/tags/data/datatable', 'TagsController@datatable')->name('tags.datatable');
    Route::post('/tags/save', 'TagsController@store')->name('tags.store');
    Route::post('/tags/update', 'TagsController@update')->name('tags.update');
    Route::post('/tags/delete', 'TagsController@destroy')->name('tags.delete');
    //
    Route::get('/tags/articulos/data/datatable', 'TagsController@datatableSincronizado')->name('tags.articulo.datatable');
    Route::get('/tags/articulos/info', 'TagsController@dataInfo')->name('tags.articulo.info');
    Route::post('/tags/articulos/storeupdate', 'TagsController@storeUpdate')->name('tags.articulo.store.update');
    Route::post('/tags/articulos/remove', 'TagsController@remove')->name('tags.articulo.remove');
    //privilegios
    Route::get('/privilegios', 'PrivilegiosController@index')->name('privilegios');  
    Route::post('/privilegios', 'PrivilegiosController@getPerfil')->name('privilegios.perfil'); 
    Route::put('/privilegios', 'PrivilegiosController@addPrivilegio')->name('add_privilegio'); 
    Route::delete('/privilegios', 'PrivilegiosController@delPrivilegio')->name('del_privilegio'); 
    //reportes Ventas por fecha
    Route::get('reportes/ventas', 'ReportesVentasController@index')->name('admin.reportes.ventas.index');
    Route::get('reportes/ventas/data', 'ReportesVentasController@data')->name('admin.reportes.ventas.data');

});
 //latitud longitud
 Route::get('latitud/longitud', 'LatitudLongitudController@index');
