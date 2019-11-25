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


class usuarioControler 
{

    public function altaUsuario($request,$response,$args){
        
        $params= $request->getParsedBody();
        $usua = new usuario;
        $usua->email=$params['email'];
        $usua->clave=$params['clave'];
        $usua->tipo=$params['tipo'];
        $usua->save();
        $usua=usuario::all()->last();

        return $response->withJson("Se dio de alta el usuario con legajo: " . $usua->id,200);
    }

    public function logIn($request,$response,$args){

        $params = $request->getParsedBody();

        $usuario = usuario::where('id','=',$params['legajo'])->get();

        if(count($usuario) != 0){
            if($usuario[0]->clave==$params['clave']){
                $dataToken= new \stdClass;
                $dataToken->id=$usuario[0]->id;
                $dataToken->email=$usuario[0]->email;
                $dataToken->tipo=$usuario[0]->tipo;
                $newResp = $response->withJson(AutentificadorJWT::CrearToken($dataToken),200);
            }else
            {
                $newResp = $response->withJson("Legajo o clave incorrectas",200);
            }
        }else{
            $newResp = $response->withJson("No se encontro el legajo ingresado",200);
        }

        return $newResp;
    }

    public function update($request,$response,$args){

        $legajo=$args["legajo"];
        $data=AutentificadorJWT::ObtenerData(($request->getHeader('token'))[0]);
        $params= $request->getParsedBody();
        $user= usuario::where('id',$legajo)->first();
        if(count($user)!=0){

            switch($data->tipo)
            {
                case 'alumno': //alumno mail y foto
                    
                    $foto=$request->getUploadedFiles();
                    $user->email=$params["email"];
                    if($foto!=null){
                        $nombre=$foto["foto"]->getClientFilename();
                        $extencion= explode(".",$nombre);
                        $foto["foto"]->moveTo('../src/app/imagenes/'.$legajo.".".$extencion[1]);
                        $user->foto = $legajo.".".$extencion[1];
                    }
                    $user->save();
                    $newResp = $response->withJson("Se edito el alumno", 200);

                break;

                case 'profesor'://Si es profesor email y materias dictadas(pueden ser varias).
                    $user->email=$params["email"];
                    $user->save();
                    $materias=explode(",", $params["materias"]);
                    
                    profesor_materia::where('legajo',$legajo)->delete();
                    
                    for($i=0;$i<count($materias);$i++){
                            $pro_mat= new profesor_materia;
                            $pro_mat->legajo=$legajo;
                            $pro_mat->materia=$materias[$i];
                            $pro_mat->save();
                    } 
                    $newResp = $response->withJson("Se edito el profesor", 200);
                break;
                
            }
        }else
        {
            $newResp = $response->withJson("no se encontro el legajo enviado", 200);
        }
        
        return $newResp;
    }




    public function parametrosValidosLogIn($request,$response,$next){
        $parametros=$request->getParsedBody();
        if(isset($parametros["legajo"]) && isset($parametros["clave"])){
                $newResp=$next($request,$response);
        }else{
            $newResp = $response->withJson("debe completar los parametros legajo y clave",200);
        }
        return $newResp;
    }

    public function parametrosValidosAlta($request,$response,$next){

        $parametros=$request->getParsedBody();
        if(isset($parametros["email"]) && isset($parametros["clave"]) && isset($parametros["tipo"])){
            if(usuarioControler::validaTipo($parametros['tipo'])){
                $newResp=$next($request,$response);
            }else{
                $newResp = $response->withJson("solo se admite tipo alumno,profesor o admin",200);
            }
        }else{
            $newResp = $response->withJson("debe completar los parametros email,clave y tipo",200);
        }
        return $newResp;
    }

    public function validaTipo($tipo){

        if($tipo=='alumno' || $tipo=='profesor' || $tipo=='admin'){
            return true;
        }else
        {
            return false;
        }
    }




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

    public function validaProfesor($request,$response,$next){
        $token=$request->getHeader('token');
        $data=AutentificadorJWT::ObtenerData($token[0]);
        if($data->tipo == 'profesor'){
            $newResp=$next($request,$response);        
        }else
        {
            $newResp = $response->withJson("Se esperaba un token de tipo profesor",200);
        }
        return $newResp;
    }

    public function validaAlumno($request,$response,$next){
        $token=$request->getHeader('token');
        $data=AutentificadorJWT::ObtenerData($token[0]);
        if($data->tipo == 'alumno'){
            $newResp=$next($request,$response);        
        }else
        {
            $newResp = $response->withJson("Se esperaba un token de tipo alumno",200);
        }
        return $newResp;
    }









}