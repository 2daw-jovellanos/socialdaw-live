<?php namespace model;
use dawfony\Klasto;

 class Orm {


    public function crearUsuario($user) {
        Klasto::getInstance()-> execute(
            "INSERT INTO usuario(login, password, nombre, email, rol_id) "
                . "VALUES (?, ?, ?, ?, ?)",
            [$user->login, $user->password, $user->nombre, $user->email, 1]
        );
    }

    /**
     * Obtiene usuario
     *
     * @return mixed el usuario o null si no se encuentra.¡
     */
    public function obtenerUsuario($user) {
        return Klasto::getInstance() -> queryOne(
            "SELECT login, password, nombre, email, rol_id from usuario WHERE login = ? ",
            [$user],
            "model\Usuario"
        );
    }

    /**
     * 
     */

    public function obtenerCategorias() {
        return Klasto::getInstance() -> query(
            "SELECT id, descripcion"
                ." FROM categoria_post"
        );
    }

    public function obtenerUltimosPosts($page=0) {

        /** TO-DO: Está sin probar aún */

        global $config;
        $limit = $config["post_per_page"];
        $offset = $page * $limit;
        $posts = Klasto::getInstance() -> query(
            "SELECT `id`, `fecha`, `resumen`, `texto`, `foto`, `categoria_post_id`, `usuario_login`"
                ." FROM `post`"
                ." ORDER BY `fecha` DESC"
                ." LIMIT $limit OFFSET $offset"
                ,
            [],
            "model\Post"
        );
        $categorias = $this->obtenerCategorias();
        foreach ($posts as $post) {
            $post->numLikes = Klasto::getInstance() -> queryOne(
                "SELECT count(*) as cuenta FROM `like` WHERE post_id = ?",
                [$post->id]
            )["cuenta"];
            $post->numComments = Klasto::getInstance() -> queryOne(
                "SELECT count(*) as cuenta FROM comenta WHERE post_id = ?",
                [$post->id]
            )["cuenta"];
            $post->categoria = $categorias[$post->categoria_post_id]["descripcion"];
        }
        return $posts;
    }


}