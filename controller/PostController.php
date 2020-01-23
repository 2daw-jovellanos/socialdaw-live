<?php
namespace controller;
require_once "funciones.php";
use \dawfony\Ti;
use \model\Orm;
use \model\Post;

class PostController extends Controller {

    function listarLoUltimo() {
        $orm = new Orm;
        $posts = $orm ->obtenerUltimosPosts();
        // añadir si tienen like del usuario logueado
        if (isset($_SESSION["login"])) {
            foreach($posts as $post) {
                $post->like = $orm->leHaDadoLike($post->id,$_SESSION["login"]);
                var_dump($post->like);
            }
        }
        echo Ti::render("view/listado.phtml",compact("posts"));
    }

    function formularioNuevoPost() {
        if (!isset($_SESSION["rol_id"])) {
            throw new \Exception("Intento de inserción de usuario no logueado");
        }
        $categorias = (new Orm) ->obtenerCategorias();
        echo Ti::render("view/formpost.phtml", compact("categorias"));
    }

    function procesarNuevoPost() {
        global $URL_PATH;
        if (!isset($_SESSION["rol_id"])) {
            throw new \Exception("Intento de inserción de usuario no logueado");
        }
        // TO DO: Comprobaciones


        $post = new Post;
        $post -> fecha = date('Y-m-d H:i:s');
        $post->resumen = sanitizar($_REQUEST["resumen"]);
        $post->texto = html_purify($_REQUEST["texto"]);
        $post->foto = $_FILES["foto"]["name"];
        $post->categoria_post_id = $_REQUEST["categoria_id"];
        $post->usuario_login = $_SESSION["login"];
        move_uploaded_file($_FILES["foto"]["tmp_name"], "assets/foto/" . $post->foto);
        // TO DO: Hacer la inserción
        (new Orm)->insertarPost($post);
        header("Location: " . $URL_PATH. "/post/" . $post->id);
    }

    function verPost($postid) {
        $post = (new Orm) ->obtenerPost($postid);
        //var_dump($post);
        echo Ti::render("view/post.phtml", compact("post"));
    }

}