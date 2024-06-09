<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

        $id_cliente = $parametros['id_cliente'];
        $id_mozo = $parametros['id_mozo'];
        $capacidad = $parametros['capacidad'];
        $cuenta = $parametros['cuenta'];

        $mesa = new Mesa();
        $mesa->id_cliente = $id_cliente;
        $mesa->id_mozo = $id_mozo;
        $mesa->capacidad = $capacidad;
        $mesa->cuenta = $cuenta;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "[ALTA]: Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {

        $id = $args['id'];
        $mesa = Mesa::obtenerMesa($id);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $id_cliente = $parametros['id_cliente'];
        $id_mozo = $parametros['id_mozo'];
        $estado_mesa = $parametros['estado_mesa'];
        $capacidad = $parametros['capacidad'];
        $cuenta = $parametros['cuenta'];

        Mesa::modificarMesa($id,$id_cliente,$id_mozo,$estado_mesa,$capacidad,$cuenta);

        $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        Mesa::borrarMesa($id);

        $payload = json_encode(array("mensaje" => "[BAJA]: Mesa ".$id." desactivada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}