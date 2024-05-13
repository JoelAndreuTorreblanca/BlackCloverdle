<?php

class ClassicController extends Controller{

    private $game_mode = 1;

    public function __construct(){
        parent::__construct();
    }

    public function processTry(){
        // Parent ya comprueba si existe el parámetro en la petición y lo devuelve
        $triedCharacter = parent::processTry();
        $triedName = strtolower($triedCharacter["name"]);

        $winnerInstance = new Winner();
        $winner = $winnerInstance->getTodaysWinner($this->game_mode);

        $pWinner = new Personaje($winner['id_personaje']);
        $wName = strtolower($pWinner->name);
        // Obtenemos colores de las letras
        $response = $this->blackcloverdle($triedName, $wName);
        // Indices de las letras verdes
        $verdes = array_keys($response, "green");
        // Si la longitud del nombre es igual a la cantidad de letras wedes -> win
        $win = strlen($wName) == count($verdes);
        $response = ["letters" => $response];
        $response["win"] = $win;
        // Si ha ganado o si es el sexto intento y no ha ganado, enviamos la solución
        if($win || (isset($_REQUEST['n_try']) && $_REQUEST['n_try'] == 6 && !$win)){
            $response['answer'] = trim($pWinner->name . " " . $pWinner->last_name);
        }

        $this->ajaxResponse(false, $response);
    }
}
