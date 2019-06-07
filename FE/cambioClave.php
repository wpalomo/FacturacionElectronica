<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, X-EXAMPLE-HEADER, authorization');

include_once 'librerias/ClaseCambioClave.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

switch ($action) {
    case 'cambioClave':
        cambioClave();
        break;
}

/* * *****
  $objetoCambioClave = new ClaseCambioClave(1, 'mdqAl57L3w', 'mdqAl57L3w');
  $result = $objetoCambioClave->cambioClave();

  $data = ClaseJson::getJson($result);

  echo $data;
  /****** */

function cambioClave() {
    if (isset($_POST['id_usuario']) && isset($_POST['login']) && isset($_POST['clave']) && isset($_POST['clave_nueva'])) {
        $clave = substr(crypt($_POST['clave'], strtoupper($_POST['login'])), 3);
        $clave_nueva = substr(crypt($_POST['clave_nueva'], strtoupper($_POST['login'])), 3);

        $objetoCambioClave = new ClaseCambioClave($_POST['id_usuario'], $clave, $clave_nueva);
        $result = $objetoCambioClave->cambioClave();

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Los campos Login o Clave estan vacios');
    }

    echo $data;
}
