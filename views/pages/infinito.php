<?php
    /*
    * Este modo de juego es infinito, el personaje se guarda mediante localhost
    * En este juego, el winner es para cada usuario, así que no se guardará en al DB
    */

    $winner = new Winner();
    $infinitoWinner = $winner->getRandomPersonaje();
    $personaje = new Personaje($infinitoWinner);

    // Calculamos el ancho que tendrán las letras
    $letras = strlen($personaje->name);

    echo "<style>";
    echo ".tablero .tablero__fila{
        grid-template-columns: repeat({$letras}, minmax(28px, 50px));
    }";
    echo "</style>";
?>

<input type="hidden" name="controller" id="controller" value="Infinito"/>
<input type="hidden" name="id_winner" id="id_winner" value="<?= $infinitoWinner ?>"/>
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