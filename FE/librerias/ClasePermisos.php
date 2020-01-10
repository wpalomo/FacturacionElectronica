<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';

/**
 * Description of ClasePermisos
 *
 * @author jpsanchez
 */
class ClasePermisos {

    public function getMenuPerfil($id_perfil) {        
        $id_perfil = mssql_real_escape_string($id_perfil);

        $query = "
            EXEC SP_GEN_PERMISOS
            @in_id_perfil = '$id_perfil',
            @in_operacion = 'QOP'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

}
