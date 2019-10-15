<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, X-EXAMPLE-HEADER, authorization');

include_once 'librerias/ClaseMenuFavoritos.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);
//$action = 'getMenuUsuario';

switch ($action) {
    case 'getMenuFavoritos':
        getMenuFavoritos();
        break;
    case 'updateMenuFavoritos':
        updateMenuFavoritos();
        break;
}

function getMenuFavoritos() {

    $id_usuario = $_POST['id_usuario'];



    $objetoMenuFavoritos = new ClaseMenuFavoritos();

    $result = $objetoMenuFavoritos->getMenuFavoritos($id_usuario);

    $data = ClaseJson::getJson($result);

    echo $data;
}

function updateMenuFavoritos() {
    if (isset($_POST['json'])) {

        $objetoMenuFavoritos = new ClaseMenuFavoritos();

        $result = $objetoMenuFavoritos->updateMenuFavoritos($_POST['json']);

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}
