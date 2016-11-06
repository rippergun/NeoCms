<?php
ob_start();
session_start();
setlocale(LC_ALL, 'fr_FR');

require_once ('Neo.environnement.php');
require_once ('Neo.clientRouteur.php');
require_once ('Neo.connect.php');

$controleur = new NeoClientRouteur();
$client = $controleur->checkUrl();

if (isset($client['client_path']) && !empty($client['client_path'])) {
    require($client['client_path'] . "/constantes.php");
    foreach ($client as $key => $value) {
        define(strtoupper($key), $value);
    }
    unset($client, $key, $value);
} else {
    echo 'no such site<hr>';
    if ($_SERVER['REMOTE_ADDR'] == $_SERVER['LOCAL_ADDR']) {
        echo 'admin mode<hr>';
        require('install/install.php');
    }

    exit;
}

require(PATH_CORE_INCLUDE.'/core.constantes.php');
?>