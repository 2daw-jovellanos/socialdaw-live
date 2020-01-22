<?php
namespace controller;
use \dawfony\Ti;
use \model\Orm;

class PostController extends Controller {

    function listarLoUltimo() {
        $posts = (new Orm) ->obtenerUltimosPosts();
        echo "<pre>".var_dump($posts)."</pre>";
        echo Ti::render("view/listado.phtml",[]);
    }

}