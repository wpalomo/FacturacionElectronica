<?php

include_once 'librerias/header.php';
include_once 'librerias/ClaseUsuario.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);
$tipo = '';

switch ($action) {
    case 'getUsuarios':
        getUsuarios();
        break;
    case 'insert':
        $tipo = 'I';
        insert();
        break;
    case 'update':
        $tipo = 'U';
        update();
        break;
    case 'delete':
        $tipo = 'D';
        delete();
        break;
    case 'uploadImagen':
        uploadImagen();
        break;
}

//print_r($_POST['myfile']);
//echo $_POST['myfile'];
//print_r($_FILES);
//echo $_FILES;

function getUsuarios() {

    $parametros = array(
        'start' => $_POST['start'],
        'limit' => $_POST['limit'],
        'sortField' => isset($_POST['sortField']) ? $_POST['sortField'] : 'id_usuario',
        'sortOrder' => isset($_POST['sortOrder']) ? $_POST['sortOrder'] : '1',
        'filters' => $_POST['filters'],
    );

    $objetoUsuario = new ClaseUsuario();

    $result = $objetoUsuario->getUsuarios($parametros);

    $data = ClaseJson::getJson($result);

    echo $data;
}

function insert() {
    if (isset($_POST['usuario'])) {
        $parametros = array(
            'usuario' => $_POST['usuario'],
            'tipo' => $tipo
        );

        $objetoUsuario = new ClaseUsuario();

        $result = $objetoUsuario->insert($parametros);

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}

function update() {
    if (isset($_POST['usuario'])) {
        $parametros = array(
            'usuario' => $_POST['usuario'],
            'tipo' => $tipo
        );

        $objetoUsuario = new ClaseUsuario();

        $result = $objetoUsuario->update($parametros);

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}

function delete() {
    if (isset($_POST['usuario'])) {
        $parametros = array(
            'usuario' => $_POST['usuario'],
            'tipo' => $tipo
        );

        $objetoUsuario = new ClaseUsuario();

        $result = $objetoUsuario->delete($parametros);

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}

function uploadImagen() {
    if (isset($_FILES)) {
        print_r($_FILES);

        $objetoUsuario = new ClaseUsuario();

        $result = $objetoUsuario->uploadImagen($_FILES, $_POST['login']);

        $data = ClaseJson::getJson($result);

        echo $data;
    }
}
