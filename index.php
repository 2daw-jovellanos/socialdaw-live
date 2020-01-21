<?php
require 'vendor/autoload.php';
require 'cargarconfig.php';
session_start();
use NoahBuscher\Macaw\Macaw;

// página principal
Macaw::get($URL_PATH . '/', "controller\PostController@principal");

// registro
Macaw::get($URL_PATH . '/registro', "controller\UserController@formularioRegistro");
Macaw::post($URL_PATH . '/registro', "controller\UserController@procesarRegistro");

// login
Macaw::get($URL_PATH . '/login', "controller\UserController@formularioLogin");
Macaw::post($URL_PATH . '/login', "controller\UserController@procesarLogin");

// logout
Macaw::get($URL_PATH . '/logout', "controller\UserController@hacerLogout");

// Captura de URL no definidas.
Macaw::error(function() {
  http_response_code(404);
  echo '404 :: Not Found';
});

Macaw::dispatch();
