<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMW{

    public function VerificarAccesoSocio(Request $request, RequestHandler $handler): Response{
        $clave_usuario = $request->getHeaderLine('clave');
        $tipo_usuario = $request->getHeaderLine('tipo');
        $response = new Response();

        if(empty($tipo_usuario) || empty($clave_usuario)){
            $response->getBody()->write(json_encode(array("error" => "Error en los datos de la clave, algun campo esta vacio o no esta completo.")));
            $response = $response->withStatus(400);
        }else{
            if(strtolower($tipo_usuario) == "socio"){
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write(json_encode(array("error" => "No tienes accesos, solo para SOCIOS.")));
                $response = $response->withStatus(401);
            }
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>
