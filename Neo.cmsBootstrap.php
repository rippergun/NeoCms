<?php
require ('Neo.environnement.php');
require ('Neo.clientRouteur.php');
require_once ('Neo.connect.php');

$controleur = new NeoClientRouteur();
$client     = $controleur->checkUrl();

if (isset($client['client_path']) && !empty($client['client_path'])) {
    require($client['client_path'] . "/constantes.php");
    unset($client);
} else {
    echo 'no such site<hr>';
    if ($_SERVER['REMOTE_ADDR'] == $_SERVER['LOCAL_ADDR']) {
        echo 'admin mode<hr>';
        require('install/install.php');
    }

    exit;
}

require('classes/core.constantes.php');
?>