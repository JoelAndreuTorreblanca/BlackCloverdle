<?php
    /*
    * Este modo de juego es el más simple, lo único
    * que se necesita es cargar de la BBDD el personaje
    * del día
    */

    $winner = new Winner();
    // $winner->setWinners([1]);
    $todayWinner = $winner->getTodaysWinner(1);
    $personaje = new Personaje($todayWinner["id_personaje"]);

    // Calculamos el ancho que tendrán las letras
    $letras = strlen($personaje->name);

    echo "<style>";
    echo ".tablero .tablero__fila{
        grid-template-columns: repeat({$letras}, minmax(28px, 50px));
    }";
    echo "</style>";
?>

<input type="hidden" name="controller" id="controller" value="Classic"/>
<input type="hidden" name="id_winner" id="id_winner" value="<?= $todayWinner["id_winner"] ?>"/>
<input type="hidden" name="veces_adivinado" id="veces_adivinado" value="<?= $todayWinner["veces_adivinado"] ?>"/>
<div id="tablero" class="tablero">
    <input type="text" name="input_try" id="input_try" data-minlength="<?= $letras ?>" data-maxlength="<?= $letras ?>" maxlength="<?= $letras ?>">
    <?php require_once PARTIALS_DIR . "table-head.php"; ?>
    <div class="tablero__body">
        <?php
            for($i = 0; $i < 6; $i ++){
                $n = $i + 1;
                echo "<div class='tablero__fila' data-fila='{$n}'>";

                for($j = 0; $j < $letras; $j ++){
                    echo "<div class='tablero__letra empty'>
                            <div class='tablero__letra--container'>
                                <span class='letra'></span>
                            </div>
                          </div>";
                }

                echo "</div>";
            }
        ?>
    </div>
    <?php require_once PARTIALS_DIR . "table-footer.php"; ?>
</div>
<div id="results">
    <div id="status" data-end="false" style="display: none;"></div>
    <?php require_once PARTIALS_DIR . "stats.php"; ?>
    <?php require_once PARTIALS_DIR . "how-to-play/how-to-{$pagina}.php"; ?>
</div>