<?php

namespace model;

use dawfony\Klasto;

class Orm
{


    public function crearUsuario($user)
    {
        Klasto::getInstance()->execute(
            "INSERT INTO usuario(login, password, nombre, email, rol_id) "
                . "VALUES (?, ?, ?, ?, ?)",
            [$user->login, $user->password, $user->nombre, $user->email, 1]
        );
    }

    public function obtenerUsuario($login)
    {
        return Klasto::getInstance()->queryOne(
            "SELECT login, password, nombre, email, rol_id from usuario WHERE login = ? ",
            [$login],
            "model\Usuario"
        );
    }

    public function contarSeguidores($login)
    {
        return Klasto::getInstance()->queryOne(
            "SELECT count(*) as cuenta from sigue WHERE usuario_login_seguido = ? ",
            [$login]
        )["cuenta"];
    }

    public function contarSiguiendo($login)
    {
        return Klasto::getInstance()->queryOne(
            "SELECT count(*) as cuenta from sigue WHERE usuario_login_seguidor = ? ",
            [$login]
        )["cuenta"];
    }

    public function loSigues($tulogin, $login)
    {
        return Klasto::getInstance()->queryOne(
            "SELECT count(*) as cuenta from sigue"
                ." WHERE usuario_login_seguidor = ? "
                ." AND usuario_login_seguido = ? ",
            [$tulogin, $login]
        )["cuenta"] > 0;
    }

    public function seguir($tulogin, $login) {
        Klasto::getInstance()->execute(
            "INSERT INTO sigue(usuario_login_seguidor, usuario_login_seguido)"
                . " VALUES(?,?)",
                [$tulogin, $login]
        );
    }

    public function noSeguir($tulogin, $login) {
        Klasto::getInstance()->execute(
            "DELETE FROM sigue WHERE usuario_login_seguidor=? AND usuario_login_seguido=?",
                [$tulogin, $login]
        );
    }

    public function darOQuitarLike($postid, $login)
    {
        $db = Klasto::getInstance();
        $num = $db->execute(
            "DELETE FROM `like` WHERE post_id = ? AND usuario_login = ?",
            [$postid, $login]
        );
        if ($num > 0) {
            return false; // Ya no tiene like
        }
        $db->execute(
            "INSERT INTO `like`(post_id, usuario_login) VALUES(?,?)",
            [$postid, $login]
        );
        return true; // Sí tiene like

    }

    public function obtenerCategorias()
    {
        return Klasto::getInstance()->query(
            "SELECT id, descripcion"
                . " FROM categoria_post"
        );
    }

    public function contarLikes($postid)
    {
        return Klasto::getInstance()->queryOne(
            "SELECT count(*) as cuenta FROM `like` WHERE post_id = ?",
            [$postid]
        )["cuenta"];
    }

    public function contarComments($postid)
    {
        return Klasto::getInstance()->queryOne(
            "SELECT count(*) as cuenta FROM comenta WHERE post_id = ?",
            [$postid]
        )["cuenta"];
    }

    public function obtenerComments($postid)
    {
        return Klasto::getInstance()->query(
            "SELECT usuario_login, texto, fecha FROM comenta WHERE post_id = ? ORDER BY fecha DESC",
            [$postid],
            "\model\Comentario"
        );
    }

    public function leHaDadoLike($postid, $login)
    {
        return (Klasto::getInstance()->queryOne(
            "SELECT count(*) as cuenta FROM `like` WHERE post_id = ? and usuario_login = ?",
            [$postid, $login]
        )["cuenta"]) > 0;
    }


    public function obtenerPost($postid)
    {
        $post = Klasto::getInstance()->queryOne(
            "SELECT `id`, `fecha`, `resumen`, `texto`, `foto`, `categoria_post_id`, `usuario_login` FROM `post`"
                . " WHERE id=?",
            [$postid],
            "model\Post"
        );
        $post->numLikes = $this->contarLikes($postid);
        $post->comentarios = $this->obtenerComments($postid);
        $post->numComments = count($post->comentarios);
        $categorias = $this->obtenerCategorias();
        $post->categoria = $categorias[$post->categoria_post_id]["descripcion"];
        return $post;
    }


    
    public function contarUltimosPosts(){
        return Klasto::getInstance()->queryOne("SELECT count(*) as cuenta FROM `post`")["cuenta"];
    }

    public function obtenerUltimosPosts($page = 1)
    {
        global $config;
        $limit = $config["post_per_page"];
        $offset = ($page -1) * $limit;
        $posts = Klasto::getInstance()->query(
            "SELECT `id`, `fecha`, `resumen`, `texto`, `foto`, `categoria_post_id`, `usuario_login`"
                . " FROM `post`"
                . " ORDER BY `fecha` DESC"
                . " LIMIT $limit OFFSET $offset",
            [],
            "model\Post"
        );
        $categorias = $this->obtenerCategorias();
        foreach ($posts as $post) {
            $post->numLikes = $this->contarLikes($post->id);
            $post->numComments = $this->contarComments($post->id);
            $post->categoria = $categorias[$post->categoria_post_id]["descripcion"];
        }
        return $posts;
    }


    public function contarPostsSeguidos($login) {
        return Klasto::getInstance()->queryOne(
            "SELECT count(*) as cuenta"
                . " FROM `post` JOIN `sigue` ON post.usuario_login = sigue.usuario_login_seguido"
                . " WHERE sigue.usuario_login_seguidor = ?",
            [$login]
        )["cuenta"];
    }

    public function obtenerPostsSeguidos($login, $page = 1)
    {
        global $config;
        $limit = $config["post_per_page"];
        $offset = ($page -1) * $limit;
        $posts = Klasto::getInstance()->query(
            "SELECT `id`, `fecha`, `resumen`, `texto`, `foto`, `categoria_post_id`, `usuario_login`"
                . " FROM `post` JOIN `sigue` ON post.usuario_login = sigue.usuario_login_seguido"
                . " WHERE sigue.usuario_login_seguidor = ?"
                . " ORDER BY `fecha` DESC"
                . " LIMIT $limit OFFSET $offset",
            [$login],
            "model\Post"
        );
        $categorias = $this->obtenerCategorias();
        foreach ($posts as $post) {
            $post->numLikes = $this->contarLikes($post->id);
            $post->numComments = $this->contarComments($post->id);
            $post->categoria = $categorias[$post->categoria_post_id]["descripcion"];
        }
        return $posts;
    }



    public function obtenerPostsPorUsuario($login)
    {
        return Klasto::getInstance()->query(
            "SELECT post.id, fecha, resumen, descripcion as categoria"
                . " FROM post JOIN categoria_post ON categoria_post_id = categoria_post.id"
                . " WHERE usuario_login = ?"
                . " ORDER BY fecha DESC",
            [$login],
            "model\Post"
        );
    }




    function insertarPost(&$post)
    {
        Klasto::getInstance()->execute(
            "INSERT INTO `post`(`fecha`, `resumen`, `texto`, `foto`, `categoria_post_id`, `usuario_login`)"
                . " VALUES (?,?,?,?,?,?)",
            [
                $post->fecha, $post->resumen, $post->texto,
                $post->foto, $post->categoria_post_id, $post->usuario_login
            ]
        );
        $post->id = Klasto::getInstance()->getInsertId();
    }

    function insertarComentario($comentario) {
        Klasto::getInstance()->execute(
            "INSERT INTO `comenta`(`post_id`, `usuario_login`, `fecha`, `texto`)"
                . " VALUES (?,?,?,?)",
            [$comentario->post_id, $comentario->usuario_login,
            $comentario->fecha, $comentario->texto]
        );
    }
}
