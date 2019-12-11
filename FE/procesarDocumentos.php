<?php

include_once 'librerias/header.php';
include_once 'librerias/ClaseProcesarDocumentos.php';
include_once 'librerias/ClaseValidaciones.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

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
    //echo 'generarProcesoFE';
    //$json = '[{"cci_empresa": "008", "nci_documento": "10010015467", "ambiente": "1", "opcion": "P"}]';
    $json = $_POST['json'];


    //var_dump(json_decode($json));
    //if (isset($_POST['json'])) {
    if (isset($json)) {
        $objetoProcesoFE = new ClaseProcesarDocumentos();

        $documentos = json_decode($json, true);

        //print_r($documentos);
        $result = '';

        foreach ($documentos as $key => $value) {
            if (!is_array($result)) {
                $result = $objetoProcesoFE->generarXml($value);
            }

            if (!is_array($result)) {
                $result = $objetoProcesoFE->firmarFE($value);
            }

            if (!is_array($result)) {
                $result = $objetoProcesoFE->enviarFE($value);
            }

            if (!is_array($result)) {
                $result = $objetoProcesoFE->autorizarFE($value);
            }

            if (!is_array($result)) {
                $result = $objetoProcesoFE->generarPDFResumido($value);
            }

//            if (!is_array($result)) {
//                $result = $objetoProcesoFE->generarPDF($value);
//            }

            if (is_array($result)) {
                echo $result['DESCRIPCION_ERROR'];
            } else {
                echo $result;
            }
        }
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    echo $data;
}
