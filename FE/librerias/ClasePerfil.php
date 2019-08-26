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
            select *, descripcion_perfil as descripcion_perfil2
            from VW_PERFILES
        ";

        $where = " WHERE id_perfil > 0 ";

        $selectTotalRegistros = "
            select count(*) as total_registros
            from VW_PERFILES
        ";

        $records = json_decode(stripslashes($parametros['filters']), true);

        //print_r($records);

        foreach ($records as $key => $val) {
            //echo 'KEY IS:' . $key . '<br/>';
            $field = $key;
            foreach ($records[$key] as $_key => $_val) {
                //echo 'KEY IS:' . $_key . '<br/>';
                //echo 'VALUE IS: ' . $_val . '<br/>';


                if ($_key == 'value') {
                    $valor = $_val;
                }

                if ($_key == 'matchMode') {
                    if ($key == 'estado_perfil' && $valor == 'T') {
                        continue;
                    } else {
                        switch ($_val) {
                            case 'startsWith':
                                $where = $where . " AND " . $key . " like '$valor%' ";
                                break;
                            case 'contains':
                                $where = $where . " AND " . $key . " like '%$valor%' ";
                                break;
                            case 'equals':
                                $where = $where . " AND " . $key . " = '$valor' ";
                                break;
                            case 'in':
                                if (is_array($valor)) {
                                    $cadenaIn = " IN(";
                                    foreach ($valor as $valueIn) {
                                        $cadenaIn = $cadenaIn . "'$valueIn'";

                                        if (next($valor) == true) {
                                            $cadenaIn = $cadenaIn . ",";
                                        } else {
                                            $cadenaIn = $cadenaIn . ") ";
                                        }
                                    }
                                    //echo $cadenaIn;
                                    $where = $where . " AND " . $key . $cadenaIn;
                                }
                                break;
                        }
                    }
                }




//                if ($_key == 'value') {
//                    if ($key == 'estado_perfil' && $_val == 'T') {
//                        continue;
//                    } else {
//                        $where = $where . " AND " . $key . " = '$_val' ";
//                    }
//                }
            }
        }


        $start = $parametros['start'];
//        if ($start == 0) {
//            $start = 1;
//        }

        $order = 'ORDER BY ' . $parametros['sortField'] . ' ';
        if ($parametros['sortOrder'] == '-1') {
            $order = $order . ' DESC ';
        }

        //$offset = 'OFFSET ' . (($start - 1) * $parametros['limit']) . ' ROWS ';
        $offset = 'OFFSET ' . ($start) . ' ROWS ';
        $fetch = 'FETCH NEXT ' . $parametros['limit'] . ' ROWS ONLY';


        $queryTotalRegistros = $selectTotalRegistros . $where;
        $query = $select . $where . $order . $offset . $fetch;

//        echo $queryTotalRegistros;
//        echo $query;
//        $query = "
//            EXEC SP_GEN_PERFILES            
//            @in_operacion = 'Q'
//        ";

        $parametros = array(
            'query' => $queryTotalRegistros
        );

        $resultTotal = ClaseBaseDatos::query($parametros);

//        print_r($resultTotal);

        if ($resultTotal['error'] == 'N') {
            $dataTotal = $resultTotal['data'];
            $totalRegistros = $dataTotal[0]['total_registros'];

            $parametros = array(
                'query' => $query,
                'total' => $totalRegistros
            );

            $result = ClaseBaseDatos::query($parametros);

//            print_r($result);
            return $result;
        } else {
            return $resultTotal;
        }
    }

    public function insert() {
        
    }

    public function update() {
        
    }

}
