<?php
require 'vendor/autoload.php';
require 'cargarconfig.php';

use NoahBuscher\Macaw\Macaw;

// página principal
Macaw::get($URL_PATH . '/', "controller\PostController@principal");

// Captura de URL no definidas.
Macaw::error(function() {
  echo '404 :: Not Found';
});

Macaw::dispatch();
