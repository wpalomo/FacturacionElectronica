<?php

include_once 'XMLSerializer.php';

/**
 * Description of ClaseAutorizarFE
 *
 * @author jpsanchez
 */
ini_set("soap.wsdl_cache_enabled", "0");

class ClaseAutorizarFE {

    private $ambiente;
    private $dataLog = array();

    public function autorizar($dataEmpresa, $dataDocumento, $cci_tipocmpr) {
        $this->ambiente = $dataEmpresa['AMBIENTE'];
        $auxRuta = $cci_tipocmpr;

        $resultAmbiente = $this->getDataAmbiente();

        if ($resultAmbiente == 'S') {
            return 'S';
        } else {
            $dataAmbiente = $resultAmbiente;
        }

        $rutaEnviados = $dataEmpresa['CCI_RUTA_ENVIADOS'] . $auxRuta . '\\';
        $rutaAutorizados = $dataEmpresa['CCI_RUTA_AUTORIZADOS'] . $auxRuta . '\\';
        $rutaNoAutorizados = $dataEmpresa['CCI_RUTA_NO_AUTORIZADOS'] . $auxRuta . '\\';
        $direccionWSAutorizacion = $dataAmbiente['CWS_AUTORIZACION'];

        $cci_empresa = $dataDocumento['CCI_EMPRESA'];
        $cci_sucursal = $dataDocumento['CCI_SUCURSAL'];
        $cci_cliente = $dataDocumento['CCI_CLIENTE'];
        $nci_documento = $dataDocumento['NCI_DOCUMENTO'];
        $claveAcceso = $dataDocumento['CCI_CLAVE_ACCESO'];
        $rutaEnviadosCompleta = $rutaEnviados . $claveAcceso . '.xml';
        $rutaAutorizadosCompleta = $rutaAutorizados . $claveAcceso . '.xml';
        $rutaAutorizadosCompletaAux = $rutaAutorizados . $claveAcceso . '_a.xml';
        $rutaNoAutorizadosCompleta = $rutaNoAutorizados . $claveAcceso . '.xml';
        $rutaNoAutorizadosCompletaAux = $rutaNoAutorizados . $claveAcceso . '_a.xml';

        //echo 'AUTORIZANDO XML ' . $cci_tipocmpr . ': ' . $cci_empresa . ' - ' . $nci_documento . ' ';

        if (file_exists($rutaEnviadosCompleta)) {
            $objetoXml = new XMLSerializer();

            try {
                $client = new SoapClient($direccionWSAutorizacion);
                $response = $client->autorizacionComprobante(["claveAccesoComprobante" => $claveAcceso]);

                //print_r($response);
                //echo '<br>-------------------------------------------------<br>';
                //$array = json_decode(json_encode($response), true);
                //print_r($array);

                $estadoWS = isset($response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado) ? $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado : '';
                $numeroAutorizacionWS = isset($response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion) ? $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion : '';
                $fechaAutorizacionWS = isset($response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion) ? $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion : '';
                $ambienteWS = isset($response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente) ? $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente : '';
                $identificadorWS = isset($response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->mensajes->mensaje->identificador) ? $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->mensajes->mensaje->identificador : '';
                $mensajeWS = isset($response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->mensajes->mensaje->mensaje) ? $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->mensajes->mensaje->mensaje : '';
                $informacionAdicionalWS = isset($response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->mensajes->mensaje->informacionAdicional) ? $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->mensajes->mensaje->informacionAdicional : '';
                $tipoWS = isset($response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->mensajes->mensaje->tipo) ? $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->mensajes->mensaje->tipo : '';

                if (strpos($ambienteWS, 'PRODUCC') !== false) {
                    $ambienteWS = 'PRODUCCIÓN';
                }

                $xml = $objetoXml->generateValidXmlFromObj($response);
                $dom = new DOMDocument('1.0', 'UTF-8');
                $dom->preserveWhiteSpace = FALSE;
                $dom->loadXML($xml);

                if ($estadoWS == 'AUTORIZADO') {
                    //echo 'AUTORIZADO ';
                    //echo $estadoWS . '<br>';
                    //echo $numeroAutorizacionWS . '<br>';
                    //echo $fechaAutorizacionWS . '<br>';
                    //echo $identificadorWS . '<br>';
                    //echo $mensajeWS . '<br>';
                    //echo $informacionAdicionalWS . '<br>';
                    //echo $tipoWS . '<br>';

                    copy($rutaEnviadosCompleta, $rutaAutorizadosCompleta);


                    $dom->save($rutaAutorizadosCompletaAux);

                    $str = file_get_contents($rutaAutorizadosCompletaAux);
                    $str = str_replace("<nodes>", "", $str);
                    $str = str_replace("</nodes>", "", $str);
                    $str = str_replace("&lt;", "<", $str);
                    $str = str_replace("&gt;", ">", $str);
                    $str = str_replace("<comprobante><?xml", "<comprobante><![CDATA[<?xml", $str);

                    switch ($cci_tipocmpr) {
                        case 'FAC':
                            $str = str_replace("</factura></comprobante>", "</factura>]]></comprobante>", $str);
                            break;
                        case 'NC':
                            $str = str_replace("</notaCredito></comprobante>", "</notaCredito>]]></comprobante>", $str);
                            break;
                        case 'RET':
                            $str = str_replace("</comprobanteRetencion></comprobante>", "</comprobanteRetencion>]]></comprobante>", $str);
                            break;
                        case 'GUI':
                            $str = str_replace("</guiaRemision></comprobante>", "</guiaRemision>]]></comprobante>", $str);
                            break;
                    }

                    file_put_contents($rutaAutorizadosCompletaAux, $str);

                    $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, $cci_tipocmpr, $nci_documento, $claveAcceso, 'AUTORIZAR', 'A', $estadoWS, $identificadorWS, $numeroAutorizacionWS, $fechaAutorizacionWS, $ambienteWS, $mensajeWS, $informacionAdicionalWS, $tipoWS);
                } else {
                    echo 'RECHAZADO ' . $mensajeWS . '-' . utf8_decode($informacionAdicionalWS) . '<br>';
                    //echo $estadoWS . '<br>';$cci_empresa, $cci_sucursal, $cci_cliente, $cci_tipocmpr, $nci_documento, $claveAcceso, $proceso, $estado, $estadoWS = '', $identificadorWS = '', $numeroAutorizacionWS = '', $fechaAutorizacionWS = '', $ambienteWS = '', $mensajeWS = '', $informacionAdicionalWS = '', $tipoWS = ''
                    //echo $numeroAutorizacionWS . '<br>';
                    //echo $fechaAutorizacionWS . '<br>';
                    //echo $identificadorWS . '<br>';
                    //echo $mensajeWS . '<br>';
                    //echo $informacionAdicionalWS . '<br>';
                    //echo $tipoWS . '<br>';

                    copy($rutaEnviadosCompleta, $rutaNoAutorizadosCompleta);

                    $dom->save($rutaNoAutorizadosCompletaAux);

                    $str = file_get_contents($rutaNoAutorizadosCompletaAux);
                    $str = str_replace("<nodes>", "", $str);
                    $str = str_replace("</nodes>", "", $str);
                    $str = str_replace("&lt;", "<", $str);
                    $str = str_replace("&gt;", ">", $str);
                    $str = str_replace("<comprobante><?xml", "<comprobante><![CDATA[<?xml", $str);

                    switch ($cci_tipocmpr) {
                        case 'FAC':
                            $str = str_replace("</factura></comprobante>", "</factura>]]></comprobante>", $str);
                            break;
                        case 'NC':
                            $str = str_replace("</notaCredito></comprobante>", "</notaCredito>]]></comprobante>", $str);
                            break;
                        case 'RET':
                            $str = str_replace("</comprobanteRetencion></comprobante>", "</comprobanteRetencion>]]></comprobante>", $str);
                            break;
                        case 'GUI':
                            $str = str_replace("</guiaRemision></comprobante>", "</guiaRemision>]]></comprobante>", $str);
                            break;
                    }

                    file_put_contents($rutaNoAutorizadosCompletaAux, $str);

                    $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, $cci_tipocmpr, $nci_documento, $claveAcceso, 'AUTORIZAR', 'R', $estadoWS, $identificadorWS, $numeroAutorizacionWS, $fechaAutorizacionWS, $ambienteWS, $mensajeWS, $informacionAdicionalWS, $tipoWS);
                }

                //echo $estadoWS . ' ' . $numeroAutorizacionWS . ' ' . $fechaAutorizacionWS . ' ' . $identificadorWS . ' ' . $mensajeWS . ' ' . $informacionAdicionalWS . ' ' . $tipoWS;
            } catch (Exception $e) {
                var_dump($e);
            }
        }

        //echo '<hr>';
    }

    private function getDataAmbiente() {
        $query = "
            EXEC BIZ_FAC..SP_FE_AMBIENTE
            @IN_COD_AMBIENTE = '$this->ambiente',
            @in_operacion = 'QA'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'][0];
        }
    }

    public function getDataLog() {
        return $this->dataLog;
    }

    private function setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, $cci_tipocmpr, $nci_documento, $claveAcceso, $proceso, $estado, $estadoWS = '', $identificadorWS = '', $numeroAutorizacionWS = '', $fechaAutorizacionWS = '', $ambienteWS = '', $mensajeWS = '', $informacionAdicionalWS = '', $tipoWS = '') {
        $this->dataLog = array(
            'CCI_EMPRESA' => $cci_empresa,
            'CCI_SUCURSAL' => $cci_sucursal,
            'CCI_CLIENTE' => $cci_cliente,
            'CCI_TIPOCMPR' => $cci_tipocmpr,
            'NCI_DOCUMENTO' => $nci_documento,
            'CCI_CLAVE_ACCESO' => $claveAcceso,
            'CCI_PROCESO' => $proceso,
            'CES_FE' => $estado,
            'ESTADO_WS' => $estadoWS,
            'NUMERO_AUTORIZACION_WS' => $numeroAutorizacionWS,
            'FECHA_AUTORIZACION_WS' => $fechaAutorizacionWS,
            'AMBIENTE_WS' => $ambienteWS,
            'IDENTIFICADOR_WS' => $identificadorWS,
            'MENSAJE_WS' => $mensajeWS,
            'INFORMACION_ADICIONAL_WS' => $informacionAdicionalWS,
            'TIPO_WS' => $tipoWS,
        );
    }

}
