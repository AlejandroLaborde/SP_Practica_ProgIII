<?php
namespace App\Models\ORM;
use App\Models\ORM\usuario;
use App\Models\ORM\profesor_materia;
use App\Models\AutentificadorJWT;

include_once __DIR__ . '/usuario.php';
include_once __DIR__ . '/profesor_materia.php';
include_once __DIR__ . '../../modelAPI/AutentificadorJWT.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class ejemplos 
{
// 


//Lectura en general

$foto=$request->getUploadedFiles();
$token=$request->getHeader('token');
$params= $request->getParsedBody();
$params=$args["nombre"];
$usuario = usuario::where('id','=',$params['legajo'])->get();
$mensaje=["mensaje"=>"aca va mensaje"];
$newResp = $response->withJson($mensaje,200);






//IMAGENES
    // $foto=$request->getUploadedFiles();
    // $user->email=$params["email"];
    // if($foto!=null){
    //     $nombre=$foto["foto"]->getClientFilename();
    //     $extencion= explode(".",$nombre);
    //     $foto["foto"]->moveTo('../src/app/imagenes/'.$legajo.".".$extencion[1]);
    //     $user->foto = $legajo.".".$extencion[1];
    // }
    // $user->save();


//RECORRER RELACION

// $materias=explode(",", $params["materias"]);
                    
// profesor_materia::where('legajo',$legajo)->delete();

// for($i=0;$i<count($materias);$i++){
//         $pro_mat= new profesor_materia;
//         $pro_mat->legajo=$legajo;
//         $pro_mat->materia=$materias[$i];
//         $pro_mat->save();
// } 


public function EstadosTickets($request, $response, $args){
        
    $rol= ticket::where('tickets.id','!=',0)
    ->join('estados','tickets.estado','estados.id')
    ->join('mesas','tickets.codMesa','mesas.id')
    ->select(array('tickets.codigo','estados.estado'))
    ->get();
    $nuevoRetorno= $response->withJson($rol,200);
    return $nuevoRetorno;
}

//VALIDA TOKEN

public function validaToken($request,$response,$next){
    $token=$request->getHeader('token');
    
    if(count($token)!=0){
        if(AutentificadorJWT::VerificarToken($token[0])){
            $newResp=$next($request,$response);
        }else
        {
            $newResp = $response->withJson("El token enviado es invalido",200);
        }
            
    }else
    {
        $newResp = $response->withJson("Debe setear el token en los header",200);
    }
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


public function validaAdmin($request,$response,$next){

    $token=$request->getHeader('token');
    $data=AutentificadorJWT::ObtenerData($token[0]);
    if($data->tipo == 'admin'){
        $newResp=$next($request,$response);        
    }else
    {
        $newResp = $response->withJson("Se esperaba un token de tipo admin",200);
    }
    return $newResp;
}
//date('Y-m-d H:i:s')
//loggueoo
// $app->add(function ($req, $res, $next) use ($container) {
//     $info=array();
//     $info["metodo"]=$req->getMethod();
//     $info["URI"]=$req->getUri()->getBaseUrl();
//     $info["RUTA"]=$req->getUri()->getPath();
//     $info["autoridad"]=$req->getUri()->getAuthority();
    
//     $datos=implode(";", $info);
//     $datos=http_build_query( $info,'',', ');
//     $container->get('logger')->info($datos);
//    // $container->get('logger')->addCritical('Hey, a critical log entry!');
//     $response = $next($req, $res);
//     return $response;
// });

}



public function altaUsuario($request,$response,$args){

    $token=$request->getHeader('token');
    $data=AutentificadorJWT::ObtenerData($token[0]);

        try{
            $datos=$request->getParsedBody();
            $encargado = new encargado();
            $encargado->nombre=$datos["nombre"];
            $encargado->apellido=$datos["apellido"];
            $encargado->rol=$datos["rol"];
            $encargado->usuario=$datos["usuario"];
            $encargado->clave=$datos["clave"];
            $encargado->save();
            
            $newResponse = $response->withJson($encargado
            ->where('usuario',$datos["usuario"])
            ->where('clave',$datos["clave"])
            ->select(array('nombre','apellido','usuario'))->get()); 

        }catch(\Exception $e){

            $mensaje=["mensaje"=>"Error al dar de alta usuario","causa"=>"ya existe el usuario ingresado"];
            $newResponse = $response->withJson($mensaje,500); 
        }
        
    return $newResponse;

}

public function logIn($request,$response,$args){
    
    try{
        $datos=$request->getParsedBody();
        
        $query=encargado::where('usuario','=',$datos["usuario"])
        ->join('roles','encargados.rol','roles.id')
        ->get();

        if( $datos["usuario"]==$query[0]->usuario && $datos["clave"]==$query[0]->clave){

            $datos=[];
            $hash=new \stdClass();
            $hash->nombre=$query[0]->nombre;
            $hash->apellido=$query[0]->apellido;
            $hash->usuario=$query[0]->usuario;
            $hash->codRol=$query[0]->rol;
            $hash->puesto=$query[0]->puesto;
            $token= AutentificadorJWT::CrearToken((array)$hash); 
            $newResponse = $response->withJson($token, 200); 

        }else{
            $newResponse = $response->withJson("No se pudo validar, usuario o contraseña incorrectos", 200); 
        }
    
    }catch(\Exception $e){
        
        $mensaje=["mensaje"=>"Error al intentar logIn"];
        $newResponse = $response->withJson($mensaje,500);
    }

    return  $newResponse;

}



public function diferenciaSegunTipo($request,$response,$next){
    $token=$request->getHeader('token');
    $data=AutentificadorJWT::ObtenerData($token[0]);

    switch($data->tipo){
            case 'alumno':
                $newResp=$next($request,$response);        
            break;
            case 'profesor':
                $newResp=$next($request,$response);        
            break;
            case 'admin':

                $newResp=$next($request,$response);        
            break;

    }

    $newResp = $response->withJson("Se esperaba un token de tipo alumno",200);
    return $newResp;
}

