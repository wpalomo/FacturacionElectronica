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

    public function getEmpresas() {

        $query = "
            EXEC BIZ_FAC..SP_FE_EMPRESA            
            @in_operacion = 'QE'
        ";

        $parametros = array(
            'interfaz' => 'I',
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

}
