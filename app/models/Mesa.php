<?php

class Mesa
{
    public $id;
    public $id_cliente;
    public $id_mozo;
    public $estado_mesa;


    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $id_mesa = Mesa::generarCodigoMesa();

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (id,id_cliente,id_mozo,estado_mesa) 
                                                                  VALUES (:id,:id_cliente,:id_mozo,:estado_mesa)");
        $consulta->bindValue(':id', $id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':id_cliente', 0, PDO::PARAM_INT);
        $consulta->bindValue(':id_mozo', 0, PDO::PARAM_INT);
        $consulta->bindValue(':estado_mesa', "libre", PDO::PARAM_STR);
        $consulta->execute();

        return $id_mesa;
    }


    function generarCodigoMesa($longitud = 5)
    {
        // Caracteres permitidos: solo letras mayúsculas
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $caracteresLongitud = strlen($caracteres);
        $codigo = '';

        // Generar una cadena de caracteres aleatorios
        for ($i = 0; $i < $longitud; $i++) {
            $indiceAleatorio = rand(0, $caracteresLongitud - 1);
            $codigo .= $caracteres[$indiceAleatorio];
        }

        return $codigo;
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
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function obtenerMesaLibreExiste($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE id = :id AND estado_mesa = 'libre'");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        $rta = $consulta->fetchObject('Mesa');
        if (!empty($rta)) {
            return "libre";
        } else {
            return "La mesa no existe o esta ocupada";
        }
    }

    public static function modificarMesa($id, $id_cliente, $id_mozo, $estado_mesa, $capacidad, $cuenta)
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

    public static function modificarMesaEstado($id, $estado_mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas 
                                                    SET 
                                                        estado_mesa = :estado_mesa,                                                  
                                                    WHERE id = :id");
        //$consulta->bindValue(':estado_mesa', $estado_mesa, PDO::PARAM_STR);                
        $consulta->bindValue(':estado_mesa', $estado_mesa, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function actualizarEstadoMesa($id, $estado_mesa)
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
        $consulta->bindValue(':estado_mesa', "INACTIVO", PDO::PARAM_STR);
        $consulta->execute();
    }

    //crear consumosmesa, (revisa los productos, cantidad, y calcula la cuenta)
    //saldar la cuenta = la mesa queda pagada
    //cargar cuenta mesa

}