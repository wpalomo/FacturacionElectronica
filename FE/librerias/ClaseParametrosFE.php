<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';

/**
 * Description of ClaseParametrosFE
 *
 * @author jpablos
 */
class ClaseParametrosFE {
    public function getParametros($cci_empresa) {
        $query = "
            EXEC BIZ_FAC..SP_FE_PARAMETROS            
            @IN_CCI_EMPRESA= '$cci_empresa',
            @in_operacion = 'QX'
        ";
        
        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }
    
    public function update($parametros) {
        print_r($parametros);
        $parametrosFE = json_decode(stripslashes($parametros['parametrosFE']));
        
        print_r($parametrosFE);
        
        echo $parametrosFE;

        $cci_empresa = mssql_real_escape_string($parametrosFE['cci_empresa']);
        $dfm_fecha_inicio = mssql_real_escape_string($parametrosFE['dfm_fecha_inicio']);
        $cci_ruta_generados = mssql_real_escape_string($parametrosFE['cci_ruta_generados']);
        $cci_ruta_firmados = mssql_real_escape_string($parametrosFE['cci_ruta_firmados']);
        $cci_ruta_enviados = mssql_real_escape_string($parametrosFE['cci_ruta_enviados']);
        $cci_ruta_enviados_rechazados = mssql_real_escape_string($parametrosFE['cci_ruta_enviados_rechazados']);
        $cci_ruta_autorizados = mssql_real_escape_string($parametrosFE['cci_ruta_autorizados']);
        $cci_ruta_no_autorizados = mssql_real_escape_string($parametrosFE['cci_ruta_no_autorizados']);
        $cci_ruta_pdf = mssql_real_escape_string($parametrosFE['cci_ruta_pdf']);
        $cci_ruta_certificado = mssql_real_escape_string($parametrosFE['cci_ruta_certificado']);
        $ctx_clave_certificado = mssql_real_escape_string($parametrosFE['ctx_clave_certificado']);
        $cci_ruta_programa_fe = mssql_real_escape_string($parametrosFE['cci_ruta_programa_fe']);
        $cci_mail_default = mssql_real_escape_string($parametrosFE['cci_mail_default']);
        $cci_ruta_logo = mssql_real_escape_string($parametrosFE['cci_ruta_logo']);
        $ambiente = mssql_real_escape_string($parametros['ambiente']);

        $query = "
            EXEC BIZ_FAC..SP_FE_PARAMETROS
            @IN_CCI_EMPRESA = '$cci_empresa',
            @in_dfm_fecha_inicio = '$dfm_fecha_inicio',
            @in_cci_ruta_generados = '$cci_ruta_generados',
            @in_cci_ruta_firmados = '$cci_ruta_firmados',  
            @in_cci_ruta_enviados = '$cci_ruta_enviados', 
            @in_cci_ruta_enviados_rechazados = '$cci_ruta_enviados_rechazados', 
            @in_cci_ruta_autorizados = '$cci_ruta_autorizados', 
            @in_cci_ruta_no_autorizados = '$cci_ruta_no_autorizados', 
            @in_cci_ruta_pdf = '$cci_ruta_pdf', 
            @in_cci_ruta_certificado = '$cci_ruta_certificado', 
            @in_ctx_clave_certificado = '$ctx_clave_certificado', 
            @in_cci_ruta_programa_fe = '$cci_ruta_programa_fe', 
            @in_cci_mail_default = '$cci_mail_default', 
            @in_cci_ruta_logo = '$cci_ruta_logo', 
            @in_ambiente = '$ambiente', 
            @in_operacion = 'U'
        ";
        echo $query;
        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }
}
