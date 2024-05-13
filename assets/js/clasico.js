import { controller } from "./vars.js";
import { allowedLetters, specialLetters } from "./letters.js";
import * as tools from "./tools.js";

const $tablero = document.querySelector("#tablero");
const $tecladoMovil = document.querySelector("#teclado_mobile");

let iniciamosEventos = initGame();

const $input = document.querySelector("#input_try");
const $teclas = document.querySelectorAll(".teclado__tecla");
const $results = document.querySelector("#results");
const $status = document.querySelector("#status");
const $notifications = document.querySelector("#notifications");
const $menu_mobile = document.querySelector("#menu_mobile");
const $btn_aviso = document.querySelector("#aviso_no_cookies .btn__aviso");

// Stats
const $showStats = document.querySelector("#show-stats");
const $statsContainer = document.querySelector("#stats");
const $juegos_jugados = document.querySelector("#juegos_jugados");
const $juegos_ganados = document.querySelector("#juegos_ganados");
const $tasa_victoria = document.querySelector("#tasa_victoria");
const $mejor_intento = document.querySelector("#mejor_intento");
const $racha_actual = document.querySelector("#racha_actual");
const $mejor_racha = document.querySelector("#mejor_racha");
const $stats_bg = document.querySelector("#stats + .stats__bg");
const $close_stats = document.querySelector("#stats .stats__close");
const $ver_juego = document.querySelector("#stats .ver_juego");

// How to play
const $showHowtoplay = document.querySelector("#show-howtoplay");
const $howtoplayContainer = document.querySelector("#how_to_play");
const $close_howtoplay = document.querySelector("#how_to_play .howtoplay__close");
const $howtoplay_bg = document.querySelector("#how_to_play + .howtoplay__bg");

initGeneralEvents();
if(iniciamosEventos){
    initEvents();
}

function initGame(){
    if(!tools.isAvisoAccepted()) tools.toggleAvisoNoCookies();

    let partida_finalizada = localStorage.getItem("partida_finalizada");
    let partida_nuevo_dia = localStorage.getItem("partida_nuevo_dia");
    const hoy = tools.today();

    // Si null, es la primera partida histórica, seteamos la fecha de hoy e iniciamos juego
    // Si no es null y además es diferente de hoy -> es la primera partida de hoy, seteamos fecha de hoy e iniciamos
    // Si se cumple una de las dos, iniciamos sí o sí
    if(partida_nuevo_dia === null || partida_nuevo_dia !== hoy){
        localStorage.setItem("partida_nuevo_dia", hoy);
        localStorage.setItem("partida_finalizada", "false");
        document.querySelector(".tablero__fila").classList.add("active");
        localStorage.removeItem("game_content");
        localStorage.removeItem("teclado_movil");
    
    // Si null, es la primera partida y seteamos la variable a false
    }else if(partida_finalizada === null){
        localStorage.setItem("partida_finalizada", "false");
        document.querySelector(".tablero__fila").classList.add("active");

    // O bien, si es true, la partida anterior ha sido finalizada y no iniciamos el juego
    }else if(partida_finalizada === "true"){
        loadContent();
        return false;

    // En el resto de casos la partida no ha sido finalizada y cargamos los datos
    }else{
        loadContent();

        // Si se ha recargado la página sin haber hecho un intento, es necesario poner la clase active
        const content = localStorage.getItem("game_content");
        const teclado_movil = localStorage.getItem("teclado_movil");
        if(content === null && teclado_movil === null){
            document.querySelector(".tablero__fila").classList.add("active");
        }
    }

    return true;
}

function initGeneralEvents(){
    $stats_bg.addEventListener("click", toggleStats);
    $close_stats.addEventListener("click", toggleStats);
    $ver_juego.addEventListener("click", () => {
        $tablero.style.display = "block";
        $tecladoMovil.style.display = "";
        $results.style.display = "";
    });
    $showStats.addEventListener("click", () => {
        updateGuestStats(false, null, true);
        showStats();
        toggleStats();
    });

    $btn_aviso.addEventListener("click", function(){
        localStorage.setItem("leido_aviso_nocookies", "true");
        tools.toggleAvisoNoCookies();
    });

    $menu_mobile.addEventListener("click", function(){
        let $menu = document.querySelector("#menu");

        if(this.classList.contains("open")){
            $menu.style.height = "0px";
        }else{
            $menu.style.height = $menu.scrollHeight + "px";
        }
        this.classList.toggle("open");
    });

    $howtoplay_bg.addEventListener("click", toggleHowToPlay);
    $close_howtoplay.addEventListener("click", toggleHowToPlay);
    $showHowtoplay.addEventListener("click", toggleHowToPlay);
}

