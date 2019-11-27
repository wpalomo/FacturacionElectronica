<?php

include_once 'librerias/header.php';
include_once 'librerias/ClaseProcesarDocumentos.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

$action = 'getDocumentos';

switch ($action) {
    case 'getDocumentos':
        getDocumentos();
        break;
}

function getDocumentos() {
    $parametros = array(
        'start' => $_POST['start'],
        'limit' => $_POST['limit'],
        'sortField' => isset($_POST['sortField']) ? $_POST['sortField'] : 'cci_empresa',
        'sortOrder' => isset($_POST['sortOrder']) ? $_POST['sortOrder'] : '1',
        'filters' => $_POST['filters'],
    );

    $objetoProcesarDocumentos = new ClaseProcesarDocumentos();

    $result = $objetoProcesarDocumentos->getDocumentos($parametros);

    $data = ClaseJson::getJson($result);

    echo $data;
}
