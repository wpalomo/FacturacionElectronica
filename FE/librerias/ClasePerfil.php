<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';

/**
 * Description of ClasePerfil
 *
 * @author jpsanchez
 */
class ClasePerfil {

    private $id_perfil;
    private $descripcion_perfil;
    private $estado_perfil;

    public function __construct($id_perfil, $descripcion_perfil, $estado_perfil) {
        $this->id_perfil = mssql_real_escape_string($id_perfil);
        $this->descripcion_perfil = mssql_real_escape_string($descripcion_perfil);
        $this->estado_perfil = mssql_real_escape_string($estado_perfil);
    }

    public function getPerfiles() {
        $query = "
            EXEC SP_GEN_PERFILES            
            @in_operacion = 'Q'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

}
