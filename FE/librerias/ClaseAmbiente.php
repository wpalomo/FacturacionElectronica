<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';

/**
 * Description of ClaseAmbiente
 *
 * @author jpablos
 */
class ClaseAmbiente {

    public function getAmbientes() {
        $query = "
            EXEC BIZ_FAC..SP_FE_AMBIENTE            
            @in_operacion = 'Q'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    public function getAmbiente($cod_ambiente) {
        $query = "
            EXEC BIZ_FAC..SP_FE_AMBIENTE            
            @IN_COD_AMBIENTE= '$cod_ambiente',
            @in_operacion = 'QX'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

    public function update($parametros) {
        $ambiente = json_decode(stripslashes($parametros['ambiente']), true);

        $cod_ambiente = mssql_real_escape_string($ambiente['cod_ambiente']);
        $cws_recepcion = mssql_real_escape_string($ambiente['cws_recepcion']);
        $cws_autorizacion = mssql_real_escape_string($ambiente['cws_autorizacion']);

        $query = "
            EXEC BIZ_FAC..SP_FE_AMBIENTE
            @IN_COD_AMBIENTE = '$cod_ambiente',
            @IN_CWS_RECEPCION = '$cws_recepcion',
            @IN_CWS_AUTORIZACION = '$cws_autorizacion',              
            @in_operacion = 'U'
        ";
        
        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

}
