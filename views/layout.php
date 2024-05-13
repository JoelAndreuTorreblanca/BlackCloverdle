<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?= CSS_DIR ?>blackcloverdle.css"/>
        <?php require_once PARTIALS_DIR . "meta.php";?>
        <title><?= $tituloPestanya ?? "BlackCloverdle" ?></title>
    </head>
<body id="<?= $pagina ?>">
    <main>
        <?php require_once PARTIALS_DIR . "header.php"; ?>
        <?php require_once PARTIALS_DIR . "avisonocookies.php"; ?>
        <div class="content-wrapper">
            <div id="notifications"></div>
            <div class="container">
                <!-- begin: contenido -->
                <?php require_once $contenido;?>
                <!-- end: contenido -->
            </div>
        </div>
        <div id="teclado_mobile">
            <?php require PARTIALS_DIR . "teclado.php"; ?>
        </div>
        <?php require_once PARTIALS_DIR . "footer.php"; ?>
    </main>
</body>
</html>