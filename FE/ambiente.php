<?php

include_once 'librerias/header.php';
include_once 'librerias/ClaseAmbiente.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

switch ($action) {
    case 'getAmbientes':
        getAmbientes();
        break;
    case 'getAmbiente':
        getAmbiente();
        break;
    case 'update':
        update();
        break;
}

function getAmbientes() {

    $objetoAmbiente = new ClaseAmbiente();

    $result = $objetoAmbiente->getAmbientes();

    $data = ClaseJson::getJson($result);

    echo $data;
}

function getAmbiente() {

    $objetoAmbiente = new ClaseAmbiente();

    $result = $objetoAmbiente->getAmbiente($_POST['cod_ambiente']);

    $data = ClaseJson::getJson($result);

    echo $data;
}

function update() {
    if (isset($_POST['ambiente'])) {
        $parametros = array(
            'ambiente' => $_POST['ambiente']
        );

        $objetoAmbiente = new ClaseAmbiente();

        $result = $objetoAmbiente->update($parametros);

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}