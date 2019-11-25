<?php
namespace App\Models;
use Firebase\JWT\JWT;
use Exception;
class AutentificadorJWT
{
    private static $claveSecreta = 'ClaveSecreta2695175342382582';
    private static $tipoEncriptacion = ['HS256'];
    private static $aud = null;
    
    public static function CrearToken($datos)
    {
       
        $payload = array(  	
            'datos'=>$datos,
            'app'=> "ParcialLabordeParodiAlejandro"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }
    
    public static function VerificarToken($token)
    {
        $valido=false;
        if(empty($token))
        {
            throw new Exception("El token esta vacio.");
        } 
      
      try {
            $decodificado = JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
            $valido=true;
        } catch (\Exception $e) {
            $valido=false;
        } 
        return $valido;
    }
    
     public static function ObtenerPayLoad($token)
    {
        return JWT::decode($token,AutentificadorJWT::$claveSecreta,AutentificadorJWT::$tipoEncriptacion);
    }
     public static function ObtenerData($token)
    {
        $ret= new \stdClass;
        $ret=JWT::decode($token,AutentificadorJWT::$claveSecreta,AutentificadorJWT::$tipoEncriptacion);
        return $ret->datos;
    }


    // public function validaToken($request,$response,$next){
    //     $token=$request->getHeader('token');
        
    //     if(count($token)!=0){
    //         if(AutentificadorJWT::VerificarToken($token[0])){
    //             $newResp=$next($request,$response);
    //         }else
    //         {
    //             $newResp = $response->withJson("El token enviado es invalido",200);
    //         }
                
    //     }else
    //     {
    //         $newResp = $response->withJson("Debe setear el token en los header",200);
    //     }
    //     return $newResp;
    // }

}