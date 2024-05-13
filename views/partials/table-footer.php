<div class="tablero__footer">
    <?php if(isset($todayWinner["veces_adivinado"]) && $todayWinner["veces_adivinado"] > 0){ ?>
        <div class="character_guesses">¡<span id="n_guesses" class="n_guesses"><?= $todayWinner["veces_adivinado"] ?></span> jugadores ya lo han adivinado!</div>
    <?php } ?>
    <?php if($pagina === "infinito"){ ?>
        <div class="btn__wrapper">
            <a href="index.php?pagina=infinito" class="hidden reset btn btn__success">Reiniciar</a>
        </div>
    <?php } ?>
    <?php require_once PARTIALS_DIR . "teclado.php"; ?>
</div>