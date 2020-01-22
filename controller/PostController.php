<?php
namespace controller;
use \dawfony\Ti;
use \model\Orm;

class PostController extends Controller {

    function listarLoUltimo() {
        $posts = (new Orm) ->obtenerUltimosPosts();
        echo Ti::render("view/listado.phtml",compact("posts"));
    }

}