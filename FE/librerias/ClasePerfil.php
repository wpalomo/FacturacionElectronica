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

//    public function __construct($id_perfil, $descripcion_perfil, $estado_perfil) {
//        $this->id_perfil = mssql_real_escape_string($id_perfil);
//        $this->descripcion_perfil = mssql_real_escape_string($descripcion_perfil);
//        $this->estado_perfil = mssql_real_escape_string($estado_perfil);
//    }

    public function getPerfiles($parametros) {

//        print_r($parametros);

        $select = "
            select *
            from VW_PERFILES
        ";

        $where = " WHERE id_perfil > 0 ";

        $records = json_decode(stripslashes($parametros['filters']), true);

        foreach ($records as $key => $val) {
            //echo 'KEY IS:' . $key . '<br/>';
            foreach ($records[$key] as $_key => $_val) {
                //echo 'KEY IS:' . $_key . '<br/>';
                //echo 'VALUE IS: ' . $_val . '<br/>';

                if ($_key == 'value') {
                    $where = $where . " AND " . $key . " = '$_val' ";
                }
            }
        }



        $order = 'ORDER BY ' . $parametros['sortField'] . ' ';
        if ($parametros['sortOrder'] == '-1') {
            $order = $order . ' DESC ';
        }

        $offset = 'OFFSET ' . ($parametros['start'] * $parametros['limit']) . ' ROWS ';
        $fetch = 'FETCH NEXT ' . $parametros['limit'] . ' ROWS ONLY';


        $query = $select . $where . $order . $offset . $fetch;

//        echo $query;

//        $query = "
//            EXEC SP_GEN_PERFILES            
//            @in_operacion = 'Q'
//        ";

        $parametros = array(
            'query' => $query
        );

        $result = ClaseBaseDatos::query($parametros);

        return $result;
    }

}
