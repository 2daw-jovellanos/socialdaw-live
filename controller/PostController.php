<?php
namespace controller;
use \dawfony\Ti;
use \model\Orm;
use \model\Post;

class PostController extends Controller {

    function listarLoUltimo() {
        $posts = (new Orm) ->obtenerUltimosPosts();
        echo Ti::render("view/listado.phtml",compact("posts"));
    }

    function formularioNuevoPost() {
        $categorias = (new Orm) ->obtenerCategorias();
        echo Ti::render("view/formpost.phtml", compact("categorias"));
    }

    function procesarNuevoPost() {
        // TO DO: Comprobaciones
        $post = new Post;
        $post -> fecha = date('Y-m-d H:i:s');
        $post->resumen = $_REQUEST["resumen"];
        $post->texto = $_REQUEST["texto"];
        $post->foto = $_FILES["foto"]["name"];
        $post->categoria_id = $_REQUEST["categoria_id"];
        $post->usuario_login = $_SESSION["login"];
        move_uploaded_file($_FILES["foto"]["tmp_name"], "assets/foto/" . $post->foto);
        // TO DO: Hacer la inserción
        echo "<pre>".var_dump($post)."</pre>";
        
    }

}