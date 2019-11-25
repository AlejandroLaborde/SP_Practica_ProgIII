<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\cd;
use App\Models\cdApi;



return function (App $app) {
    $container = $app->getContainer();

    // Rutas ORM
    $routes = require __DIR__ . '/../src/routes/routesORM.php';
    $routes($app);

    // Rutas ORM
    $routes = require __DIR__ . '/../src/routes/routesUsuarios.php';
    $routes($app);  

    // Rutas ORM
    $routes = require __DIR__ . '/../src/routes/routesMateria.php';
    $routes($app);  



    // $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
    //     $container->get('logger')->info("Slim-Skeleton '/' route");
    //     return $container->get('renderer')->render($response, 'index.phtml', $args);
    // });

};
