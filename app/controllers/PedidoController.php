<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    
    public function CargarUno($request, $response, $args)
    {

        $parametros = $request->getParsedBody();
        $id_mesa = $parametros['id_mesa'];
        $id_cliente = $parametros['id_cliente'];
        $id_producto = $parametros['id_producto'];
        $sector = $parametros['sector'];
        $minutosEspera = $parametros['minutosEspera'];
        $estado = $parametros['estado'];
        $cantidad = $parametros['cantidad'];

      if (!isset($parametros) ||
                      !isset($id_mesa) ||
                      !isset($id_cliente) ||
                      !isset($id_producto) ||
                      !isset($sector)||
                      !isset($minutosEspera)||
                      !isset($estado)||
                      !isset($cantidad)) {
        $payload = json_encode(array("error" => "Faltan ingresar datos."));
        $response = $response->withStatus(400);
      } else {

        $usuario = Usuario::obtenerUsuario($id_cliente);
        $mesa = Mesa::obtenerMesa($id_mesa);

        if($usuario != null && $mesa != null){

            $pedido = new Pedido();
            $pedido->id_mesa = $id_mesa;
            $pedido->id_cliente = $id_cliente;
            $pedido->id_producto = $id_producto;
            $pedido->sector = $sector;
            $pedido->minutosEspera = $minutosEspera;
            $pedido->estado = $estado;
            $pedido->cantidad = $cantidad;
            $pedido->crearPedido();

              $payload = json_encode(array("mensaje" => "Pedido creado con exito."));
              $response = $response->withStatus(201);
        }else{
            $payload = json_encode(array("mensaje" => "Id Usuario o Id Mesa, INEXISTENTES"));
        }
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {

        $id = $args['id'];
        $pedido = Pedido::obtenerPedido($id);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ObtenerDemoraPedido($request, $response, $args)
    {

        $id_pedido = $args['id'];
        $pedido = Pedido::obtenerTiempoPedido($id_pedido);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $id_mesa = $parametros['id_mesa'];
        $id_cliente = $parametros['id_cliente'];
        $id_producto = $parametros['id_producto'];
        $sector = $parametros['sector'];
        $minutosEspera = $parametros['minutosEspera'];
        $estado = $parametros['estado'];
        $cantidad = $parametros['cantidad'];

        Pedido::modificarPedido($id,$id_mesa,$id_cliente,$id_producto,$sector,$minutosEspera,$estado,$cantidad);

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        Pedido::borrarPedido($id);

        $payload = json_encode(array("mensaje" => "[BAJA]: Pedido ".$id." borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function TraerPorSector($request, $response, $args)
    {
      $dataToken = json_decode($request->getParsedBody()["dataToken"], true);
      $tipo = $dataToken['tipo'];

      if($tipo == "CERVECERO"){

        $lista = Pedido::obtenerPedidosPorSector("CERVECERO");
        $payload = json_encode(array("PEDIDOS_CERVECEROS" => $lista));
        $response->getBody()->write($payload);
        
      }else if ($tipo == "COCINERO"){

        $lista = Pedido::obtenerPedidosPorSector("COCINERO");
        $payload = json_encode(array("PEDIDOS_COCINEROS" => $lista));
        $response->getBody()->write($payload);
        
      }else if ($tipo == "BARTENDER"){

        $lista = Pedido::obtenerPedidosPorSector("BARTENDER");
        $payload = json_encode(array("PEDIDOS_BARTENDER" => $lista));
        $response->getBody()->write($payload);
       
      }else if ($tipo == "SOCIO"){

        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("TODOS_LOS_PEDIDOS" => $lista));
        $response->getBody()->write($payload);
        

      }else if ($tipo == "MOZO"){

        $lista = Pedido::obtenerPedidosListosParaEntregar();
        $payload = json_encode(array("MOZO_PEDIDOS_POR_ENTREGAR" => $lista));
        $response->getBody()->write($payload);
        

      }else{
        $payload = json_encode(array("mensaje" => "[ERROR]: A los pedidos solo pueden acceder MOZOS, COCINEROS, BARTENDER, CERVECERO, SOCIOS"));
        $response->getBody()->write($payload);
        
      }
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ActualizarEstadoPedidoSegunSector($request, $response, $args)
    {

      $dataToken = json_decode($request->getParsedBody()["dataToken"], true);
      $parametros = $request->getParsedBody()["body"];

      $idPedido = $parametros['idPedido'];
      $estado = $parametros['estado'];
      $tipo = $dataToken['tipo'];
      $tiempo = $parametros['tiempo'];

      
      if(Pedido::existeIdEnEseSector($idPedido,$tipo) != null || $tipo == "MOZO"){

          if($tipo == "CERVECERO"){
            
            if(Pedido::actualizarEstadoPedido($idPedido,$estado,"CERVECERO",$tiempo)){
              $payload = json_encode(array("mensaje" => "PEDIDO ACTUALIZADO POR EL CERVECERO."));
            }else{
              $payload = json_encode(array("ERROR" => "Problemas con la actualizacion"));
            }

          }else if ($tipo == "COCINERO"){

            if(Pedido::actualizarEstadoPedido($idPedido,$estado,"COCINERO",$tiempo)){
              $payload = json_encode(array("mensaje" => "PEDIDO ACTUALIZADO POR EL COCINERO."));
            }else{
              $payload = json_encode(array("ERROR" => "Problemas con la actualizacion"));
            }

          }else if ($tipo == "BARTENDER"){

            if(Pedido::actualizarEstadoPedido($idPedido,$estado,"BARTENDER",$tiempo)){
              $payload = json_encode(array("mensaje" => "PEDIDO ACTUALIZADO POR EL BARTENDER."));
            }else{
              $payload = json_encode(array("ERROR" => "Problemas con la actualizacion"));
            }

          }else if ($tipo == "MOZO"){

            if(Pedido::actualizarEstadoPedidoPorId($idPedido,$estado,$tiempo)){
              $payload = json_encode(array("mensaje" => "PEDIDO ACTUALIZADO POR EL MOZO."));
            }else{
              $payload = json_encode(array("ERROR" => "Problemas con la actualizacion"));
            }
          

          }else if ($tipo == "SOCIO"){

            $payload = json_encode(array("mensaje" => "[INFO]: Como sos socio podes acceder a la version FULL sin limitaciones en *Modificar Uno*"));

          }else{
            $payload = json_encode(array("mensaje" => "[ERROR]: A los pedidos solo pueden acceder MOZOS, COCINEROS, BARTENDER, CERVECERO, SOCIOS"));
          }
          
          $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');

        }else{

          $payload = json_encode(array("mensaje" => "[ERROR]: NO EXISTE ESE ID PEDIDO ASIGNADO A TU SECTOR." ));
          
          $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');
        }
  }






}