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
     * @return mixesd el usuario o null si no se encuentra.¡
     */
    public function obtenerUsuario($user) {
        return Klasto::getInstance() -> queryOne(
            "SELECT login, password, nombre, email, rol_id from usuario WHERE login = ? ",
            [$user],
            "model\Usuario"
        );
    }


}