<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\ORM\usuario;
use App\Models\ORM\usuarioControler;

include_once __DIR__ . '/../../src/app/models/usuario.php';
include_once __DIR__ . '/../../src/app/models/usuarioControler.php';

return function (App $app) {


    
    $app->post('/login' ,  usuarioControler::class . ':logIn')
        ->add(usuarioControler::class . ':parametrosValidosLogIn');   

    $app->group('/usuario',function(){

        $this->post('', usuarioControler::class . ':altaUsuario')
        ->add(usuarioControler::class . ':parametrosValidosAlta');

        $this->post('/{legajo}', usuarioControler::class . ':update')
            ->add(usuarioControler::class . ':validaToken');

    });
    



};