<?php
require 'vendor/autoload.php'; // El autoload de las clases gestionadas por composer
require 'cargarconfig.php'; // Carga de la configuración, autoload para nuestras clases y tareas de inicialización

use dawfony\KlastoException;
use NoahBuscher\Macaw\Macaw;


/* ABRIR SESIÓN: Abrir sesión para todos los usuarios, incluso para los anónimos supone una pequeña sobrecarga
   pero también una gran comodidad. 
*/
session_start();

/* DEFINICIÓN DE RUTAS */

// página principal
Macaw::get($URL_PATH . '/', "controller\PostController@listarLoUltimo");

// loultimo
Macaw::get($URL_PATH . '/loultimo', "controller\PostController@listarLoUltimo");
Macaw::get($URL_PATH . '/loultimo/pag/(:num)', "controller\PostController@listarLoUltimo");

// registro
Macaw::get($URL_PATH . '/registro', "controller\UserController@formularioRegistro");
Macaw::post($URL_PATH . '/registro', "controller\UserController@procesarRegistro");

// login
Macaw::get($URL_PATH . '/login', "controller\UserController@formularioLogin");
Macaw::post($URL_PATH . '/login', "controller\UserController@procesarLogin");

// logout
Macaw::get($URL_PATH . '/logout', "controller\UserController@hacerLogout");

// ver perfil
Macaw::get($URL_PATH . '/perfil/(:any)', "controller\UserController@verPerfil");

// ver post
Macaw::get($URL_PATH . '/post/(:num)', "controller\PostController@verPost");

// nuevo post
Macaw::get($URL_PATH . '/post/new', "controller\PostController@formularioNuevoPost");
Macaw::post($URL_PATH . '/post/new', "controller\PostController@procesarNuevoPost");

// nuevo comentario
Macaw::post($URL_PATH . '/post/(:num)/comentario/new', "controller\PostController@procesarNuevoComentario");



// API JSON
Macaw::get($URL_PATH . '/api/like/(:num)', "controller\ApiController@likeClicked");
Macaw::get($URL_PATH . '/api/existe/(:any)', "controller\ApiController@existeLogin");


// Captura de URL no definidas. Página de respuesta 404 personalizada
Macaw::error(function() {
  (new \controller\ErrorController) -> gestionarNotFound();
});

// Despachar rutas, con captura de Excepciones. Página de respuesta 500 personalizada.
try {
  Macaw::dispatch();
} catch (Exception $ex) {
  (new \controller\ErrorController) -> gestionarExcepcion($ex);
}
