<?php

include_once 'librerias/header.php';
include_once 'librerias/ClasePermisos.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

switch ($action) {
    case 'getMenuPerfil':
        getMenuPerfil();
        break;
    case 'getMenuPerfilIdMenu':
        getMenuPerfilIdMenu();
        break;
    case 'updateMenuPerfil':
        updateMenuPerfil();
        break;
}

function getMenuPerfil() {

    $objetoPermiso = new ClasePermisos();

    $result = $objetoPermiso->getMenuPerfil($_POST['id_perfil']);

    //$data = ClaseJson::getJson($result);

    echo $result;
}

function getMenuPerfilIdMenu() {

    $objetoPermiso = new ClasePermisos();

    $result = $objetoPermiso->getMenuPerfilIdMenu($_POST['id_perfil']);

    $data = ClaseJson::getJson($result);

    echo $data;
}

function updateMenuPerfil() {
    if (isset($_POST['json'])) {
        $objetoPermiso = new ClasePermisos();

        $result = $objetoPermiso->updateMenuPerfil($_POST['id_perfil'], $_POST['json']);

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}
