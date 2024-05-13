<?php

$ruta = file_exists("./config/db.php") ? "./" : "../";
$file =  $ruta . "config/db.php";

require_once $file;

class Objeto {

    public $id;

    public function __construct($id = null)
    {
        $this->id = $id;
        $this->load();
    }

    // Cargamos toda la info del objeto de la DB
    public function load(){

        $obj = $this->read();
        $vars = $this->getAtts();

        // Si existe el obj en la BD cargamos valores
        if(isset($obj)){
            foreach($vars as $name => $val){
                // Si existe el valor de la bd actuamos
                if(isset($obj[$name])){
                    if($name != 'id'){
                        $this->{$name} = $obj[$name];
                    }
                }
            }
        }
    }

    // Añade este objeto con sus propiedades a la bd.
    public function add(): bool{

        // Si ya existe un objeto como este en la bd salimos
        if($this->read() !== null) return false;

        $conexion = Db::conexion();
        $tabla = strtolower(get_class($this));
        $vars = $this->getAtts();
        $prop = []; // Array de las columnas que vamos a insertar
        $etiquetas = []; // Etiquetas de las consultas preparadas
        $datos = []; // Array de datos para las consultas preparadas

        $sql = "INSERT INTO {$tabla}(";

        // Guardamos usando PDO toda la info
        foreach($vars as $i => $v){
            if(!class_exists($i) && ($i != 'id' || ($i == 'id' && isset($v)))){ // Si la propiedad no es un objeto
                $prop[] = $i == 'id' ? "{$i}_{$tabla}" : $i;
                $etiquetas[] = ":".$i;
                $datos[":{$i}"] = $v;
            }
        }
        $sql .= implode(',', $prop);
        $sql .= ') VALUES (' . implode(',', $etiquetas) . ");";

        try {
            $sentencia = $conexion->prepare($sql);
            $res = $sentencia->execute($datos);
            if($res){
                $this->id = $conexion->lastInsertId();
            }
            return $res;
            // $resultado = $sentencia->execute($datos);
            // return ($resultado == true) ? $pieza["numpieza"] : null;
        } catch (Exception $e) {
            return false;
        }
    }

    // Guarda los cambios de un objeto que ya exista en la bd
    public function save(){

        // Si no existe un objeto como este en la bd salimos
        if($this->read() === null) return false;
        
        $conexion = Db::conexion();
        $tabla = strtolower(get_class($this));
        $vars = $this->getAtts();
        $campos = []; // Campos que serán modificados
        $datos = []; // Array de datos para las consultas preparadas

        $sql = "UPDATE {$tabla} SET ";

        // Recorremos todos los atributos del objeto
        foreach($vars as $i => $v){
            if(!class_exists($i)){ // Si la propiedad no es un objeto
                if($i != 'id'){
                    $campos[] = "{$i} = :{$i}";
                }
                $datos[":{$i}"] = $v;
            }
        }
        $sql .= implode(',', $campos);
        $sql .= " WHERE id_{$tabla} = :id";

        try {
            $sentencia = $conexion->prepare($sql);
            return $sentencia->execute($datos);
            // $resultado = $sentencia->execute($datos);
            // return ($resultado == true) ? $pieza["numpieza"] : null;
        } catch (Exception $e) {
            // throw new Exception($e->getMessage());
            return false;
        }
    }

    // Lee todas las propiedades de un objeto de la BD o null si no existe o falla.
    public function read($id = null): ?array{

        $conexion = Db::conexion();
        $tabla = strtolower(get_class($this));

        $sql = "SELECT * FROM {$tabla} WHERE id_{$tabla}=:id";

        $sentencia = $conexion->prepare($sql);
        $datos = [":id" => $id ?? $this->id];
        $resultado = $sentencia->execute($datos);
        // True si la consulta se ejecuta correctamente
        // Pero no quiere decir que hayan resultados
        if (!$resultado) return null;
        //como sólo va a devolver un resultado uso fetch
        $arr = $sentencia->fetch(PDO::FETCH_ASSOC);
        //fetch duevelve el objeto standar o false si no hay resultados
        return ($arr == false) ? null : $arr;
    }

    // Obtiene todos los elementos de la tabla
    public function list(){

        $conexion = Db::conexion();
        $tabla = strtolower(get_class($this));

        $sentencia = $conexion->query("SELECT * FROM {$tabla};");
        //usamos método query
        $arr = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        //fetch duevelve el objeto standar o false si no hay resultados
        return $arr;
    }

    public function delete($id = null){

        $conexion = Db::conexion();
        $tabla = strtolower(get_class($this));

        $target_id = (int) ($id ?? $this->id);

        $sql = "DELETE FROM {$tabla} WHERE id_{$tabla} = {$target_id}";

        try {
            $sentencia = $conexion->prepare($sql);
            //devuelve true si se borra correctamente
            //false si falla el borrado
            // Pero, si el id existe el borrado es correcto
            // Pero no borra
            $resultado = $sentencia->execute();
            // Si no ha borrado nada considero borrado error
            return ($sentencia->rowCount() <= 0) ? false : true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Obtiene las propiedades de un objeto de una clase
    public function getAtts(){
        return get_object_vars($this);
    }
}

?>