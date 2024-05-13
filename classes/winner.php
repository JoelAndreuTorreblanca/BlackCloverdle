<?php

class winner extends Objeto{
    public $id_game_mode;
    public $id_personaje;
    public $day;
    public $veces_adivinado;

    public $db;

    public function __construct($id = null){
        $this->db = db::conexion();
        parent::__construct($id);
    }

    public function setWinners(Array $modes = []){
        // Establecemos los winners de los modos pasados
        if(count($modes) > 0){
            foreach($modes as $id_mode){
                $last3 = $this->last3Personajes($id_mode);
                $gm = new Game_Mode($id_mode);
                $personajes = [];

                if($gm->name == "clasico"){
                    $personajes = Personaje::getAllCharsBy("clasico", true);
                }elseif($gm->name == "atributos"){
                    $personajes = Personaje::getAll();
                }

                $maxnum = count($personajes) - 1;
                $index = $this->getRandom(0, $maxnum);
                $id_p = $personajes[$index]["id_personaje"];
                // Si el personaje seleccionado se ha jugado en los últimos 3 días, seleccionamos otro
                while(in_array($id_p, $last3)){
                    $index = $this->getRandom(0, $maxnum);
                    $id_p = $personajes[$index]["id_personaje"];
                }
                
                $this->id_game_mode = $id_mode;
                $this->id_personaje = $id_p;
                $this->veces_adivinado = 0;
                $this->day = $this->today();;
                
                $this->add();
                $this->id = NULL;
            }
        }else{
            // Establecemos todos los winners
            $modos = Game_Mode::getAll();

            foreach($modos as $modo){
                $id_mode = $modo["id_game_mode"];
                $last3 = $this->last3Personajes($id_mode);
                $personajes = [];
                
                if($modo["name"] == "clasico"){
                    $personajes = Personaje::getAllCharsBy("clasico", true);
                }elseif($modo["name"] == "atributos"){
                    $personajes = Personaje::getAll();
                }

                $maxnum = count($personajes) - 1;
                $index = $this->getRandom(0, $maxnum);
                $id_p = $personajes[$index]["id_personaje"];
                // Si el personaje seleccionado se ha jugado en los últimos 3 días, seleccionamos otro
                while(in_array($id_p, $last3)){
                    $index = $this->getRandom(0, $maxnum);
                    $id_p = $personajes[$index]["id_personaje"];
                }
                
                $this->id_game_mode = $id_mode;
                $this->id_personaje = $id_p;
                $this->veces_adivinado = 0;
                $this->day = $this->today();

                $this->add();
                $this->id = NULL;
            }
        }
    }

    public function last3Personajes(int $id_game_mode){
        $sql = "SELECT id_personaje FROM winner WHERE id_game_mode = :id ORDER BY day DESC LIMIT 3";

        $datos = [
            ":id" => $id_game_mode,
        ];

        try {

            $sentencia = $this->db->prepare($sql);
            $resultado = $sentencia->execute($datos);
            // Devuelve true si la consulta se ejecuta correctamente
            // Eso no quiere decir que existan resultados
            if (!$resultado) return null;
            $chars = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            //fetch devuelve resultado o false si no existe el char
            if($chars == false) return null;

            // Preparamos el array para devolver únicamente los id's
            $return = [];
            foreach($chars as $char){
                $return[] = $char["id_personaje"];
            }

            return $return;

        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "<br/>";
            return false;
        }
    }

    public function getRandomPersonaje($clasico = true){
        $personajes = $clasico ? Personaje::getAllCharsBy("clasico", true) : Personaje::getAll();
        $index = $this->getRandom(0, count($personajes) - 1);
        return $personajes[$index]["id_personaje"];
    }

    /**
     * Busca el ganador del modo de juego pasado
     *
     * @param integer $modo ID del modo de juego
     * @return array Winner
     */
    public function getTodaysWinner($modo)
    {
        $sql = "SELECT * FROM winner WHERE id_game_mode = :val AND `day` = :hoy;";

        $datos = [
            ":val" => $modo,
            ":hoy" => $this->today(),
        ];

        try {

            $sentencia = $this->db->prepare($sql);
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

    public function today(){
        return date("Y-m-d", time());
    }

    public function getRandom($min, $max) {
        return mt_rand($min, $max);
    }
}