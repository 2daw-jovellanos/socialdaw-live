<?php
namespace controller;
use \model\Usuario;
use \model\Orm;
require_once ("funciones.php");


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
        $user->login = sanitizar(strtolower($_REQUEST["login"]));
        $user->password = password_hash($_REQUEST["password"], PASSWORD_DEFAULT);
        $user->email = sanitizar($_REQUEST["email"]);
        $user->nombre = sanitizar($_REQUEST["nombre"]);
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
        
        $login = strtolower(sanitizar($_REQUEST["login"]));
        $password = $_REQUEST["password"];
        $user = (new Orm) -> obtenerUsuario($login);
        if (!$user) {
            $user = new Usuario;
        }
        if (!password_verify($password, $user->password))  {
            $msg = "login o contraseña incorrecto";
            echo \dawfony\Ti::render("view/formlogin.phtml", compact("msg", "login"));
        } else {
            //GUARDAR CREDENCIALES
            $_SESSION["login"] = $login;
            $_SESSION["rol_id"] = $user->rol_id;
            global $URL_PATH;
            header("Location: $URL_PATH/"); // Mandar al cliente al inicio
        }

    }
    public function hacerLogout()
    {
        global $URL_PATH;
        session_destroy();
        header("Location: $URL_PATH/");
    }

    public function verPerfil($login)
    {
        $orm = new Orm;
        $user = $orm->obtenerUsuario($login);
        $posts = $orm->obtenerPostsPorUsuario($login);
        $user->seguidores = $orm->contarSeguidores($login);
        $user->siguiendo = $orm->contarSiguiendo($login);
        if (isset($_SESSION["login"])) {
            $user->loSigues = $orm->loSigues($_SESSION["login"], $login);
        }
        echo \dawfony\Ti::render("view/perfil.phtml",compact("user", "posts"));
    }

    public function seguirPerfil($login)
    {
        global $URL_PATH;
        if (!isset($_SESSION["rol_id"])) {
            throw new \Exception("Intento de seguir a un usuario por parte de usuario no logueado");
        }
        $orm = new Orm;
        $orm->seguir($_SESSION["login"], $login);
        header("Location: $URL_PATH/perfil/$login");
    }
    
    public function noSeguirPerfil($login)
    {
        global $URL_PATH;
        if (!isset($_SESSION["rol_id"])) {
            throw new \Exception("Intento de dejar de seguir a un usuario por parte de usuario no logueado");
        }
        $orm = new Orm;
        $orm->noSeguir($_SESSION["login"], $login);
        header("Location: $URL_PATH/perfil/$login");
    }


}
