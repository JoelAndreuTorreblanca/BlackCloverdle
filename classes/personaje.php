<?php

class personaje extends Objeto{
    public $name;
    public $last_name;
    public $description;
    public $image;
    public $clasico;

    static $db;

    public function __construct($id = null){
        self::$db = db::conexion();
        parent::__construct($id);
    }

    /**
     * Busca un personaje en la base de datos según el campo.
     *
     * @param string $columna Campo en el que buscar.
     * @param string $valor Valor a buscar.
     * @param bool $clasico Opcional. Busca el que sea apto para el modo clásico.
     * @return array
     */
    static function getCharBy($columna, $valor, $clasico = false)
    {
        if(is_null(self::$db)) self::$db = db::conexion();

        $sql = "SELECT * FROM personaje WHERE $columna = :val";

        if ($clasico) {
            $sql .= " AND clasico = TRUE";
        }
        $sql .= ";";

        $datos = [
            ":val" => $valor,
        ];

        try {

            $sentencia = self::$db->prepare($sql);
            $resultado = $sentencia->execute($datos);
            // Devuelve true si la consulta se ejecuta correctamente
            // Eso no quiere decir que existan resultados
            if (!$resultado) return null;
            $char = $sentencia->fetch(PDO::FETCH_ASSOC);
            //fetch devuelve resultado o false si no existe el char
            return ($char == false) ? null : $char;
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "<br/>";
            return false;
        }
    }

    /**
     * Busca todos los characters en la base de datos según el campo.
     *
     * @param string $columna Campo en el que buscar.
     * @param string $valor Valor a buscar.
     * @param bool $clasico Opcional. Busca los que sean aptos para el modo clásico.
     * @return array|null
     */
    static function getAllCharsBy($columna, $valor, $clasico = false)
    {
        if(is_null(self::$db)) self::$db = db::conexion();

        $sql = "SELECT * FROM personaje WHERE $columna = :val";

        if ($clasico) {
            $sql .= " AND clasico = TRUE";
        }
        $sql .= ";";

        $datos = [
            ":val" => $valor,
        ];

        try {

            $sentencia = self::$db->prepare($sql);
            $resultado = $sentencia->execute($datos);
            // Devuelve true si la consulta se ejecuta correctamente
            // Eso no quiere decir que existan resultados
            if (!$resultado) return null;
            $character = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            //fetch devuelve resultado o false si no existe el character
            return ($character == false) ? null : $character;
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "<br/>";
            return false;
        }
    }

    /**
     * Devuelve todos los personajes.
     *
     * @return array Todos los personajes.
     */
    static function getAll()
    {
        try {
            $sentencia = self::$db->query("SELECT * FROM personaje;");
            $characters = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            return $characters;
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "<br/>";
            return false;
        }
    }
}