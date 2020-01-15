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
        echo "TO - DO: Procesar login";
    }
}
