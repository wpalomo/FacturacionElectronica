<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';

/**
 * Description of ClaseEmpresa
 *
 * @author jpsanchez
 */
class ClaseEmpresa {

    public function getEmpresas($cadenaEmpresas) {

        $query = "
            EXEC BIZ_FAC..SP_FE_EMPRESA
            @in_cadena_empresas = '$cadenaEmpresas',
            @in_operacion = 'QE'
        ";

        //echo $query;
        
        $parametros = array(
            'interfaz' => 'I',
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }
    
    public function getEmpresasNoRegistradas() {

        $query = "
            EXEC BIZ_FAC..SP_FE_EMPRESA            
            @in_operacion = 'QNR'
        ";

        $parametros = array(
            'interfaz' => 'I',
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

        public function getEmpresasLocal() {

        $query = "
            EXEC dbo.SP_GEN_EMPRESAS            
            @in_operacion = 'QE'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }
}
