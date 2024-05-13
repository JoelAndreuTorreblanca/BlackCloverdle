<?php

class db
{
    const HOST = "localhost";
    const DBNAME = "blackcloverdle2";
    const USER = "root";
    const PASSWORD = "";

    static public function conexion(){

        $conexion = null;

        try {

            $opciones =  [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_CASE    => PDO::CASE_LOWER
            ];
            $conexion = new PDO('mysql:host=' . self::HOST . ';  dbname=' . self::DBNAME, self::USER, self::PASSWORD, $opciones);
        } catch (Exception $e) {

            echo "Ocurrió algo con la base de datos: " . $e->getMessage();
        }

        return $conexion; //Es un objeto de conexion PDO
    }

    static public function desconexion(){

        $conexion = null;
        return $conexion; //Es un objeto de conexion PDO
    }
}
