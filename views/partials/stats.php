<div id="stats" class="stats">
    <div class="stats__container">
        <div class="stats__heading modal-heading">Estadísticas🏆</div>
        <div class="stats__wrapper">
            <div class="stat">
                <span class="stat__label">Juegos jugados</span>
                <span id="juegos_jugados" class="stat__data"></span>
            </div>
            <div class="stat">
                <span class="stat__label">Juegos ganados</span>
                <span id="juegos_ganados" class="stat__data"></span>
            </div>
            <div class="stat">
                <span class="stat__label">Tasa de victorias</span>
                <span id="tasa_victoria" class="stat__data"></span>
            </div>
            <div class="stat">
                <span class="stat__label">Mejor intento</span>
                <span id="mejor_intento" class="stat__data"></span>
            </div>
            <div class="stat">
                <span class="stat__label">Racha actual</span>
                <span id="racha_actual" class="stat__data"></span>
            </div>
            <div class="stat">
                <span class="stat__label">Mejor racha</span>
                <span id="mejor_racha" class="stat__data"></span>
            </div>
        </div>
        <div class="stats__footer">
            <div class="stats__close btn btn__primary btn__primary--ghost">Cerrar</div>
            <div class="ver_juego btn btn__primary btn__primary--ghost">Ver juego</div>
            <?php if($pagina === "infinito"){ ?>
                <a href="index.php?pagina=infinito" class="reset btn btn__success">Reiniciar</a>
            <?php } ?>
        </div>
    </div>
</div>
<div class="stats__bg"></div>