<?php
namespace App\Models\ORM;
use App\Models\ORM\materia;

include_once __DIR__ . '/materia.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class materiaControler 
{

    public function altaMateria($request,$response,$args){

        $params= $request->getParsedBody();
        $materia = new materia;
        $materia->nombre=$params['nombre'];
        $materia->cuatrimestre=$params['cuatrimestre'];
        $materia->cupos=$params['cupos'];
        $materia->save();
        $newResp = $response->withJson("se dio de alta la materia",200); 
        return $newResp;
    }


    public function validaParametrosMateria($request,$response,$next){
        $params= $request->getParsedBody();
        if(isset($params['nombre']) && isset($params['cuatrimestre']) && isset($params['cupos'])){
            $newResp = $next($request,$response);
        }else
        {
            $newResp = $response->withJson("Debe setear los parametros nombre, cuatrimestre y cupos",200);
        }
        return $newResp;
    }


}