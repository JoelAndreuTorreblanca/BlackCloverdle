<?php
    // En este require_once está el session_start()
    // require_once "assets/php/controlSesiones.php";
    require_once "./config/defines.inc.php";
    // Clases
    require_once "./classes/Objeto.php";
    require_once "./classes/personaje.php";
    require_once "./classes/Game_Mode.php";
    require_once "./classes/winner.php";
    require_once "./classes/user.php";
    //Controllers
    require_once "./controllers/Controller.php";
    require_once "./controllers/ClassicController.php";
    require_once "./controllers/InfinitoController.php";

    // Gestionamos la llamada ajax
    if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == true){

        if(isset($_REQUEST['controller'])){
            $controller = $_REQUEST['controller'] . "Controller";
            $c = new $controller();
        }else{
            $c = new Controller();
        }
        $c->init();
        die;
    }

    $paginasValidas = ["clasico", "infinito"];

    $pagina = "clasico";
    // Almacena la pag que se mostrará, por defecto:
    $contenido = PAGES_DIR . "/clasico.php";
    $tituloPestanya = "BlackCloverdle - Clásico";
    // $js_global = JS_DIR . "global.js";
    $ruta_js = JS_DIR . "clasico.js";
    $css = "";

    // Si 'pagina' estan definidas en la url
    if (isset($_REQUEST["pagina"])) {

        $pagina = $_REQUEST["pagina"];
        $ruta_js = "";

        if (!in_array($pagina, $paginasValidas)) {

            $contenido = PAGES_DIR . "404.php";
            $tituloPestanya = "Página no encontrada";
            $pagina = "notfound";
            $ruta_js = JS_DIR . "{$pagina}.js";
            
        // Si la página es una de las válidas
        } else {
            $tituloPestanya = ucwords("BlackCloverdle - " . $pagina);

            switch ($pagina) {
                case "clasico":
                    $contenido = PAGES_DIR . "{$pagina}.php";
                    $ruta_js = JS_DIR . "{$pagina}.js";
                    $tituloPestanya = "BlackCloverdle - Clásico";
                    break;
                case "atributos":
                    // No implementado
                    $contenido = PAGES_DIR . "{$pagina}.php";
                    $ruta_js = JS_DIR . "{$pagina}.js";
                    break;
                case "infinito":
                    $contenido = PAGES_DIR . "{$pagina}.php";
                    $ruta_js = JS_DIR . "{$pagina}.js";
                    break;
                case "login":
                    // No implementado
                    $contenido = PAGES_DIR . "{$pagina}.php";
                    $ruta_js = JS_DIR . "{$pagina}.js";
                    break;
                default:
                    $contenido = PAGES_DIR . "404.php";
                    $tituloPestanya = "Pagina No encontrada";
                    $pagina = "notfound";
                    $ruta_js = JS_DIR . "{$pagina}.js";
                    break;
            }
        }
    }

    require_once VIEWS_DIR . "layout.php";