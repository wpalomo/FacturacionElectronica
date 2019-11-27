<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';

/**
 * Description of ClaseProcesarDocumentos
 *
 * @author jpsanchez
 */
class ClaseProcesarDocumentos {

    public function getDocumentos($parametros) {
        $select = "
        SELECT f.cci_empresa, 
        f.cci_sucursal, 
        f.cci_cliente, 
        f.cno_cliprov,
        f.dfm_fecha,
        f.cci_tipocmpr, 
        f.nci_documento,
        f.id_log_fe,
        f.cci_usuario,
        f.dfx_reg_fecha,
        f.ces_fe,
        f.cci_clave_acceso
        FROM BIZ_FAC..VI_FAC_FE_DOCUMENTOS f ";

        $where = " WHERE cci_empresa != '' ";

        $selectTotalRegistros = "
            select count(*) as total_registros
            from BIZ_FAC..VI_FAC_FE_DOCUMENTOS
        ";

        $start = $parametros['start'];

        $order = 'ORDER BY ' . $parametros['sortField'] . ' ';
        if ($parametros['sortOrder'] == '-1') {
            $order = $order . ' DESC ';
        }

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

}
