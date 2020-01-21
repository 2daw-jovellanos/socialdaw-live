<?php

namespace controller;

use \model\Usuario;
use \model\Orm;

class UserController extends Controller
{

    public function formularioRegistro()
    {
        echo \dawfony\Ti::render("view/formregistro.phtml");
    }

    public function procesarRegistro()
    {
        // TO DO: Faltan comprobaciones, de seguridad

        // hacer la grabación
        $user = new Usuario();
        $user->login = strtolower($_REQUEST["login"]);
        $user->password = password_hash($_REQUEST["password"], PASSWORD_DEFAULT);
        $user->email = $_REQUEST["email"];
        $user->nombre = $_REQUEST["nombre"];
        (new Orm) -> crearUsuario($user);
        // generar la vista
        $msg = "Ok, <strong>$user->login</strong>. Se ha procesado tu solicitud de registro."
            ." Ahora puedes hacer login";
        echo \dawfony\Ti::render("view/msg-success.phtml", compact("msg"));
    }

    public function formularioLogin()
    {
        echo \dawfony\Ti::render("view/formlogin.phtml");
    }

    public function procesarLogin()
    {
        
        $login = strtolower($_REQUEST["login"]);
        $password = $_REQUEST["password"];
        $user = (new Orm) -> obtenerUsuario($login);
        if (!$user) {
            $user = new Usuario;
        }
        if (!password_verify($password, $user->password))  {
            $msg = "login o contraseña incorrecto";
            echo \dawfony\Ti::render("view/formlogin.phtml", compact("msg", "login"));
        } else {
            $_SESSION["login"] = $login;
            $_SESSION["rol_id"] = $user->rol_id;
            global $URL_PATH;
            header("Location: $URL_PATH/");
        }

    }
    public function hacerLogout()
    {
        global $URL_PATH;
        session_destroy();
        header("Location: $URL_PATH/");
    }
}
