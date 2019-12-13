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
        //$mail = $documentos[0]['mail'];
        //echo '<hr>';
        //print_r($mail);
        //die();
        //$mail =
        //print_r($documentos);
        $result = '';

        foreach ($documentos as $key => $value) {
            if ($value['opcion'] == 'T') {
                if (!is_array($result)) {
                    $result = $objetoProcesoFE->generarXml($value);
                }
            }

            if ($value['opcion'] == 'T') {
                if (!is_array($result)) {
                    $result = $objetoProcesoFE->firmarFE($value);
                }
            }

            if ($value['opcion'] == 'T') {
                if (!is_array($result)) {
                    $result = $objetoProcesoFE->enviarFE($value);
                }
            }

            if ($value['opcion'] == 'T') {
                if (!is_array($result)) {
                    $result = $objetoProcesoFE->autorizarFE($value);
                }
            }

            if ($value['opcion'] == 'T' || $value['opcion'] == 'P') {
                if ($value['opcion'] == 'P') {
                    if ($value['ces_fe'] == 'P' || $value['ces_fe'] == 'R') {
                        if ($value['ces_fe'] == 'R') {
                            if (!is_array($result)) {
                                $result = $objetoProcesoFE->habilitarDocumentoRechazado($value['cci_empresa'], $value['cci_tipocmpr'], $value['nci_documento'], 'P');
                            }
                        }

                        if (!is_array($result)) {
                            $result = $objetoProcesoFE->generarXml($value);
                        }
                    }

                    if (!is_array($result)) {
                        $result = $objetoProcesoFE->actualizarGenerarPDF2($value['cci_empresa'], $value['cci_tipocmpr'], $value['nci_documento'], 'S');
                    }
                }

                if (!is_array($result)) {
                    $result = $objetoProcesoFE->generarPDFResumido($value);
                }

                if (!is_array($result)) {
                    $result = $objetoProcesoFE->generarPDF($value);
                }
            }

            if ($value['mail']) {
                $mail = $value['mail'];
                
                if (count($mail) > 0) {
                    
                }

                //print_r($mail);
            }


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
