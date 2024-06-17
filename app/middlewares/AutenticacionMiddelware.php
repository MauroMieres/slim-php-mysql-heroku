<?php

require_once './models/AutentificadorJWT.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AutenticacionMiddelware
{

    public function VerificarToken(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $header = $request->getHeaderLine('Authorization');
        // Valido si el header es null o vacio.
        if($header == null || $header == ""){
          $esValido = "vacio";
        }else{
          // En caso de que no sea vacio o null, valido el token.
          $token = trim(explode("Bearer", $header)[1]);
          AutentificadorJWT::verificarToken($token);
          // Si no exploto nada durante la validacion, asigno que el token es valido.
          $esValido = "si";
        }
      } catch (Exception $e) {
        //Si exploto algo, asigno que el token, no es valido.
        $payload = json_encode(array('error' => $e->getMessage()));
        $esValido = "no";
      }

      if ($esValido == "si") {
        $dataToken = AutentificadorJWT::ObtenerData($token);
        $requestContent = $request->getParsedBody();
        $payload = array("body" => $requestContent, "dataToken" => $dataToken);
        $request = $request->withParsedBody($payload);
        $response = $handler->handle($request);
      }else if ($esValido == "vacio"){
        $response->getBody()->write('Error, el token esta vacio.');
      }else{
        $response->getBody()->write('Error, el token no es valido.');
      }
      return $response->withHeader('Content-Type', 'application/json');
    }
    
}