function initEvents(){

    document.addEventListener("keydown", (event) => {
        if(!tools.isAvisoAccepted()) return;
        // Si el juego ha terminado salimos
        if($status.dataset.end == "true") return;
        $input.focus();
        onKeydown(event);
    });

    $teclas.forEach((item) => {
        item.addEventListener("click", function(event){
            if(!tools.isAvisoAccepted()) return;
            // Si el juego ha terminado salimos
            if($status.dataset.end == "true") return;
            handleKeyboardClick(this.dataset.tecla);
        });
    });
}

// Escribir con el teclado físico
function onKeydown(event){
    const { key } = event;

    if(key === "Enter"){
        // Hacemos que no se puedan enviar 2 intentos si pulsamos rápido enter
        if($input.dataset.enabled !== "false"){
            sendTry(() => {
                $input.dataset.enabled = "true";
            });
        }
        return;
    }
    // Con teclas especiales como F5 no hacemos nada
    if(specialLetters.includes(key)) return;

    if(allowedLetters.includes(key)){
        // Si ctrl + backspace => borramos toda la fila del tablero
        if((key === "Backspace" && event.ctrlKey)){
            document.querySelectorAll(".tablero__fila.active .tablero__letra").forEach((celda) => {
                celda.querySelector(".letra").innerText = "";
                celda.classList.add("empty");
            });

        // Permitimos la combinación ctrl + shift + m
        }else if((key === "M" || key === "m") && event.ctrlKey && event.shiftKey){
            return;

        // Si se ha pulsado ctrl o alt, prevenimos la escritura
        }else if(event.ctrlKey || event.altKey){
            event.preventDefault();
            return;
        }

        // Poner letra en el tablero
        setLetter(key);
    }else{
        event.preventDefault();
    }
}

// Escribir en el teclado de la pantalla
function handleKeyboardClick(letra){
    let value = "";

    if(letra === "Backspace"){
        value = $input.value.slice(0, -1);
    }else if(letra === "Enter"){
        // Hacemos que no se puedan enviar 2 intentos si pulsamos rápido enter
        if($input.dataset.enabled !== "false"){
            sendTry(() => {
                $input.dataset.enabled = "true";
            });
        }
        return;
    }else{
        const max = $input.dataset.maxlength;
        if($input.value.length >= max) return;
        value = $input.value + letra;
    }
    $input.value = value;
    setLetter(letra);
}

// Establece las letras en las casillas
function setLetter(letra){

    clearAnimations(); // Paramos la animación de parpadeo
    let $vacia = document.querySelector(".tablero__body .tablero__fila.active .tablero__letra.empty");

    if($vacia === null && letra === "Backspace"){

        $vacia = document.querySelector(".tablero__body .tablero__fila.active .tablero__letra:last-child");
        $vacia.querySelector(".letra").innerText = "";
        $vacia.classList.add("empty");

    }else if(letra === "Backspace"){

        $vacia = $vacia.previousElementSibling;
        // Si en este punto es null es porque estamos en la primera letra de la fila y no necesitamos borrar. Salimos.
        if($vacia === null) return;
        $vacia.querySelector(".letra").innerText = "";
        $vacia.classList.add("empty");

    }else{
        // Si en este punto es null es porque estamos en la última letra de la fila y no necesitamos añadir. Salimos.
        if($vacia === null) return;
        $vacia.querySelector(".letra").innerText = letra;
        $vacia.classList.remove("empty");
    }
}

function sendTry(unblockInput){
    $input.dataset.enabled = "false";

    if(!tools.isValidTry($input)){
        tools.displayError("La palabra introducida es demasiado corta", $notifications);
        unblockInput();
        return;
    }

    let datos = new FormData();
    let prefixController = document.getElementById("controller").value;
    let n_try = document.querySelector(".tablero__fila.active").dataset.fila;

    datos.append("ajax", true);
    datos.append("action", "processTry");
    datos.append("try", $input.value);
    datos.append("n_try", n_try);
    datos.append("controller", prefixController);

    const myInit = {
        method: 'POST',
        mode: 'cors',
        cache: 'no-cache',
        body: datos,
    }; // body data debe coincidir con "Content-Type" del header

    let peticion = new Request(controller, myInit);
    
    fetch(peticion)
    .then(promesa => promesa.json())
    .then(function (datos) {

        tools.clearNotifications($notifications);
        
        if(datos.error){
            tools.displayError(datos.data, $notifications);
        }else{
            handleTryResult(datos.data);
        }
        unblockInput();
    })
    .catch(function (error) {
        console.log(error);
    });
}

