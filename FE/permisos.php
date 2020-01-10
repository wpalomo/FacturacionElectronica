<?php

include_once 'librerias/header.php';
include_once 'librerias/ClasePermisos.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

switch ($action) {
    case 'getMenuPerfil':
        getMenuPerfil();
        break;
}

function getMenuPerfil() {

    $objetoPermiso = new ClasePermisos();

    $result = $objetoPermiso->getMenuPerfil($_POST['id_perfil']);

    $data = ClaseJson::getJson($result);

    echo $data;
}
