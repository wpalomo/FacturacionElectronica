<?php

include_once 'XMLSerializer.php';

/**
 * Description of ClaseEnviarFE
 *
 * @author jpsanchez
 */
ini_set("soap.wsdl_cache_enabled", "0");

class ClaseEnviarFE {

    private $ambiente;
    private $dataLog = array();

    public function enviar($dataEmpresa, $dataDocumento, $cci_tipocmpr) {
        $this->ambiente = $dataEmpresa['AMBIENTE'];
        $auxRuta = $cci_tipocmpr;

        $resultAmbiente = $this->getDataAmbiente();

        if ($resultAmbiente == 'S') {
            return 'S';
        } else {
            $dataAmbiente = $resultAmbiente;
        }

        $rutaFirmados = $dataEmpresa['CCI_RUTA_FIRMADOS'] . $auxRuta . '\\';
        $rutaEnviados = $dataEmpresa['CCI_RUTA_ENVIADOS'] . $auxRuta . '\\';
        $rutaEnviadosRechazados = $dataEmpresa['CCI_RUTA_ENVIADOS_RECHAZADOS'] . $auxRuta . '\\';
        $direccionWSEnvio = $dataAmbiente['CWS_RECEPCION'];

        $cci_empresa = $dataDocumento['CCI_EMPRESA'];
        $cci_sucursal = $dataDocumento['CCI_SUCURSAL'];
        $cci_cliente = $dataDocumento['CCI_CLIENTE'];
        $nci_documento = $dataDocumento['NCI_DOCUMENTO'];
        $claveAcceso = $dataDocumento['CCI_CLAVE_ACCESO'];
        $rutaFirmadosCompleta = $rutaFirmados . $claveAcceso . '.xml';
        $rutaEnviadosCompleta = $rutaEnviados . $claveAcceso . '.xml';
        $rutaEnviadosRechazadosCompleta = $rutaEnviadosRechazados . $claveAcceso . '.xml';

        echo 'ENVIANDO XML ' . $cci_tipocmpr . ': ' . $cci_empresa . ' - ' . $nci_documento . ' ';

        if (file_exists($rutaFirmadosCompleta)) {
            try {
                $client = new SoapClient($direccionWSEnvio);
                $xml = file_get_contents($rutaFirmadosCompleta);
                $response = $client->validarComprobante(["xml" => $xml]);



                $estadoWS = isset($response->RespuestaRecepcionComprobante->estado) ? $response->RespuestaRecepcionComprobante->estado : '';
                $identificadorWS = isset($response->RespuestaRecepcionComprobante->comprobantes->comprobante->mensajes->mensaje->identificador) ? $response->RespuestaRecepcionComprobante->comprobantes->comprobante->mensajes->mensaje->identificador : '';
                $mensajeWS = isset($response->RespuestaRecepcionComprobante->comprobantes->comprobante->mensajes->mensaje->mensaje) ? $response->RespuestaRecepcionComprobante->comprobantes->comprobante->mensajes->mensaje->mensaje : '';
                $informacionAdicionalWS = isset($response->RespuestaRecepcionComprobante->comprobantes->comprobante->mensajes->mensaje->informacionAdicional) ? $response->RespuestaRecepcionComprobante->comprobantes->comprobante->mensajes->mensaje->informacionAdicional : '';
                $tipoWS = isset($response->RespuestaRecepcionComprobante->comprobantes->comprobante->mensajes->mensaje->tipo) ? $response->RespuestaRecepcionComprobante->comprobantes->comprobante->mensajes->mensaje->tipo : '';

                $numeroAutorizacionWS = '';
                $fechaAutorizacionWS = '';
                $ambienteWS = '';

                echo $estadoWS . ' ' . $identificadorWS . ' ' . $mensaje . ' ' . $informacionAdicional . $tipo . '<br>';

                if ($estadoWS == 'RECIBIDA') {
                    copy($rutaFirmadosCompleta, $rutaEnviadosCompleta);
                    $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, $cci_tipocmpr, $nci_documento, $claveAcceso, 'ENVIAR', 'E', $estadoWS, $identificadorWS, $numeroAutorizacionWS, $fechaAutorizacionWS, $ambienteWS, $mensajeWS, $informacionAdicionalWS, $tipoWS);
                } else {
                    $objetoXml = new XMLSerializer();
                    $xmlAux = $objetoXml->generateValidXmlFromArray($response);

                    $dom = new DOMDocument();
                    $dom->preserveWhiteSpace = FALSE;
                    $dom->loadXML($xmlAux);

                    $dom->save('xmlAux.xml');

                    $str = file_get_contents('xmlAux.xml');
                    $str = str_replace('<?xml version="1.0" encoding="UTF-8"?>', "", $str);
                    $str = str_replace("<nodes>", "", $str);
                    $str = str_replace("</nodes>", "", $str);
                    $str = str_replace("<RespuestaRecepcionComprobante>", "", $str);
                    $str = str_replace("</RespuestaRecepcionComprobante>", "", $str);

                    $str = '<ns2:respuestaSolicitud xmlns:ns2="http://ec.gob.sri.ws.recepcion">' . $str . '</ns2:respuestaSolicitud>';

                    echo $mensajeWS . '-' . utf8_decode($informacionAdicionalWS) . '<br>';
                    copy($rutaFirmadosCompleta, $rutaEnviadosRechazadosCompleta);

                    $xml = file_get_contents($rutaEnviadosRechazadosCompleta);

                    switch ($cci_tipocmpr) {
                        case 'FAC':
                            $tipoDoc = "</factura>";
                            break;
                        case 'NC':
                            $tipoDoc = "</notaCredito>";
                            break;
                        case 'RET':
                            $tipoDoc = "</comprobanteRetencion>";
                            break;
                        case 'GUI':
                            $tipoDoc = "</guiaRemision>";
                            break;
                    }

                    $xml = str_replace($tipoDoc, "", $xml);
                    $xml = $xml . $str;
                    $xml = $xml . $tipoDoc;
                    file_put_contents($rutaEnviadosRechazadosCompleta, $xml);

                    $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, $cci_tipocmpr, $nci_documento, $claveAcceso, 'ENVIAR', 'R', $estadoWS, $identificadorWS, $numeroAutorizacionWS, $fechaAutorizacionWS, $ambienteWS, $mensajeWS, $informacionAdicionalWS, $tipoWS); //                  
                }
            } catch (Exception $e) {
                //echo 'vardump';
                //var_dump($e);
                //print_r($e);
                echo $e->faultstring;
            }
        } else {
            echo 'archivo no existe';
        }

        echo '<hr>';
    }

    private function getDataAmbiente() {
        $query = "
            EXEC BIZ_FAC..SP_FE_AMBIENTE
            @IN_COD_AMBIENTE = '$this->ambiente',
            @in_operacion = 'QE'
        ";

        //echo $query;

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
