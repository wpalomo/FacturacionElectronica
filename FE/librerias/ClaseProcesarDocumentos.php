<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';
include_once 'ClaseGenerarXml.php';
include_once 'ClaseFirmarFE.php';
include_once 'ClaseEnviarFE.php';
include_once 'ClaseAutorizarFE.php';
include_once 'ClaseGeneraPdfFactura.php';
include_once 'ClaseGeneraPdfNC.php';
include_once 'ClaseGeneraPdfRetencion.php';
include_once 'ClaseGeneraPdfGuia.php';

/**
 * Description of ClaseProcesarDocumentos
 *
 * @author jpsanchez
 */
class ClaseProcesarDocumentos {

    private $entorno;
    private $codDoc = '';
    private $dataEmpresas = array();
    private $dataDocumentos = array();

    public function __construct() {
        ClaseValidaciones::crearDirectorios();
    }

    public function getDocumentos($parametros) {
        $select = "
        SELECT f.cci_empresa, 
        f.cno_empresa,
        f.cci_sucursal, 
        f.cci_cliente, 
        f.cno_cliprov,
        f.dfm_fecha,
        f.cci_tipocmpr, 
        f.descripcion_cci_tipocmpr,
        f.nci_documento,
        f.id_log_fe,
        f.cci_usuario,
        --f.dfx_reg_fecha,
        f.ces_fe,
        f.descripcion_ces_fe,
        f.cci_clave_acceso,
        f.ambiente
        FROM BIZ_FAC..VI_FAC_FE_DOCUMENTOS f ";

        $where = " WHERE cci_empresa != '' ";

        $selectTotalRegistros = "
            select count(*) as total_registros
            from BIZ_FAC..VI_FAC_FE_DOCUMENTOS
        ";

        $records = json_decode(stripslashes($parametros['filters']), true);

        foreach ($records as $k => $value) {
            $valor = '';
            $tmp = '';
            foreach ($value as $key => $_value) {

                if (is_array($_value)) {
                    $tmp = $_value;
                } else {
                    if ($key == 'value') {
                        $valor = $_value;
                    }
                }

                if ($key == 'matchMode') {
                    switch ($_value) {
                        case 'startsWith':
                            $where = $where . " AND " . $k . " like '$valor%' ";
                            break;
                        case 'contains':
                            $where = $where . " AND " . $k . " like '%$valor%' ";
                            break;
                        case 'equals':
                            if (is_array($tmp)) {
                                foreach ($tmp as $k2 => $v2) {
                                    if ($k2 == 'value') {
                                        $valor = $v2;
                                    }

                                    if ($k == 'estado_usuario' && $valor == 'T') {
                                        continue;
                                    }
                                }

                                $where = $where . " AND " . $k . " = '$valor' ";
                            } else {
                                $where = $where . " AND " . $k . " = '$valor' ";
                            }
                            break;
                        case 'in':
                            if (is_array($tmp)) {
                                $cadenaIn = " IN(";
                                foreach ($tmp as $valueIn) {
                                    $cadenaIn = $cadenaIn . "'$valueIn'";

                                    if (next($tmp) == true) {
                                        $cadenaIn = $cadenaIn . ",";
                                    } else {
                                        $cadenaIn = $cadenaIn . ") ";
                                    }
                                }
                                //echo $cadenaIn;
                                $where = $where . " AND " . $k . $cadenaIn;
                            }
                            break;
                    }
                }
            }
        }

        $start = $parametros['start'];

        switch ($parametros['sortField']) {
            case 'cci_empresa':
                $order = 'ORDER BY ' . $parametros['sortField'] . ' ';
                if ($parametros['sortOrder'] == '-1') {
                    $order = $order . ' DESC ';
                }

                $order = $order . ', cci_tipocmpr, nci_documento ';
                break;
            case 'cci_tipocmpr':
                $order = 'ORDER BY ' . $parametros['sortField'] . ' ';
                if ($parametros['sortOrder'] == '-1') {
                    $order = $order . ' DESC ';
                }

                $order = $order . ', cci_empresa, nci_documento ';
                break;
            case 'nci_documento':
                $order = 'ORDER BY ' . $parametros['sortField'] . ' ';
                if ($parametros['sortOrder'] == '-1') {
                    $order = $order . ' DESC ';
                }

                $order = $order . ', cci_empresa, cci_tipocmpr ';
                break;
        }

        /*
          $order = 'ORDER BY ' . $parametros['sortField'] . ' ';
          if ($parametros['sortOrder'] == '-1') {
          $order = $order . ' DESC ';
          }
         */

        $offset = 'OFFSET ' . ($start) . ' ROWS ';
        $fetch = 'FETCH NEXT ' . $parametros['limit'] . ' ROWS ONLY';

        $queryTotalRegistros = $selectTotalRegistros . $where;
        $query = $select . $where . $order . $offset . $fetch;

        //echo $queryTotalRegistros;
        //echo $query;

        $parametros = array(
            'interfaz' => 'I',
            'query' => $queryTotalRegistros
        );

        $resultTotal = ClaseBaseDatos::query($parametros);


        if ($resultTotal['error'] == 'N') {
            $dataTotal = $resultTotal['data'];
            $totalRegistros = $dataTotal[0]['total_registros'];

            $parametros = array(
                'interfaz' => 'I',
                'query' => $query,
                'total' => $totalRegistros
            );

            $result = ClaseBaseDatos::query($parametros);

//            print_r($result);
            return $result;
        } else {
            return $resultTotal;
        }



//        $parametros = array(
//            'interfaz' => 'I',
//            'query' => $select
//        );
//
//        $result = ClaseBaseDatos::query($parametros);
//
//        return $result;
    }

