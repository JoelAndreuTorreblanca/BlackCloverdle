<?php

// http://joelandreu.es/BlackCloverdle/config/cron.php

require_once "./../classes/Objeto.php";
require_once "./../classes/personaje.php";
require_once "./../classes/Game_Mode.php";
require_once "./../classes/winner.php";

$w = new Winner();
$w->setWinners([1]);

die;