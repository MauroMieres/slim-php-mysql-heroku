<?php

class Pedido
{
    public $id;
    public $id_mesa;
    public $minutosEspera;
    public $estado;
    public $fecha;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $codigo = uniqid();
        // Convierte la cadena única en un hash MD5 para asegurarse de que sea alfanumérica
        $hash = md5($codigo);
        // Toma los primeros $longitud caracteres del hash
        $codigo = substr($hash, 0, 5);

        $fecha = date('Y-m-d H:i');
        var_dump($fecha);

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (id,id_mesa,minutosEspera,estado,fecha) 
                                                                     VALUES (:id,:id_mesa,:minutosEspera,:estado,:fecha)");

        $consulta->bindValue(':id', $codigo, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':minutosEspera', 0, PDO::PARAM_INT);
        $consulta->bindValue(':estado', "En preparacion", PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);

        $consulta->execute();

        return $codigo;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerProductosPedido($id)
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT v.id_producto, p.nombre AS producto, v.cantidad, v.minutosEspera 
                  FROM ventas v 
                  JOIN productos p ON v.id_producto = p.id 
                  WHERE v.id_pedido = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $ventas = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $ventas;
    }

    public static function obtenerTiempoPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id as IdPedido,minutosEspera FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function modificarPedido($id, $id_mesa, $id_cliente, $id_producto, $sector, $minutosEspera, $estado, $cantidad)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos 
                                                    SET id_mesa = :id_mesa,
                                                    id_cliente = :id_cliente,
                                                    id_producto = :id_producto,
                                                    sector = :sector,
                                                    minutosEspera = :minutosEspera,
                                                    estado = :estado,
                                                    cantidad = :cantidad
                                                    WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->bindValue(':minutosEspera', $minutosEspera, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function agregarProductosPedido($id_pedido,$id_producto,$minutosEspera,$cantidad)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta("INSERT INTO ventas (id_pedido,id_producto,minutosEspera,cantidad)
                                                                     VALUES (:id_pedido,:id_producto,:minutosEspera,:cantidad)");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);    
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);  
        $consulta->bindValue(':minutosEspera', $minutosEspera, PDO::PARAM_INT);  
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $consulta->execute();

        $array = Pedido::obtenerTiempoPedido($id_pedido);
        $minutosEsperaPedido = $array[0]->minutosEspera;

        $minutosEsperaPedido+= $minutosEspera;

        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos 
                                                  SET minutosEspera = :minutosEspera
                                                  WHERE id = :id_pedido");

    $consulta->bindValue(':minutosEspera', $minutosEsperaPedido, PDO::PARAM_INT);
    $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
    $consulta->execute();

    }


    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', "BORRADO", PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function obtenerPedidosPorSector($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos 
                                                        WHERE sector = :sector");
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPedidosListosParaEntregar()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos 
                                                        WHERE estado = 'LISTO PARA ENTREGAR'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }


    public static function actualizarEstadoPedido($idPedido, $estado, $sector, $tiempo)
    {

        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos 
                                                        SET estado = :estado,
                                                        minutosEspera = :tiempo 
                                                        WHERE id = :id 
                                                        AND sector = :sector");
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public static function actualizarEstadoPedidoPorId($idPedido, $estado, $tiempo)
    {

        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos 
                                                        SET estado = :estado,
                                                        minutosEspera = :tiempo 
                                                        WHERE id = :id ");
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);

        return $consulta->execute();
    }


    public static function existeIdEnEseSector($idPedido, $sector)
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos 
                                                        WHERE id = :id
                                                        AND sector = :sector");
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Pedido');
    }
}