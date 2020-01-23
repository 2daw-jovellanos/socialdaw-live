<?php 
namespace model;

class Usuario {
    public $login;
    public $password;
    public $email;
    public $nombre;
    public $rol_id;
    public $seguidores = 0;
    public $siguiendo = 0;
}