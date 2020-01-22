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

// registro
Macaw::get($URL_PATH . '/registro', "controller\UserController@formularioRegistro");
Macaw::post($URL_PATH . '/registro', "controller\UserController@procesarRegistro");

// login
Macaw::get($URL_PATH . '/login', "controller\UserController@formularioLogin");
Macaw::post($URL_PATH . '/login', "controller\UserController@procesarLogin");

// logout
Macaw::get($URL_PATH . '/logout', "controller\UserController@hacerLogout");

// nuevo post
Macaw::get($URL_PATH . '/post/new', "controller\PostController@formularioNuevoPost");
Macaw::post($URL_PATH . '/post/new', "controller\PostController@procesarNuevoPost");

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
