<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\ORM\materiaControler;
use App\Models\ORM\usuarioControler;

include_once __DIR__ . '/../../src/app/models/materia.php';
include_once __DIR__ . '/../../src/app/models/materiaControler.php';
include_once __DIR__ . '/../../src/app/models/usuarioControler.php';

return function (App $app) {


    $app->post('/materia', materiaControler::class . ':altaMateria')
    ->add(materiaControler::class . ':validaParametrosMateria')
    ->add(usuarioControler::class . ':validaAdmin')
    ->add(usuarioControler::class . ':validaToken');

};