function handleTryResult(results){
    const $fila = document.querySelector(".tablero__fila.active");
    const $letras = $fila.querySelectorAll(".tablero__letra");

    let counter = 0;
    let coloresTeclado = [];
    let inputValue = $input.value;

    // Insertamos las letras para evitar un bug que ocurría.
    // El bug consiste en pegar el nombre de un personaje con ctrl + v
    // Eso generaba que no se escriba la palabra en el tablero pero sí es posible enviarla como intento
    // Se elige esta solución porque puede ayudar en posibles futuros errores
    $letras.forEach(function(el, ind){
        el.querySelector(".letra").innerText = inputValue.charAt(ind);
    });

    for(let i in results.letters){
        let clase = results.letters[i];
        let dataLetra = $letras[i].querySelector(".letra").innerText;
        coloresTeclado.push({l: dataLetra, clase: clase});

        setTimeout(function(){
            $letras[i].classList.add(clase);

            let resultsLength = Object.keys(results.letters);
            if(i == resultsLength.length - 1){
                saveContent();
            }
        },100 * counter);
        
        counter++;
    }

    showResultsInKeyboard(coloresTeclado);

    let $nextFila = $fila.nextElementSibling;
    // Si no quedan filas o hemos ganado
    if($nextFila === null || results.win){

        setTimeout(function(){
            saveContent();
            gameOver(results.win, results.answer);
        }, 200 * counter);
        return;
    }

    $nextFila.classList.add("active");
    $fila.classList.remove("active");

    $input.value = "";
}

function gameOver(win, answer = null){
    $input.value = "";

    // if(logged){
        // Actualizar stats usuario
    // }else{
        // Actualizar stats invitado
        let $filas = document.querySelectorAll(".tablero__fila");
        let intento = -1;

        for (let i = 0; i < $filas.length; i++) {
            if ($filas[i].classList.contains("active")) {
                intento = i + 1;
                break;
            }
        }

        // Marcamos como que ha finalizado la partida
        localStorage.setItem("partida_finalizada", "true");

        updateGuestStats(win, intento);
    // }

    $status.dataset.end = "true";
    $tablero.style.display = "none";
    $tecladoMovil.style.display = "none";
    $results.style.display = "block";
    showStats();
    if(win){
        addOneGuess()
        if(answer == null){
            tools.displaySuccess("¡¡Has ganado!!", $notifications);
        }else{
            tools.displaySuccess(`¡¡Has ganado!!<br/><a href='https://google.com/search?q=${answer.replace(/ /g, "+")}+black+clover' target='_blank'>Click para más info sobre ${answer}</a>`, $notifications);
        }
    }else{
        if(answer == null){
            tools.displayError("Has perdido :(", $notifications);
        }else{
            tools.displayError(`Has perdido :( <br/> El personaje era: <strong>${answer}</strong>. <a href='https://google.com/search?q=${answer.replace(/ /g, "+")}+black+clover' target='_blank'>Click para más info.</a>`, $notifications);
        }
    }
}

// Cambia los colores de los teclados del juego para mostrar los resultados
function showResultsInKeyboard(datos){
    datos.forEach((item) => {
        
        let letter = item.l;
        let $teclas = document.querySelectorAll(`.teclado__tecla[data-tecla='${letter}']`);

        $teclas.forEach(function(el, i){
            if(el.classList.contains("grey")){
                el.classList.remove("grey");
            }
            el.classList.add(item.clase);
        })
    });
}

function addOneGuess(){

    let datos = new FormData();
    let prefixController = document.getElementById("controller").value;
    let id_winner = document.getElementById("id_winner").value;

    datos.append("ajax", true);
    datos.append("action", "addOneGuess");
    datos.append("controller", prefixController);
    datos.append("id_winner", id_winner);

    const myInit = {
        method: 'POST',
        mode: 'cors',
        cache: 'no-cache',
        body: datos,
    }; // body data debe coincidir con "Content-Type" del header

    let peticion = new Request(controller, myInit);
    
    fetch(peticion)
    .then(promesa => promesa.json())
    .then(function (datos) {
        if(datos.error){
            console.error(datos.data);
        }
    })
    .catch(function (error) {
        console.log(error);
    });
}

// Para la animación de parpadeo
function clearAnimations(){
    let elems = [...document.querySelectorAll("*.blink")];
    elems.map((el) => el.classList.remove("blink"));
}

