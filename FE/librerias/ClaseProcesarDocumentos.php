<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';
include_once 'ClaseGenerarXml.php';

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

                            echo '<hr>';
                            print_r($this->dataEmpresas);
                            echo '<hr>';
                            print_r($this->dataDocumentos);
                            echo '<hr>';
                            print_r($dataPagos);
                            echo '<hr>';
                            print_r($dataDetalle);
                            echo '<hr>';

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

                            $resultDetalle = $this->getDetalleNC($valueDocumentos['CCI_EMPRESA'], $valueDocumentos['CCI_SUCURSAL'], $valueDocumentos['NCI_DOCUMENTO']);

                            if ($resultDetalle == 'S') {
                                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-7');
                            } else {
                                $dataDetalle = $resultDetalle;
                            }

                            $result = $objetoGenerarXml->generaXmlNC($valueEmpresa, $valueDocumentos, $dataDetalle);
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

                            $resultDetalle = $this->getDetalleRetencion($valueDocumentos['CCI_EMPRESA'], $valueDocumentos['CCI_SUCURSAL'], $valueDocumentos['NCI_DOCUMENTO']);

                            if ($resultDetalle == 'S') {
                                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-10');
                            } else {
                                $dataDetalle = $resultDetalle;
                            }

                            $result = $objetoGenerarXml->generaXmlRetencion($valueEmpresa, $valueDocumentos, $dataDetalle);
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

                            $resultDetalle = $this->getDetalleGuia($valueDocumentos['CCI_EMPRESA'], $valueDocumentos['CCI_SUCURSAL'], $valueDocumentos['NCI_DOCUMENTO']);

                            if ($resultDetalle == 'S') {
                                return array('ERROR' => 'S', 'DESCRIPCION_ERROR' => $this->errorDB . ' - ClaseProcesoFE - generarXml()-13');
                            } else {
                                $dataDetalle = $resultDetalle;
                            }

                            $result = $objetoGenerarXml->generarXmlGuia($valueEmpresa, $valueDocumentos, $dataDetalle);
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

    private function getEmpresas($cci_empresa, $operacion) {
        $query = "
            EXEC BIZ_FAC..SP_FE_PARAMETROS
            @in_cci_empresa = '$cci_empresa',
            @in_operacion = '$operacion'
        ";

        $parametros = array(
            'query' => $query
        );

        echo $query;

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            $this->dataEmpresas = $result['data'];
        }
    }

    private function getDataDocumentos($cci_empresa, $nci_documento, $tipoDoc, $estadoFE) {
        $operacion = '';

        if ($this->nci_documento == '') {
            $cadena = "@IN_NCI_DOCUMENTO = NULL, ";
        }

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
        echo $query;
        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        if ($result['error'] != 'N') {
            $this->errorDB = ClaseJson::getJson($result);
            return 'S';
        } else {
            $this->dataDocumentos = $result['data'];
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

}
