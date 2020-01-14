<?php

include_once 'librerias/header.php';
include_once 'librerias/ClaseProcesarDocumentos.php';
include_once 'librerias/ClaseValidaciones.php';


$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);

switch ($action) {
    case 'getDocumentos':
        getDocumentos();
        break;
    case 'getMailsDocumento':
        getMailsDocumento();
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

function getMailsDocumento() {
    $parametros = array(
        'cci_empresa' => $_POST['cci_empresa'],
        'cci_sucursal' => $_POST['cci_sucursal'],
        'cci_tipocmpr' => $_POST['cci_tipocmpr'],
        'nci_documento' => $_POST['nci_documento']
    );

    $objetoProcesarDocumentos = new ClaseProcesarDocumentos();

    $result = $objetoProcesarDocumentos->getMailsDocumento($parametros);

    $data = ClaseJson::getJson($result);

    echo $data;
}

function generarProcesoFE() {
    $json = $_POST['json'];

    if (isset($json)) {
        $objetoProcesoFE = new ClaseProcesarDocumentos();

        $documentos = json_decode($json, true);

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

            if ($value['opcion'] == 'T' || $value['opcion'] == 'M') {
                if ($value['opcion'] == 'T') {
                    if (!is_array($result)) {
                        $result = $objetoProcesoFE->enviarMail($value);
                    }
                }


                if ($value['opcion'] == 'M') {
                    if ($value['mail']) {
                        $mail = $value['mail'];

                        if (count($mail) > 0) {
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

                            if (!is_array($result)) {
                                $result = $objetoProcesoFE->generarPDFResumido($value);
                            }

                            if (!is_array($result)) {
                                $result = $objetoProcesoFE->generarPDF($value);
                            }

                            if (!is_array($result)) {
                                $result = $objetoProcesoFE->actualizarEnviarMail2($value['cci_empresa'], $value['cci_tipocmpr'], $value['nci_documento'], 'S');
                            }

                            if (!is_array($result)) {
                                $result = $objetoProcesoFE->enviarMail($value);
                            }
                        }
                    }
                }
            }

            if (is_array($result)) {
                //echo $result['DESCRIPCION_ERROR'];
                echo ClaseJson::getMessageJson(false, $result['DESCRIPCION_ERROR']);
            } else {
                //echo $result;
                //echo 'todo ok';
                if ($value['opcion'] != 'P') {
                    if ($value['opcion'] != 'M') {
                        echo ClaseJson::getMessageJson(true, 'Proceso Generado');
                    }

                    if ($value['opcion'] == 'M') {
                        echo ClaseJson::getMessageJson(true, 'Correo enviado.');
                    }
                } else {
                    echo $result;
                }
            }
        }
    } else {
        $data = ClaseJson::getMessageJson(false, 'Error en el envio de información');
    }

    //echo $data;
    //echo ClaseJson::getMessageJson(false, $data);
}
