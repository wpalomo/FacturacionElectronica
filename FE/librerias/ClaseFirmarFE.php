<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClaseFirmarFE
 *
 * @author jpsanchez
 */
class ClaseFirmarFE {

    private $dataLog = array();

    public function firmar($dataEmpresa, $dataDocumento, $cci_tipocmpr) {
        $auxRuta = $cci_tipocmpr;

        $rutaGenerados = $dataEmpresa['CCI_RUTA_GENERADOS'] . $auxRuta . '\\';
        $rutaFirmados = $dataEmpresa['CCI_RUTA_FIRMADOS'] . $auxRuta . '\\';
        $rutaCertificado = $dataEmpresa['CCI_RUTA_CERTIFICADO'];
        $claveCertificado = $dataEmpresa['CTX_CLAVE_CERTIFICADO'];
        $rutaProgramaFE = $dataEmpresa['CCI_RUTA_PROGRAMA_FE'];

        $cci_empresa = $dataDocumento['CCI_EMPRESA'];
        $cci_sucursal = $dataDocumento['CCI_SUCURSAL'];
        $cci_cliente = $dataDocumento['CCI_CLIENTE'];
        $nci_documento = $dataDocumento['NCI_DOCUMENTO'];
        $claveAcceso = $dataDocumento['CCI_CLAVE_ACCESO'];
        $rutaGeneradosCompleta = $rutaGenerados . $claveAcceso . '.xml';
        $rutaFirmadosCompleta = $rutaFirmados . $claveAcceso . '.xml';

        echo 'FIRMANDO XML ' . $cci_tipocmpr . ': ' . $cci_empresa . ' - ' . $nci_documento . '<br>';
        echo $rutaGeneradosCompleta;

        if (file_exists($rutaGeneradosCompleta)) {
            $output = array();

            exec('java -jar ' . $rutaProgramaFE . ' ' . $rutaCertificado . ' ' . $claveCertificado . ' ' . $rutaGeneradosCompleta . ' ' . $rutaFirmados . ' ' . $claveAcceso . '.xml', $output);

            if (file_exists($rutaFirmadosCompleta)) {
                print_r($output);

                $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, $cci_tipocmpr, $nci_documento, $claveAcceso, 'FIRMAR', 'F');
            } else {
                $mensajeWS = 'ERROR EN GENERACION DE FIRMA(ERROR-LOCAL)';
                $informacionAdicionalWS = $output[4];
                $this->setDataLog($cci_empresa, $cci_sucursal, $cci_cliente, $cci_tipocmpr, $nci_documento, $claveAcceso, 'FIRMAR', 'R', '', '', '', '', '', $mensajeWS, $informacionAdicionalWS, '');
                echo '<br>' . $mensajeWS . ' - ' . $informacionAdicionalWS;
            }
        } else {
            echo 'archivo no existe';
        }
        echo '<hr>';
    }

    function getDataLog() {
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
