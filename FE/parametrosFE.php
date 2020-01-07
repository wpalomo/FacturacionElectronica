<?php

include_once 'librerias/header.php';
include_once 'librerias/ClaseParametrosFE.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

switch ($action) {
    case 'getParametro':
        getParametro();
        break;
    case 'update':
        update();
        break;
}

function getParametro() {

    $objetoParametro = new ClaseParametrosFE();

    $result = $objetoParametro->getParametros($_POST['cci_empresa']);

    $data = ClaseJson::getJson($result);

    echo $data;
}

function update() {
    if (isset($_POST['parametrosFE'])) {
        $parametros = array(
            'parametrosFE' => $_POST['parametrosFE']
        );

        $objetoParametro = new ClaseParametrosFE();
        print_r($parametros);
        $result = $objetoParametro->update($parametros);

        $data = ClaseJson::getJson($result);
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}
