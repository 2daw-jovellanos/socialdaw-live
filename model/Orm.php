<?php namespace model;
use dawfony\Klasto;

 class Orm {


    public function crearUsuario($user) {
        $db = Klasto::getInstance();
        $db -> execute(
            "INSERT INTO usuario(login, password, nombre, email, rol_id) "
                . "VALUES (?, ?, ?, ?, ?)",
            [$user->login, $user->password, $user->nombre, $user->email, 1]
        );
    }

}