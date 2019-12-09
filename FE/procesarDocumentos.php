<?php

include_once 'librerias/header.php';
include_once 'librerias/ClaseProcesarDocumentos.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

$action = 'generarProcesoFE';



switch ($action) {
    case 'getDocumentos':
        getDocumentos();
        break;
    case 'generarProcesoFE':
        generarProcesoFE();
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

function generarProcesoFE() {
    echo 'generarProcesoFE';
    $json = '{"cci_empresa": "008", "nci_documento": "10010016111", "opcion": "P"}';

    var_dump(json_decode($json));

    //if (isset($_POST['json'])) {
    if (isset($json)) {
        $documentos = json_decode($json, true);

        print_r($documentos);

        foreach ($documentos as $key => $value) {
            echo $value;
        }
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}
