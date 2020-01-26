<?php
namespace controller;
require_once "funciones.php";
use \dawfony\Ti;
use \model\Comentario;
use \model\Orm;
use \model\Post;

class PostController extends Controller {

    function listarLoUltimo($pagina = 1) {
        global $config;
        global $URL_PATH;
        $orm = new Orm;
        $posts = $orm ->obtenerUltimosPosts($pagina);
        // añadir si tienen like del usuario logueado
        if (isset($_SESSION["login"])) {
            foreach($posts as $post) {
                $post->like = $orm->leHaDadoLike($post->id,$_SESSION["login"]);
            }
        }
        $cuenta = $orm ->contarUltimosPosts();
        $numpaginas = ceil($cuenta / $config["post_per_page"]);
        $titulo = "Últimos posts";
        $subtitulo = "Página $pagina de $numpaginas";
        $ruta = "$URL_PATH/loultimo/pag/";
        echo Ti::render("view/listado.phtml",compact("posts","cuenta", "titulo", "subtitulo", "numpaginas", "pagina", "ruta"));
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
        $orm = new Orm;
        $post = $orm ->obtenerPost($postid);
        if (isset($_SESSION["login"])) {
            $post->like = $orm->leHaDadoLike($post->id,$_SESSION["login"]);
            
        }
        echo Ti::render("view/post.phtml", compact("post"));
    }

    function procesarNuevoComentario($postid) {
        global $URL_PATH;
        if (!isset($_SESSION["rol_id"])) {
            throw new \Exception("Intento de comentario de usuario no logueado");
        }
        $comentario = new Comentario;
        $comentario ->post_id = $postid;
        $comentario ->fecha = date('Y-m-d H:i:s');
        $comentario ->texto = sanitizar($_REQUEST["texto"]);
        $comentario ->usuario_login = $_SESSION["login"];
        (new Orm) ->insertarComentario($comentario);
        header("Location: " . $URL_PATH. "/post/" . $postid . "#comentarios");
    }

}