function showStats(){
    let juegos_jugados = localStorage.getItem("juegos_jugados");
    let juegos_ganados = localStorage.getItem("juegos_ganados");
    let mejor_intento = localStorage.getItem("mejor_intento");
    let racha_actual = localStorage.getItem("racha_actual");
    let mejor_racha = localStorage.getItem("mejor_racha");

    let tasa_victorias = ((juegos_ganados * 100) / juegos_jugados) + "";
    let indexPunto = tasa_victorias.indexOf(".");

    if(indexPunto != -1){ // Si hay decimales, dejamos solo 2
        tasa_victorias = tasa_victorias.slice(0, indexPunto + 3);
    }

    $juegos_jugados.innerText = juegos_jugados;
    $juegos_ganados.innerText = juegos_ganados;
    $tasa_victoria.innerText = juegos_jugados > 0 ? tasa_victorias + " %" : "-";
    $mejor_intento.innerText = mejor_intento;
    $racha_actual.innerText = racha_actual;
    $mejor_racha.innerText = mejor_racha;
}

function updateGuestStats(win = false, intento = null, showing = false){
    let juegos_jugados = localStorage.getItem("juegos_jugados");
    let juegos_ganados = localStorage.getItem("juegos_ganados");
    let mejor_intento = localStorage.getItem("mejor_intento");
    let racha_actual = localStorage.getItem("racha_actual");
    let mejor_racha = localStorage.getItem("mejor_racha");

    if(juegos_jugados === null){
        localStorage.setItem("juegos_jugados", intento !== null ? 1 : 0);
    }else if(intento !== null){
        localStorage.setItem("juegos_jugados", parseInt(juegos_jugados) + 1);
    }

    if(juegos_ganados === null){
        localStorage.setItem("juegos_ganados", win ? 1 : 0);
    }else if(win){
        localStorage.setItem("juegos_ganados", parseInt(juegos_ganados) + 1);
    }

    if(mejor_intento === null){
        if(win){
            localStorage.setItem("mejor_intento", intento);
        }else{
            localStorage.setItem("mejor_intento", "-");
        }
    }else{
        if(win && (mejor_intento > intento || mejor_intento == "-")){
            localStorage.setItem("mejor_intento", intento);
        }
    }

    if(racha_actual === null){
        if(showing){
            racha_actual = 0;
        }else{
            racha_actual = win ? 1 : 0;
        }
        localStorage.setItem("racha_actual", racha_actual);
    }else{
        if(!showing){
            racha_actual = win ? parseInt(racha_actual) + 1 : 0;
        }
        localStorage.setItem("racha_actual", racha_actual);
    }

    if(mejor_racha === null || mejor_racha === "null"){
        localStorage.setItem("mejor_racha", win ? racha_actual : 0);
    }else if(racha_actual > mejor_racha){
        localStorage.setItem("mejor_racha", racha_actual);
    }
}

function toggleStats(){
    document.querySelector("body").classList.toggle("showing-modal");
    $results.classList.toggle("showing-stats-modal");
    $statsContainer.classList.toggle("modal");
}

function toggleHowToPlay(){
    document.querySelector("body").classList.toggle("showing-modal");
    $results.classList.toggle("showing-howtoplay-modal");
    $howtoplayContainer.classList.toggle("modal");
}

function loadContent (){
    const content = localStorage.getItem("game_content");
    const teclado_movil = localStorage.getItem("teclado_movil");

    if(content !== null && teclado_movil !== null){
        $tablero.innerHTML = content;
        $tecladoMovil.innerHTML = teclado_movil;

        // Si cargamos contenido, actualizamos número de veces adivinado del personaje
        const $n_guesses = document.querySelector("#n_guesses");
        $n_guesses.innerText = document.getElementById("veces_adivinado").value;
        if(!tools.haGanado()){
            getWinnerGame();
        }
        return true;
    }

    return false;
}

function getWinnerGame(){
    let datos = new FormData();
    let prefixController = document.getElementById("controller").value;
    let id_winner = document.getElementById("id_winner").value;

    datos.append("ajax", true);
    datos.append("action", "getWinnerName");
    datos.append("controller", prefixController);
    datos.append("id_winner", id_winner);

    const myInit = {
        method: 'POST',
        mode: 'cors',
        cache: 'no-cache',
        body: datos,
    }; // body data debe coincidir con "Content-Type" del header

    let peticion = new Request(controller, myInit);
    
    fetch(peticion)
    .then(promesa => promesa.json())
    .then(function (datos) {

        if(datos.error){
            console.error(datos.data);
        }else{
            const $answer = document.querySelector("#answer");
            $answer.querySelector("strong").innerHTML = datos.data.winner_name;
            $answer.classList.remove("hidden");
        }
    })
    .catch(function (error) {
        console.log(error);
    });
}

function saveContent(){
    const content = $tablero.innerHTML;
    localStorage.setItem("game_content", content);
    const teclado_movil = $tecladoMovil.innerHTML;
    localStorage.setItem("teclado_movil", teclado_movil);
}