<?php

/*
*  De momento esta clase va a ser solo para gestionar las llamadas ajax
*/

class Controller {

    public $errors;
    private $actions = ["processTry", "addOneGuess", "getWinnerName"];

    public function __construct(){
        $this->errors = [
            "noaction" => "Acción no reconocida.",
            "void_try" => "No se ha enviado ningún personaje.",
            "unknown" => "El personaje introducido no existe.",
        ];
    }

    public function init(){
        // Si no hay action => error
        if(!isset($_REQUEST['action']) || (isset($_REQUEST['action']) && trim($_REQUEST['action']) == "")){
            $this->ajaxResponse(true, [$this->errors["noaction"]]);
        }

        $action = $_REQUEST['action'];

        if(in_array($action, $this->actions)){
            $this->{$action}();
        }else{
            $this->ajaxResponse(true, [$this->errors["noaction"]]);
        }
    }

    public function processTry(){
        // Si no hay try => error
        if(!isset($_REQUEST['try']) || (isset($_REQUEST['try']) && trim($_REQUEST['try']) == "")){
            $this->ajaxResponse(true, [$this->errors["void_try"]]);
        }

        $try = $_REQUEST['try'];
        $p = Personaje::getCharBy("name", $try); // Comprobado: Si no existe, devuelve NULL

        if(!isset($p)){
            $this->ajaxResponse(true, [$this->errors["unknown"]]);
        }else{
            return $p;
        }
    }

    public function addOneGuess(){
        $id_winner = $_REQUEST['id_winner'] ?? null;
        // Si no hay id, error
        if(!isset($id_winner)) $this->ajaxResponse(true);

        $winner = new Winner($id_winner);
        $winner->veces_adivinado = $winner->veces_adivinado + 1;
        $result = $winner->save();

        $this->ajaxResponse(!$result);
    }

    public function getWinnerName(){
        $id_winner = $_REQUEST['id_winner'] ?? null;
        // Si no hay id, error
        if(!isset($id_winner)) $this->ajaxResponse(true);
        $winner = new Winner($id_winner);
        $p = new Personaje($winner->id_personaje);

        $name = $p->last_name != "" ? $p->name . " " . $p->last_name : $p->name;

        $this->ajaxResponse(false, ["winner_name" => $name]);
    }

    public function ajaxResponse(bool $error = false, array $data = []){
        $response = [
            "error" => $error,
            "data" => $data,
        ];

        echo json_encode($response, JSON_OBJECT_AS_ARRAY);
        die;
    }

    public function blackcloverdle($guess, $solution) {
        $splitSolution = str_split($solution);
        $splitGuess = str_split($guess);
    
        $solutionCharsTaken = array_map(function($v) {
            return false;
        }, $splitSolution);
    
        $statuses = [];
    
        /*
        Green Cases
        */
        foreach($splitGuess as $i => $letter) {
            if($letter === $splitSolution[$i]) {
                $statuses[$i] = 'green';
                $solutionCharsTaken[$i] = true;
                continue ;
            }
        }
    
        /*
        Grey Cases
        */
        foreach($splitGuess as $i => $letter) {
            
            if(isset($statuses[$i])) continue;
    
            if(!in_array($letter, $splitSolution)) {
                $statuses[$i] = 'grey';
                continue;
            }
    
            /*
            Red Cases
            */
            $indexOfPresentChar = !$solutionCharsTaken[array_search($letter, $splitSolution)] ? array_search($letter, $splitSolution) : -1;
    
            if($indexOfPresentChar > -1) {
                $statuses[$i] = 'red';
                $solutionCharsTaken[$indexOfPresentChar] = true;
                continue;
            } else {
                $statuses[$i] = 'grey';
                continue;
            }
        }
    
        return $statuses;
    }
}