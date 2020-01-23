<?php 
namespace model;

class Post {
    public $id;
    public $fecha;
    public $resumen;
    public $texto;
    public $foto;
    public $categoria_post_id;
    public $usuario_login;

    public $categoria = "";
    public $numLikes = 0;
    public $numComments = 0;
    public $like = false;
}