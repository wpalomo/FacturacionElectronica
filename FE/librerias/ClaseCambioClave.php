<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';



include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'ClaseUtilidades.php';
include_once 'ClaseSesion.php';
include_once 'funciones.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClaseCambioClave
 *
 * @author jpsanchez
 */
class ClaseCambioClave {

    private $id_usuario;
    private $clave;
    private $clave_nueva;

    public function __construct($id_usuario, $clave, $clave_nueva) {
        $this->id_usuario = mssql_real_escape_string($id_usuario);
        $this->clave = mssql_real_escape_string($clave);
        $this->clave_nueva = mssql_real_escape_string($clave_nueva);
    }

    public function cambioClave() {
        $query = "
            EXEC SP_GEN_CAMBIO_CLAVE
            @in_id_usuario = '$this->id_usuario',
            @in_clave = '$this->clave',
            @in_clave_nueva = '$this->clave_nueva',
            @in_usuario_ing_act = '0',
            @in_operacion = 'CCL'
        ";
        
        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

}