    public function generarXml($parametros) {
        $this->entorno = _ENTORNO;
        $objetoGenerarXml = new ClaseGenerarXml();
        $error = 'N';

        /*
         * si el entorno es de pruebas y el ambiente de la empresa 
         * es produccion(valor de 2) entonces no se debe procesar los documentos
         * ya que se estarian autorizando en el sri documentos que 
         * estan siendo evaluados en el ambiente de pruebas
         */

        if ($parametros['ambiente'] == 2 && $this->entorno !== "PRODUCCION") {
            echo ClaseJson::getMessageJson(false, "ESTA EN AMBIENTE DE DESARROLLO Y LA EMPRESA " . $parametros['cci_empresa'] . " TIENE SETEADO EL AMBIENTE DE PRODUCCION, EN AMBIENTE DE DESARROLLO NO SE DEBERIAN PROCESAR LOS DOCUMENTOS DEL SRI, PUEDEN POR ERROR ENVIARSE DOCUMENTOS DE PRUEBA ACCIDENTALMENTE Y SER AUTORIZADOS, REVISE");
        } else {
            if (($error = $this->getEmpresas($parametros['cci_empresa'], 'QG')) == 'S') {
                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()');
            } else {
                if (($this->getDataDocumentos($parametros['cci_empresa'], $parametros['nci_documento'], $parametros['cci_tipocmpr'], 'P')) == 'S') {
                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()');
                } else {
                    if (count($this->dataDocumentos) > 0) {
                        switch ($parametros['cci_tipocmpr']) {
                            case 'FAC':
                                $this->codDoc = '01';

                                $resultPagos = $this->getPagosFactura($parametros['cci_empresa'], $parametros['cci_sucursal'], $parametros['nci_documento']);

                                if ($resultPagos == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-3');
                                } else {
                                    $dataPagos = $resultPagos;
                                }

                                $resultDetalle = $this->getDetalleFactura($parametros['cci_empresa'], $parametros['cci_sucursal'], $parametros['nci_documento']);

                                if ($resultDetalle == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-4');
                                } else {
                                    $dataDetalle = $resultDetalle;
                                }

                                $result = $objetoGenerarXml->generaXmlFactura($this->dataEmpresas, $this->dataDocumentos, $dataPagos, $dataDetalle);
                                if ($result == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $objetoGenerarXml->getErrorDB() . ' - ClaseProcesoFE - generarXml()-5');
                                }


                                $resultActualizar = $this->actualizarEstadoDocumento($objetoGenerarXml->getDataLog());
                                if ($resultActualizar == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-6');
                                }

                                break;
                            case 'NC':
                                $this->codDoc = '04';

                                $resultDetalle = $this->getDetalleNC($parametros['cci_empresa'], $parametros['cci_sucursal'], $parametros['nci_documento']);

                                if ($resultDetalle == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-7');
                                } else {
                                    $dataDetalle = $resultDetalle;
                                }

                                $result = $objetoGenerarXml->generaXmlNC($this->dataEmpresas, $this->dataDocumentos, $dataDetalle);
                                if ($result == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $objetoGenerarXml->getErrorDB() . ' - ClaseProcesoFE - generarXml()-8');
                                }

                                $resultActualizar = $this->actualizarEstadoDocumento($objetoGenerarXml->getDataLog());
                                if ($resultActualizar == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-9');
                                }
                                break;
                            case 'RET':
                                $this->codDoc = '07';

                                $resultDetalle = $this->getDetalleRetencion($parametros['cci_empresa'], $parametros['cci_sucursal'], $parametros['nci_documento']);

                                if ($resultDetalle == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-10');
                                } else {
                                    $dataDetalle = $resultDetalle;
                                }

                                $result = $objetoGenerarXml->generaXmlRetencion($this->dataEmpresas, $this->dataDocumentos, $dataDetalle);
                                if ($result == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $objetoGenerarXml->getErrorDB() . ' - ClaseProcesoFE - generarXml()-11');
                                }

                                $resultActualizar = $this->actualizarEstadoDocumento($objetoGenerarXml->getDataLog());
                                if ($resultActualizar == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-12');
                                }
                                break;
                            case 'GUI':
                                $this->codDoc = '06';

                                $resultDetalle = $this->getDetalleGuia($parametros['cci_empresa'], $parametros['cci_sucursal'], $parametros['nci_documento']);

                                if ($resultDetalle == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-13');
                                } else {
                                    $dataDetalle = $resultDetalle;
                                }

                                $result = $objetoGenerarXml->generarXmlGuia($this->dataEmpresas, $this->dataDocumentos, $dataDetalle);
                                if ($result == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $objetoGenerarXml->getErrorDB() . ' - ClaseProcesoFE - generarXml()-14');
                                }

                                $resultActualizar = $this->actualizarEstadoDocumento($objetoGenerarXml->getDataLog());
                                if ($resultActualizar == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-15');
                                }
                                break;
                        }
                    }
                }
            }
        }
    }

    public function firmarFE($parametros) {
        $objetoFirmarFE = new ClaseFirmarFE();
        $error = 'N';

        if (($error = $this->getEmpresas($parametros['cci_empresa'], 'QF')) == 'S') {
            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - firmarFE()');
        } else {

            if (($this->getDataDocumentos($parametros['cci_empresa'], $parametros['nci_documento'], $parametros['cci_tipocmpr'], 'G')) == 'S') {
                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - firmarFE()');
            } else {
                if (count($this->dataDocumentos) > 0) {
                    $objetoFirmarFE->firmar($this->dataEmpresas, $this->dataDocumentos, $parametros['cci_tipocmpr']);

                    $resultActualizar = $this->actualizarEstadoDocumento($objetoFirmarFE->getDataLog());
                    if ($resultActualizar == 'S') {
                        return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - firmarFE()');
                    }
                }
            }
        }
    }

    public function enviarFE($parametros) {
        $objetoEnviarFE = new ClaseEnviarFE();

        if (($error = $this->getEmpresas($parametros['cci_empresa'], 'QE')) == 'S') {
            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - enviarFE()');
        } else {
            if (($this->getDataDocumentos($parametros['cci_empresa'], $parametros['nci_documento'], $parametros['cci_tipocmpr'], 'F')) == 'S') {
                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - enviarFE()');
            } else {
                if (count($this->dataDocumentos) > 0) {
                    $objetoEnviarFE->enviar($this->dataEmpresas, $this->dataDocumentos, $parametros['cci_tipocmpr']);

                    $resultActualizar = $this->actualizarEstadoDocumento($objetoEnviarFE->getDataLog());
                    if ($result == 'S') {
                        return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - enviarFE()');
                    }
                }
            }
        }
    }

    public function autorizarFE($parametros) {
        $objetoAutorizarFE = new ClaseAutorizarFE();

        if (($error = $this->getEmpresas($parametros['cci_empresa'], 'QA')) == 'S') {
            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - autorizarFE()');
        } else {
            if (($this->getDataDocumentos($parametros['cci_empresa'], $parametros['nci_documento'], $parametros['cci_tipocmpr'], 'E')) == 'S') {
                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - autorizarFE()');
            } else {
                if (count($this->dataDocumentos) > 0) {
                    $objetoAutorizarFE->autorizar($this->dataEmpresas, $this->dataDocumentos, $parametros['cci_tipocmpr']);

                    $resultActualizar = $this->actualizarEstadoDocumento($objetoAutorizarFE->getDataLog());
                    if ($result == 'S') {
                        return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - autorizarFE()');
                    }
                }
            }
        }
    }

    public function generarPDF($parametros) {
        if (($error = $this->getEmpresas($parametros['cci_empresa'], 'QGR')) == 'S') {
            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-1');
        } else {
            if (($this->getDocumentosPDF($parametros['cci_empresa'], $parametros['cci_tipocmpr'], $parametros['nci_documento'])) == 'S') {
                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-2');
            } else {
                if (count($this->dataDocumentos) > 0) {
                    $resultCabecera = $this->consultaDocumentosFE($this->dataDocumentos['CCI_EMPRESA'], $this->dataDocumentos['CCI_SUCURSAL'], $this->dataDocumentos['CCI_CLAVE_ACCESO'], $parametros['cci_tipocmpr'], 'C');
                    if ($resultCabecera == 'S') {
                        return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-3');
                    } else {
                        $dataCabecera = $resultCabecera[0];
                    }

                    $resultDetalle = $this->consultaDocumentosFE($this->dataDocumentos['CCI_EMPRESA'], $this->dataDocumentos['CCI_SUCURSAL'], $this->dataDocumentos['CCI_CLAVE_ACCESO'], $parametros['cci_tipocmpr'], 'D');
                    if ($resultDetalle == 'S') {
                        return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-4');
                    } else {
                        $dataDetalle = $resultDetalle;
                    }

                    if ($valueTipos == 'FAC') {
                        $resultPagos = $this->consultaDocumentosFE($this->dataDocumentos['CCI_EMPRESA'], $this->dataDocumentos['CCI_SUCURSAL'], $this->dataDocumentos['CCI_CLAVE_ACCESO'], $parametros['cci_tipocmpr'], 'P');

                        if ($resultPagos == 'S') {
                            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-5');
                        } else {
                            $dataPagos = $resultPagos;
                        }

                        $resultVencimientos = $this->consultaDocumentosFE($this->dataDocumentos['CCI_EMPRESA'], $this->dataDocumentos['CCI_SUCURSAL'], $this->dataDocumentos['CCI_CLAVE_ACCESO'], $parametros['cci_tipocmpr'], 'V');

                        if ($resultVencimientos == 'S') {
                            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-5');
                        } else {
                            $dataVencimientos = $resultVencimientos;
                        }
                    }

                    switch ($parametros['cci_tipocmpr']) {
                        case 'FAC':
                            $objetoGeneraPdf = new ClaseGeneraPdfFactura('FAC', $dataCabecera, $dataDetalle, $dataPagos, $dataVencimientos);
                            $objetoGeneraPdf->generaPdf();
                            break;
                        case 'NC':
                            $objetoGeneraPdf = new ClaseGeneraPdfNC('NC', $dataCabecera, $dataDetalle, '', '');
                            $objetoGeneraPdf->generaPdf();
                            break;
                        case 'RET':
                            $objetoGeneraPdf = new ClaseGeneraPdfRetencion('RET', $dataCabecera, $dataDetalle, '', '');
                            $objetoGeneraPdf->generaPdf();
                            break;
                        case 'GUI':
                            $objetoGeneraPdf = new ClaseGeneraPdfGuia('GUI', $dataCabecera, $dataDetalle, '', '');
                            $objetoGeneraPdf->generaPdf();
                            break;
                    }

                    if (file_exists($dataCabecera['CCI_RUTA_PDF_COMPLETA'])) {
                        $resultActualizarRuta = $this->actualizarRutaDocumentosLog($dataCabecera['CCI_EMPRESA'], $dataCabecera['CCI_SUCURSAL'], $dataCabecera['NCI_DOCUMENTO'], $dataCabecera['CCI_RUTA_XML_COMPLETA'], $dataCabecera['CCI_RUTA_PDF_COMPLETA'], $dataCabecera['CES_FE']);

                        if ($resultActualizarRuta == 'S') {
                            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-6');
                        } else {
                            $dataDetalle = $resultDetalle;
                        }

                        $resultActualizarGenerarPDF = $this->actualizarGenerarPDF($dataCabecera['CCI_EMPRESA'], $parametros['cci_tipocmpr'], $dataCabecera['NCI_DOCUMENTO'], $dataCabecera['CES_FE'], 'N');
                        if ($resultActualizarGenerarPDF == 'S') {
                            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-8');
                        }

                        if (!copy($dataCabecera['CCI_RUTA_PDF_COMPLETA'], 'descargas/' . $this->dataDocumentos['CCI_CLAVE_ACCESO'] . '.pdf')) {
                            echo "Error al copiar archivo...\n";
                        }

//                        $resultActualizarEnviarMail = $this->actualizarEnviarMail($dataCabecera['CCI_EMPRESA'], $parametros['cci_tipocmpr'], $dataCabecera['NCI_DOCUMENTO'], $dataCabecera['CES_FE'], 'S');
//                        if ($resultActualizarEnviarMail == 'S') {
//                            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-7');
//                        }
                    } else {
                        echo ' - error al grabar el archivo' . '<br>';
                    }
                }
            }
        }
    }

    public function generarPDFResumido($parametros) {
        if (($error = $this->getEmpresas($parametros['cci_empresa'], 'QGR')) == 'S') {
            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-1');
        } else {
            //el resumido por el momento solo es para factura y nota de credito
            if ($parametros['cci_tipocmpr'] == 'FAC' || $parametros['cci_tipocmpr'] == 'NC' || $parametros['cci_tipocmpr'] == 'GUI') {
                if (($this->getDocumentosPDF($parametros['cci_empresa'], $parametros['cci_tipocmpr'], $parametros['nci_documento'])) == 'S') {
                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-2');
                } else {
                    if (count($this->dataDocumentos) > 0) {
                        $resultCabecera = $this->consultaDocumentosFE($this->dataDocumentos['CCI_EMPRESA'], $this->dataDocumentos['CCI_SUCURSAL'], $this->dataDocumentos['CCI_CLAVE_ACCESO'], $parametros['cci_tipocmpr'], 'C');
                        if ($resultCabecera == 'S') {
                            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-3');
                        } else {
                            $dataCabecera = $resultCabecera[0];
                        }

                        $dataCabecera['CCI_RUTA_PDF_COMPLETA'] = $dataCabecera['CCI_RUTA_PDF_RESUMIDO_COMPLETA'];

                        $resultDetalle = $this->consultaDocumentosFE($this->dataDocumentos['CCI_EMPRESA'], $this->dataDocumentos['CCI_SUCURSAL'], $this->dataDocumentos['CCI_CLAVE_ACCESO'], $parametros['cci_tipocmpr'], 'DR');
                        if ($resultDetalle == 'S') {
                            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-4');
                        } else {
                            $dataDetalle = $resultDetalle;
                        }

                        if (count($dataDetalle) > 0) {
                            if ($valueTipos == 'FAC') {
                                $resultPagos = $this->consultaDocumentosFE($this->dataDocumentos['CCI_EMPRESA'], $this->dataDocumentos['CCI_SUCURSAL'], $this->dataDocumentos['CCI_CLAVE_ACCESO'], $parametros['cci_tipocmpr'], 'P');

                                if ($resultPagos == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-5');
                                } else {
                                    $dataPagos = $resultPagos;
                                }

                                $resultVencimientos = $this->consultaDocumentosFE($this->dataDocumentos['CCI_EMPRESA'], $this->dataDocumentos['CCI_SUCURSAL'], $this->dataDocumentos['CCI_CLAVE_ACCESO'], $parametros['cci_tipocmpr'], 'V');

                                if ($resultVencimientos == 'S') {
                                    return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()-5');
                                } else {
                                    $dataVencimientos = $resultVencimientos;
                                }
                            }

                            switch ($parametros['cci_tipocmpr']) {
                                case 'FAC':
                                    $objetoGeneraPdf = new ClaseGeneraPdfFactura('FAC', $dataCabecera, $dataDetalle, $dataPagos, $dataVencimientos);
                                    $objetoGeneraPdf->generaPdf();
                                    break;
                                case 'NC':
                                    $objetoGeneraPdf = new ClaseGeneraPdfNC('NC', $dataCabecera, $dataDetalle, '', '');
                                    $objetoGeneraPdf->generaPdf();
                                    break;
                                case 'RET':
                                    $objetoGeneraPdf = new ClaseGeneraPdfRetencion('RET', $dataCabecera, $dataDetalle, '', '');
                                    $objetoGeneraPdf->generaPdf();
                                    break;
                                case 'GUI':
                                    $objetoGeneraPdf = new ClaseGeneraPdfGuia('GUI', $dataCabecera, $dataDetalle, '', '');
                                    $objetoGeneraPdf->generaPdf();
                                    break;
                            }

                            if (file_exists($dataCabecera['CCI_RUTA_PDF_COMPLETA'])) {
                                echo 'PDF-RESUMIDO ';
                                if (!copy($dataCabecera['CCI_RUTA_PDF_COMPLETA'], 'descargas/' . $this->dataDocumentos['CCI_CLAVE_ACCESO'] . '.pdf')) {
                                    echo "Error al copiar archivo...\n";
                                }
                            } else {
                                echo ' - error al grabar el archivo' . '<br>';
                            }
                        }
                    }
                }
            }
        }
    }

    public function enviarMail($parametros) {
        $objetoMail = new ClaseMail();

        $idListaCorreo = 1;

        $resultDocumentosMail = $this->getDocumentosMail();
        if ($resultDocumentosMail == 'S') {
            //return $this->errorDB;
            return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - enviarMail()');
        } else {
            $dataDocumentosMail = $resultDocumentosMail;
        }

        foreach ($dataDocumentosMail as $keyDoc => $valueDoc) {
            $dest = '';

            //$resultListaCorreo = $this->listaCorreosDefault($valueDoc['CCI_TIPOCMPR']);
            $resultListaCorreo = $this->listaCorreosDefault($valueDoc['CCI_EMPRESA'], $valueDoc['CCI_SUCURSAL'], $valueDoc['CCI_TIPOCMPR']);
            $imagen = $valueDoc['CCI_RUTA_LOGO'];

            if ($resultListaCorreo == 'S') {
                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - enviarMail()');
            } else {
                $dataListaCorreo = $resultListaCorreo;
            }

            echo 'ENVIANDO EMAIL: ' . $valueDoc['CCI_TIPOCMPR'] . ': ' . $valueDoc['CCI_EMPRESA'] . ' - ' . $valueDoc['NCI_DOCUMENTO'] . ' ';

            //$asunto = utf8_decode('Comprobante Electrónico ' . $valueDoc['CCI_TIPOCMPR'] . ' ' . $valueDoc['NCI_DOCUMENTO_COMPLETO'] . ' ' . $valueDoc['CNO_CLIPROV']);
            $asunto = utf8_decode('Comprobante Electrónico ' . $valueDoc['CCI_SUCURSAL'] . '-' . $valueDoc['CCI_TIPOCMPR'] . ' ' . $valueDoc['NCI_DOCUMENTO_COMPLETO'] . ' ' . $valueDoc['CNO_CLIPROV']);

            $path_parts = pathinfo($valueDoc['CCI_RUTA_LOGO']);

            $file = $path_parts['filename'] . '.' . $path_parts['extension'];
            $rutaImagen = 'imagenes/' . $file;
            $cid = 'cid:' . $path_parts['filename'];

            $mensaje = '<table width="600" align="center">
                            <tr>
                                <td align="center">"Estimado(a) <b> ' . $valueDoc['CNO_CLIPROV'] . ' </b>"<br><br><br></td>                
                            </tr>
                            <tr>
                                <td align="center">Adjunto el comprobante electrónico del documento <b>' . $valueDoc['CCI_TIPOCMPR'] . ' ' . $valueDoc['NCI_DOCUMENTO_COMPLETO'] . '</b><br><br><br> </td>                
                            </tr>                            
                            <tr>
                                <td align="center">
                                    <img src="' . $cid . '" height="100" width="297">
                                </td>
                            </tr>
                        </table>
                       ';

            $mensaje = utf8_decode($mensaje);


            $destinatarios = array(
                array(
                    'a' => 1,
                    'mail' => $valueDoc['CTX_MAIL'],
                    'nombre' => utf8_decode($valueDoc['CNO_CLIPROV'])
                )
            );

            if ($valueDoc['CCI_TIPOCMPR'] == 'FAC' || $valueDoc['CCI_TIPOCMPR'] == 'NC') {
                if ($valueDoc['CTX_MAIL_AUX1'] != '') {
                    $dest1 = array(
                        'a' => 1,
                        'mail' => $valueDoc['CTX_MAIL_AUX1'],
                        'nombre' => $valueDoc['CTX_MAIL_AUX1']
                    );

                    array_push($destinatarios, $dest1);
                }

                if ($valueDoc['CTX_MAIL_AUX2'] != '') {
                    $dest2 = array(
                        'a' => 1,
                        'mail' => $valueDoc['CTX_MAIL_AUX2'],
                        'nombre' => $valueDoc['CTX_MAIL_AUX2']
                    );

                    array_push($destinatarios, $dest2);
                }
            }

            //por el momento solo se envia el correo del usuario que genero el documento en guias de remision
            if ($valueDoc['CCI_TIPOCMPR'] == 'GUI') {
                $usuariosArray = array(
                    'a' => 1,
                    'mail' => $valueDoc['CTX_MAIL_USUARIO'],
                    'nombre' => utf8_decode($valueDoc['NOMBRE_USUARIO_MAIL'])
                );

                array_push($destinatarios, $usuariosArray);
            }

            foreach ($dataListaCorreo as $key => $value) {
                if ($value['PARA']) {
                    $dest = array(
                        'a' => $value['PARA'],
                        'mail' => $value['EMAIL'],
                        'nombre' => utf8_decode($value['NOMBRE'])
                    );
                }

                if ($value['CC']) {
                    $dest = array(
                        'cc' => $value['CC'],
                        'mail' => $value['EMAIL'],
                        'nombre' => utf8_decode($value['NOMBRE'])
                    );
                }

                if ($value['CCO']) {
                    $dest = array(
                        'cco' => $value['CCO'],
                        'mail' => $value['EMAIL'],
                        'nombre' => utf8_decode($value['NOMBRE'])
                    );
                }

                array_push($destinatarios, $dest);
            }

            $adjuntos = array(
                array(
                    'ruta' => $valueDoc['CCI_RUTA_DOCUMENTO_XML']
                ),
                array(
                    'ruta' => $valueDoc['CCI_RUTA_DOCUMENTO_PDF']
                )
            );

            if (file_exists($valueDoc['CCI_RUTA_DOCUMENTO_RESUMIDO_PDF'])) {
                $adj = array(
                    'ruta' => $valueDoc['CCI_RUTA_DOCUMENTO_RESUMIDO_PDF']
                );

                array_push($adjuntos, $adj);
            }

            $resp = $objetoMail->send($destinatarios, $asunto, $mensaje, $adjuntos, $rutaImagen, $path_parts['filename']);

            echo $resp['error'] . ' - ' . $resp['mensaje'];

            $resultIngresarMailLog = $this->ingresarMailLog($valueDoc['CCI_EMPRESA'], $valueDoc['CCI_TIPOCMPR'], $valueDoc['NCI_DOCUMENTO'], $valueDoc['CTX_MAIL'], $resp['enviado'], strip_tags($resp['mensaje']));

            if ($resultIngresarMailLog == 'S') {
                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - enviarMail()');
            }

            $resultActualizarEnviarMail = $this->actualizarEnviarMail($valueDoc['CCI_EMPRESA'], $valueDoc['CCI_TIPOCMPR'], $valueDoc['NCI_DOCUMENTO'], $valueDoc['CES_FE'], 'N');
            if ($resultActualizarEnviarMail == 'S') {
                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarPDF()');
            }

            //si hubo un error en el envio del mail (generalmente se da porque)
            //la direccion del cliente no es valida, entonces se vuelve a enviar
            //a todos menos al cliente, adicional se envia un archivo en donde
            //esta la descripcion del error en el envio del mail


            if ($resp['error'] == 'S' && $resp['enviado'] == 'N') {
                $dataErrorMail = array(
                    'CCI_EMPRESA' => $valueDoc['CCI_EMPRESA'],
                    'CCI_CLIPROV' => $valueDoc['CCI_CLIPROV'],
                    'CNO_CLIPROV' => $valueDoc['CNO_CLIPROV'],
                    'CCI_TIPOCMPR' => $valueDoc['CCI_TIPOCMPR'],
                    'NCI_DOCUMENTO' => $valueDoc['NCI_DOCUMENTO'],
                    'CTX_MAIL' => $valueDoc['CTX_MAIL'],
                    'MENSAJE' => strip_tags($resp['mensaje'])
                );

                if ($valueDoc['CTX_MAIL_AUX1'] != '') {
                    if (($keyMail = array_search($valueDoc['CTX_MAIL_AUX1'], $destinatarios)) !== false) {
                        unset($destinatarios[$keyMail]);
                    }
                }

                if ($valueDoc['CTX_MAIL_AUX2'] != '') {
                    if (($keyMail = array_search($valueDoc['CTX_MAIL_AUX2'], $destinatarios)) !== false) {
                        unset($destinatarios[$keyMail]);
                    }
                }

                unset($destinatarios[0]);

                $resp = $objetoMail->send($destinatarios, $asunto, $mensaje, $adjuntos, $rutaImagen, $path_parts['filename']);

                echo $resp['error'] . ' - ' . $resp['mensaje'];

                $objetoGenerarExcel = new ClaseGenerarExcel();

                $rutaExcel = $objetoGenerarExcel->generarExcelErroresMail($dataErrorMail);

                if (file_exists($rutaExcel)) {
                    echo '<br>ENVIANDO EMAIL ERRORES: ' . $valueDoc['CCI_TIPOCMPR'] . ' ';

                    $asunto = utf8_decode('Errores en el envio de Mail en Facturación Electrónica ');

                    $mensaje = 'Se adjunta un archivo en excel con errores que se presentaron al enviar el mail en el proceso de facturación electrónica. ';

                    $mensaje = utf8_decode($mensaje);

                    $adjuntos = array(
                        array(
                            'ruta' => $rutaExcel
                        )
                    );

                    $resp = $objetoMail->send($destinatarios, $asunto, $mensaje, $adjuntos, '', '');

                    echo $resp['error'] . ' - ' . $resp['mensaje'];
                } else {
                    echo 'no existe';
                }
            }

            echo '<hr>';
        }
    }

    private function getEmpresas($cci_empresa, $operacion) {
        $query = "
            EXEC BIZ_FAC..SP_FE_PARAMETROS
            @in_cci_empresa = '$cci_empresa',
            @in_operacion = '$operacion'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            $this->dataEmpresas = $result['data'][0];
        }
    }

    private function getDataDocumentos($cci_empresa, $nci_documento, $tipoDoc, $estadoFE) {
        $operacion = '';

        switch ($tipoDoc) {
            case 'FAC':
                $operacion = 'QFX';
                if ($estadoFE == 'P') {
                    $operacion = 'QFP';
                }
                break;
            case 'NC':
                $operacion = 'QNX';
                if ($estadoFE == 'P') {
                    $operacion = 'QNP';
                }
                break;
            case 'RET':
                $operacion = 'QRX';
                if ($estadoFE == 'P') {
                    $operacion = 'QRP';
                }
                break;
            case 'GUI':
                $operacion = 'QGX';
                if ($estadoFE == 'P') {
                    $operacion = 'QGP';
                }
                break;
        }

        $query = "
            EXEC BIZ_FAC..SP_FE_DOCUMENTOS_PROCESAR
            @IN_CCI_EMPRESA = '$cci_empresa',
            @IN_NCI_DOCUMENTO = '$nci_documento',
            @IN_CES_FE = '$estadoFE',    
            @IN_OPERACION = '$operacion'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            $this->dataDocumentos = $result['data'][0];
        }
    }

    private function getDocumentosPDF($cci_empresa, $cci_tipocmpr, $nci_documento) {
        $operacion = '';
//        $cadena = "@IN_NCI_DOCUMENTO = '$this->nci_documento', ";
//
//        if ($this->nci_documento == '') {
//            $cadena = "@IN_NCI_DOCUMENTO = NULL, ";
//        }

        switch ($cci_tipocmpr) {
            case 'FAC':
                $operacion = 'QFD';
                break;
            case 'NC':
                $operacion = 'QND';
                break;
            case 'RET':
                $operacion = 'QRD';
                break;
            case 'GUI':
                $operacion = 'QGD';
                break;
        }

        $query = "
            EXEC BIZ_FAC..SP_FE_DOCUMENTOS_PROCESAR
            @IN_CCI_EMPRESA = '$cci_empresa',
            @IN_NCI_DOCUMENTO = '$nci_documento',
            @IN_OPERACION = '$operacion'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            $this->dataDocumentos = $result['data'][0];
        }
    }

    private function getPagosFactura($cci_empresa, $cci_sucursal, $nci_documento) {
        $query = "
            EXEC BIZ_FAC..SP_FE_PAGOS_DOCUMENTO
            @IN_CCI_EMPRESA = '$cci_empresa',  
            @IN_CCI_SUCURSAL = '$cci_sucursal',    
            @IN_CCI_TIPOCMPR = 'FAC',
            @IN_NCI_DOCUMENTO = '$nci_documento',
            @IN_OPERACION = 'QP'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

    private function getDetalleFactura($cci_empresa, $cci_sucursal, $nci_documento) {
        $query = "
            EXEC BIZ_FAC..SP_FE_DETALLE_DOCUMENTOS
            @IN_CCI_EMPRESA = '$cci_empresa',  
            @IN_CCI_SUCURSAL = '$cci_sucursal',    
            --@IN_CCI_TIPOCMPR = 'FAC',
            @IN_NCI_DOCUMENTO = '$nci_documento',
            @IN_OPERACION = 'QDF'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

    private function getDetalleNC($cci_empresa, $cci_sucursal, $nci_documento) {
        $query = "
            EXEC BIZ_FAC..SP_FE_DETALLE_DOCUMENTOS
            @IN_CCI_EMPRESA = '$cci_empresa',  
            @IN_CCI_SUCURSAL = '$cci_sucursal',    
            --@IN_CCI_TIPOCMPR = 'NC',
            @IN_NCI_DOCUMENTO = '$nci_documento',
            @IN_OPERACION = 'QDN'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

    private function getDetalleRetencion($cci_empresa, $cci_sucursal, $nci_documento) {
        $query = "
            EXEC BIZ_FAC..SP_FE_DETALLE_DOCUMENTOS
            @IN_CCI_EMPRESA = '$cci_empresa',  
            @IN_CCI_SUCURSAL = '$cci_sucursal',                
            @IN_NCI_DOCUMENTO = '$nci_documento',
            @IN_OPERACION = 'QDR'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

    private function getDetalleGuia($cci_empresa, $cci_sucursal, $nci_documento) {
        $query = "
            EXEC BIZ_FAC..SP_FE_DETALLE_DOCUMENTOS
            @IN_CCI_EMPRESA = '$cci_empresa',  
            @IN_CCI_SUCURSAL = '$cci_sucursal',                
            @IN_NCI_DOCUMENTO = '$nci_documento',
            @IN_OPERACION = 'QDG'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

    public function actualizarEstadoDocumento($dataLog) {
        $cci_empresa = $dataLog['CCI_EMPRESA'];
        $cci_sucursal = $dataLog['CCI_SUCURSAL'];
        $cci_cliente = $dataLog['CCI_CLIENTE'];
        $cci_tipocmpr = $dataLog['CCI_TIPOCMPR'];
        $nci_documento = $dataLog['NCI_DOCUMENTO'];
        $claveAcceso = $dataLog['CCI_CLAVE_ACCESO'];
        $proceso = $dataLog['CCI_PROCESO'];
        $estado = $dataLog['CES_FE'];
        $estadoWS = $dataLog['ESTADO_WS'];
        $numeroAutorizacionWS = $dataLog['NUMERO_AUTORIZACION_WS'];
        $fechaAutorizacionWS = $dataLog['FECHA_AUTORIZACION_WS'];
        $ambienteWS = $dataLog['AMBIENTE_WS'];
        $identificadorWS = $dataLog['IDENTIFICADOR_WS'];
        $mensajeWS = $dataLog['MENSAJE_WS'];
        $informacionAdicionalWS = utf8_decode($this->caracter($dataLog['INFORMACION_ADICIONAL_WS']));
        $tipoWS = $dataLog['TIPO_WS'];

        $query = "
            EXEC BIZ_FAC..SP_FE_LOG
            @IN_CCI_EMPRESA = '$cci_empresa',
            @IN_CCI_SUCURSAL = '$cci_sucursal',
            @IN_CCI_CLIENTE = '$cci_cliente',
            @IN_CCI_TIPOCMPR = '$cci_tipocmpr', 
            @IN_NCI_DOCUMENTO = '$nci_documento',
            @IN_CCI_CLAVE_ACCESO = '$claveAcceso',
            @IN_CCI_PROCESO = '$proceso',     
            @IN_CES_FE = '$estado',
            @ESTADO_WS = '$estadoWS',
            @NUMERO_AUTORIZACION_WS = '$numeroAutorizacionWS',  
            @FECHA_AUTORIZACION_WS = '$fechaAutorizacionWS',
            @AMBIENTE_WS = '$ambienteWS',
            @IDENTIFICADOR_WS = '$identificadorWS',
            @MENSAJE_WS = '$mensajeWS',
            @INFORMACION_ADICIONAL_WS = '$informacionAdicionalWS',
            @TIPO_WS = '$tipoWS',   
            @IN_OPERACION = 'UEL'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

    /**
     * 
     * @param type $cci_empresa: Codigo de la Empresa
     * @param type $cci_sucursal: Codigo de la sucursal
     * @param type $cci_clave_acceso: Codigo de la clave de acceso de facturacion electronica del documento
     * @param type $tipo: tipo de documento: FAC-Factura, NC-Nota de Credito, RET-Retencion, GUI-Guia de Retencion 
     * @param type $operacion: C-Informacion de Cabecera, D-Informacion de Detalle, P-Informacion de Pagos
     * @return string
     */
    private function consultaDocumentosFE($cci_empresa, $cci_sucursal, $cci_clave_acceso, $tipo, $operacion) {
        $query = "
            EXEC BIZ_FAC..SP_FE_CONSULTA_DOCUMENTOS
            @IN_CCI_EMPRESA = '$cci_empresa',
            @IN_CCI_SUCURSAL = '$cci_sucursal',
            @IN_CCI_CLAVE_ACCESO = '$cci_clave_acceso',    
            @IN_TIPO = '$tipo',
            @IN_OPERACION = '$operacion'
        ";

        $parametros = array(
            'query' => $query
        );

        //echo $query;

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

    private function actualizarRutaDocumentosLog($cci_empresa, $cci_sucursal, $nci_documento, $cci_ruta_xml, $cci_ruta_pdf, $ces_fe) {
        $query = "
            EXEC BIZ_FAC..SP_FE_LOG
            @IN_CCI_EMPRESA = '$cci_empresa',
            @IN_CCI_SUCURSAL = '$cci_sucursal',
            @IN_NCI_DOCUMENTO = '$nci_documento',    
            @IN_CCI_RUTA_DOCUMENTO_XML = '$cci_ruta_xml',
            @IN_CCI_RUTA_DOCUMENTO_PDF = '$cci_ruta_pdf',
            @IN_CES_FE = '$ces_fe',
            @IN_OPERACION = 'URD'                	
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

    private function actualizarGenerarPDF($cci_empresa, $cci_tipocmpr, $nci_documento, $ces_fe, $generar) {
        $query = "
            EXEC BIZ_FAC..SP_FE_DOCUMENTOS_PROCESAR
            @IN_CCI_EMPRESA = '$cci_empresa',
            --@IN_CCI_SUCURSAL = '$cci_sucursal',
            @IN_CCI_TIPOCMPR = '$cci_tipocmpr',   
            @IN_NCI_DOCUMENTO = '$nci_documento', 
            @IN_CES_FE = '$ces_fe',    
            @IN_GENERAR_PDF = '$generar',    
            @IN_OPERACION = 'UPD'                	
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
            return $result['data'];
        }
    }

    private function actualizarEnviarMail($cci_empresa, $cci_tipocmpr, $nci_documento, $ces_fe, $enviar) {
        $query = "
            EXEC BIZ_FAC..SP_FE_DOCUMENTOS_PROCESAR
            @IN_CCI_EMPRESA = '$cci_empresa',
            --@IN_CCI_SUCURSAL = '$cci_sucursal',
            @IN_CCI_TIPOCMPR = '$cci_tipocmpr',   
            @IN_NCI_DOCUMENTO = '$nci_documento', 
            @IN_CES_FE = '$ces_fe',    
            @IN_ENVIAR_MAIL = '$enviar',    
            @IN_OPERACION = 'UEM'                	
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

    private function caracter($string) {
        return str_replace("'", '', $string);
    }

    private function getDocumentosMail() {

        $query = "
            EXEC BIZ_FAC..SP_FE_MAIL                
            @IN_OPERACION = 'DOC'                	
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            return $this->errorDB = ClaseJson::getJson($result);
        } else {
            return $dataListaCorreoDetalle = $result['data'];
        }
    }

    public function habilitarDocumentoRechazado($cci_empresa, $cci_tipocmpr, $nci_documento, $ces_fe) {
        $query = "
            EXEC BIZ_FAC..SP_FE_DOCUMENTOS_PROCESAR
            @IN_CCI_EMPRESA = '$cci_empresa',
            --@IN_CCI_SUCURSAL = '$cci_sucursal',
            @IN_CCI_TIPOCMPR = '$cci_tipocmpr',   
            @IN_NCI_DOCUMENTO = '$nci_documento',                 
            @IN_CES_FE = '$ces_fe',    
            @IN_OPERACION = 'HDR'                	
        ";
        echo $query;
        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

    public function actualizarGenerarPDF2($cci_empresa, $cci_tipocmpr, $nci_documento, $generar) {
        $query = "
            EXEC BIZ_FAC..SP_FE_DOCUMENTOS_PROCESAR
            @IN_CCI_EMPRESA = '$cci_empresa',
            --@IN_CCI_SUCURSAL = '$cci_sucursal',
            @IN_CCI_TIPOCMPR = '$cci_tipocmpr',   
            @IN_NCI_DOCUMENTO = '$nci_documento',                 
            @IN_GENERAR_PDF = '$generar',    
            @IN_OPERACION = 'UP2'                	
        ";
        echo $query;
        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            return $result['data'];
        }
    }

}
