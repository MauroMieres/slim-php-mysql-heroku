<?php

class Usuario
{
    public $id;
    public $nombre;
    public $apellido;
    public $clave;
    public $tipo_usuario;
    public $baja;
    public $mail;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (nombre,apellido,clave,tipo_usuario,baja,mail) 
                                                                     VALUES (:nombre,:apellido,:clave,:tipo_usuario,:baja,:mail)");
        
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);

        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':clave',$claveHash, PDO::PARAM_STR);
        $consulta->bindValue(':tipo_usuario', $this->tipo_usuario, PDO::PARAM_STR);
        $consulta->bindValue(':baja',"ACTIVO", PDO::PARAM_STR);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,nombre,apellido,tipo_usuario,mail
                                                        FROM usuarios 
                                                        WHERE baja = 'ACTIVO' ");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerEstadoEmpleados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre,apellido,baja AS estado
                                                        FROM usuarios 
                                                        WHERE tipo_usuario = 'MOZO' OR
                                                              tipo_usuario = 'BARTENDER' OR
                                                              tipo_usuario = 'COCINERO' OR
                                                              tipo_usuario = 'CERVECERO'
                                                        ");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerUsuario($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,nombre,apellido,tipo_usuario,mail
                                                        FROM usuarios 
                                                        WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function modificarUsuario($id,$nombre,$apellido,$clave,$tipo_usuario,$baja,$mail)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios 
                                                    SET nombre = :nombre,
                                                        apellido = :apellido,
                                                        clave = :clave,
                                                        tipo_usuario = :tipo_usuario,
                                                        baja = :baja,
                                                        mail = :mail
                                                    WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->bindValue(':tipo_usuario', $tipo_usuario, PDO::PARAM_STR);
        $consulta->bindValue(':baja', $baja, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->execute();
    }

    //de esta forma el borrar usuario es una baja logica, y no una baja permanente
    public static function borrarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET baja = :baja WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':baja',"INACTIVO", PDO::PARAM_STR);
        $consulta->execute();
    }

    
    public static function obtenerUsuarioPorMail($mail)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT *
                                                        FROM usuarios 
                                                        WHERE mail = :mail
                                                        ");
        $consulta->bindValue(':mail',$mail, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerClientePorMail($mail)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT *
                                                        FROM usuarios 
                                                        WHERE mail = :mail
                                                        AND tipo_usuario = 'CLIENTE'
                                                        ");
        $consulta->bindValue(':mail',$mail, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Usuario');
    }

}