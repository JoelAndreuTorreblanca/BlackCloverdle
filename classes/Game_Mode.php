<?php

class Game_Mode extends Objeto{
    public $name;
    public $db;

    public function __construct($id = null){
        $this->db = db::conexion();
        parent::__construct($id);
    }

    /**
     * Devuelve todos los modos de juego.
     *
     * @return array Todos los modos de juego.
     */
    static function getAll(){
        try {

            $sentencia = self::$db->query("SELECT * FROM game_mode;");
            $gamemodes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            return $gamemodes;
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "<br/>";
            return false;
        }
    }
}