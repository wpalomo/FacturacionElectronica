<?php

include_once 'config.inc.php';
include_once 'ClaseBaseDatos.php';
include_once 'ClaseJson.php';
include_once 'funciones.php';

/**
 * Description of ClaseMenuFavoritos
 *
 * @author jpablos
 */
class ClaseMenuFavoritos {

    public function getMenuFavoritos($id_usuario) {
        $id_usuario = mssql_real_escape_string($id_usuario);

        $query = "
            EXEC SP_GEN_MENU_FAVORITOS
            @in_id_usuario = '$id_usuario',             
            @in_operacion = 'QMF'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }
    
    public function getMenuFavoritosUsuario($id_usuario) {
        $id_usuario = mssql_real_escape_string($id_usuario);

        $query = "
            EXEC SP_GEN_MENU_FAVORITOS
            @in_id_usuario = '$id_usuario',             
            @in_operacion = 'QF2'
        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }
    

    public function updateMenuFavoritos($json) {
        $ob = json_decode($json);

        if ($ob != null) {
            $query = "
                EXEC SP_GEN_MENU_FAVORITOS
                @in_json = '$json',
                @in_operacion = 'I'
            ";

            $parametros = array(
                'query' => $query
            );

            $result = ClaseBaseDatos::query($parametros);

            return $result;
        } else {
            return ClaseJson::getMessageJson(false, 'Error en el envio de información en el archivo JSON');
        }
    }

}
