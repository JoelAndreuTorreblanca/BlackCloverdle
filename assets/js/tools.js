export function displayError(errs, $node){
    clearNotifications($node);

    let msg = document.createElement("div");
    msg.className = "alert alert__danger";
    msg.innerHTML = errs;

    msg.addEventListener("click", function(event){
        if(event.target.nodeName == "A") return;
        this.remove();
    });

    $node.appendChild(msg);

    setTimeout(function(){
        msg.classList.add("open");

        setTimeout(function(){
            msg.style.whiteSpace = "wrap";
        }, 300);
    }, 200);
}

export function displaySuccess(succ, $node){
    clearNotifications($node);

    let msg = document.createElement("div");
    msg.className = "alert alert__success";
    msg.innerHTML = succ;

    msg.addEventListener("click", function(event){
        if(event.target.nodeName == "A") return;
        this.remove();
    });

    $node.appendChild(msg);

    setTimeout(function(){
        msg.classList.add("open");
    }, 200);
}

export function clearNotifications($notifications){
    $notifications.innerHTML = "";
}

export function today(){
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    
    today = mm + '/' + dd + '/' + yyyy;
    return today;
}

// Función que comprueba si es posible enviar la palabra o no según su longitud
export function isValidTry($input){
    const value = $input.value;
    const min = $input.dataset.minlength;
    const max = $input.dataset.maxlength;

    if(value.length < min){
        let $vacias = document.querySelectorAll(".tablero__body .tablero__fila.active .tablero__letra.empty");
        $vacias.forEach((celda) => {
            celda.classList.remove("blink");
            celda.classList.add("blink");
        });

        return false;
    }else if(value.length > max){
        return false;
    }

    return true;
}

export function isAvisoAccepted(){
    let leido_aviso_nocookies = localStorage.getItem("leido_aviso_nocookies");
    return leido_aviso_nocookies === null || leido_aviso_nocookies === "false" ? false : true;
}

export function toggleAvisoNoCookies(){
    document.querySelector("#aviso_no_cookies").classList.toggle("show");
    document.querySelector("body").classList.toggle("showing-modal");
};

export function haGanado(){
    let lastFila = document.querySelector(".tablero__fila:last-child");
    // Si la última fila no tiene la clase active, es ganada
    if(!lastFila.classList.contains("active")){
        return true;
    }

    let letras = lastFila.querySelectorAll(".tablero__letra").length;
    let letrasVerdes = lastFila.querySelectorAll(".tablero__letra.green").length;
    // Si todas las letras son verdes, es ganada
    return letras == letrasVerdes;
}