<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, X-EXAMPLE-HEADER, authorization');

include_once 'librerias/ClaseMenu.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);
$action = 'getMenuUsuario';

switch ($action) {
    case 'getMenu':
        getMenu();
        break;
    case 'getMenuFavoritos':
        getMenuFavoritos();
        break;
    case 'getMenuUsuario':
        getMenuUsuario();
        break;
}

function getMenu() {
//    if (isset($_POST['id_usuario'])) {
    //$objetoMenu = new ClaseMenu($_POST['id_usuario']);
    $objetoMenu = new ClaseMenu(1);
    $result = $objetoMenu->getMenu();

    if ($result['error'] == 'N') {
        //$data = ClaseJson::getJson($result);
    }


//    } else {
//        $data = ClaseJson::getMessageJson(false, 'Los campos Login o Clave estan vacios');
//    }

    echo $result;
}

function getMenuFavoritos() {
    //    if (isset($_POST['id_usuario'])) {
    //$objetoMenu = new ClaseMenu($_POST['id_usuario']);
    $objetoMenu = new ClaseMenu(1);
    $result = $objetoMenu->getMenuFavoritos();

    if ($result['error'] == 'N') {
        //$data = ClaseJson::getJson($result);
    }


//    } else {
//        $data = ClaseJson::getMessageJson(false, 'Los campos Login o Clave estan vacios');
//    }

    echo $result;
}

function getMenuUsuario() {
    //    if (isset($_POST['id_usuario'])) {
    //$objetoMenu = new ClaseMenu($_POST['id_usuario']);
    $objetoMenu = new ClaseMenu(1);
    $result = $objetoMenu->getMenuUsuario();

    if ($result['error'] == 'N') {
        //$data = ClaseJson::getJson($result);
    }


//    } else {
//        $data = ClaseJson::getMessageJson(false, 'Los campos Login o Clave estan vacios');
//    }

    echo $result;
}
