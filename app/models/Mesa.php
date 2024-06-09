<?php

class Mesa
{
    public $id;
    public $id_cliente;
    public $id_mozo;
    public $estado_mesa;
    public $capacidad;
    public $cuenta;


    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (id_cliente,id_mozo,estado_mesa,capacidad,cuenta) 
                                                                  VALUES (:id_cliente,:id_mozo,:estado_mesa,:capacidad,:cuenta)");
        $consulta->bindValue(':id_cliente', $this->id_cliente, PDO::PARAM_INT);
        $consulta->bindValue(':id_mozo', $this->id_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':estado_mesa',"ACTIVA", PDO::PARAM_STR);
        $consulta->bindValue(':capacidad', $this->capacidad, PDO::PARAM_INT);
        $consulta->bindValue(':cuenta',$this->cuenta, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificarMesa($id,$id_cliente,$id_mozo,$estado_mesa,$capacidad,$cuenta)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas 
                                                    SET id_cliente = :id_cliente,
                                                        id_mozo = :id_mozo,
                                                        estado_mesa = :estado_mesa,
                                                        capacidad = :capacidad,
                                                        cuenta = :cuenta
                                                    WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $consulta->bindValue(':id_mozo', $id_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':estado_mesa', $estado_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':capacidad', $capacidad, PDO::PARAM_INT);
        $consulta->bindValue(':cuenta', $cuenta, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function actualizarEstadoMesa($id,$estado_mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas 
                                                    SET estado_mesa = :estado_mesa
                                                    WHERE id = :id");
    
        $consulta->bindValue(':estado_mesa', $estado_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarMesa($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado_mesa = :estado_mesa WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':estado_mesa',"INACTIVO", PDO::PARAM_STR);
        $consulta->execute();
    }

    //crear consumosmesa, (revisa los productos, cantidad, y calcula la cuenta)
    //saldar la cuenta = la mesa queda pagada
    //cargar cuenta mesa

}