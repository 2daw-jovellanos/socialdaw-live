<?php
namespace controller;
use \dawfony\Ti;
class PostController extends Controller {

    function principal () {
        echo Ti::render("view/listado.phtml",[]);
    }

}